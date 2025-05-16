<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$book_id = (int)($_GET['id'] ?? 0);
if ($book_id <= 0) {
    header('Location: books.php');
    exit;
}

// Fetch book details
$stmt = $pdo->prepare('
    SELECT b.ID, b.Title, b.ISBN, b.PublicationYear,
           GROUP_CONCAT(a.Name) AS Authors,
           GROUP_CONCAT(g.Name) AS Genres
    FROM Book b
    LEFT JOIN WrittenBy wb ON b.ID = wb.Book_ID
    LEFT JOIN Author a ON wb.Author_ID = a.ID
    LEFT JOIN BookGenre bg ON b.ID = bg.Book_ID
    LEFT JOIN Genre g ON bg.Genre_ID = g.ID
    WHERE b.ID = ?
    GROUP BY b.ID
');
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header('Location: books.php?error=Book+not+found');
    exit;
}

// Count available copies
$stmt = $pdo->prepare('SELECT COUNT(*) AS available_copies FROM BookCopy WHERE Book_ID = ? AND IsAvailable = TRUE');
$stmt->execute([$book_id]);
$available_copies = $stmt->fetchColumn();

// Count user's pending reservations for this book
$stmt = $pdo->prepare('
    SELECT COUNT(*) AS pending_count
    FROM Reservation r
    JOIN ReservationBookCopy rbc ON r.ID = rbc.Reservation_ID
    JOIN ReservationMember rm ON r.ID = rm.Reservation_ID
    WHERE rbc.Book_ID = ? AND rm.Member_ID = ? AND r.Status = "pending"
');
$stmt->execute([$book_id, $_SESSION['user']['ID']]);
$pending_count = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/scripts.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'navbar_member.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($book['Title']); ?></h1>
        <p><strong>Author(s):</strong> <?php echo htmlspecialchars($book['Authors']); ?></p>
        <p><strong>Genre(s):</strong> <?php echo htmlspecialchars($book['Genres']); ?></p>
        <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['ISBN']); ?></p>
        <p><strong>Publication Year:</strong> <?php echo htmlspecialchars($book['PublicationYear']); ?></p>
        <p><strong>Available Copies:</strong> <?php echo $available_copies; ?></p>
        <?php if ($pending_count > 0): ?>
            <p><strong>Your Reservations:</strong> You have <?php echo $pending_count; ?> pending reservation<?php echo $pending_count > 1 ? 's' : ''; ?> for this book.</p>
        <?php endif; ?>
        <?php if ($_SESSION['user']['Role'] === 'member' && $available_copies > 0): ?>
            <button id="reserve-btn-<?php echo $book['ID']; ?>" onclick="reserveBook(<?php echo $book['ID']; ?>)" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Reserve</button>
        <?php else: ?>
            <span class="text-red-500 font-bold">No copies available</span>
        <?php endif; ?>
    </div>
</body>
</html>