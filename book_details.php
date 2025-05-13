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

$stmt = $pdo->prepare('SELECT IsAvailable FROM BookCopy WHERE Book_ID = ? AND CopyNumber = 1');
$stmt->execute([$book_id]);
$copy = $stmt->fetch(PDO::FETCH_ASSOC);
$is_available = $copy ? $copy['IsAvailable'] : false;

$stmt = $pdo->prepare('
    SELECT r.ID
    FROM Reservation r
    JOIN ReservationBookCopy rbc ON r.ID = rbc.Reservation_ID
    JOIN ReservationMember rm ON r.ID = rm.Reservation_ID
    WHERE rbc.Book_ID = ? AND rm.Member_ID = ? AND r.Status = ?
');
$stmt->execute([$book_id, $_SESSION['user']['ID'], 'pending']);
$has_pending = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <span class="text-xl font-bold">Library</span>
            <div>
                <span><?php echo htmlspecialchars($_SESSION['user']['Name']) . ' (' . $_SESSION['user']['Role'] . ')'; ?></span>
                <a href="reservations.php" class="ml-4">My Reservations</a>
                <a href="profile.php" class="ml-4">Profile</a>
                <a href="logout.php" class="ml-4 bg-red-500 px-3 py-1 rounded">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4"><?php echo htmlspecialchars($book['Title']); ?></h1>
        <p><strong>Author(s):</strong> <?php echo htmlspecialchars($book['Authors']); ?></p>
        <p><strong>Genre(s):</strong> <?php echo htmlspecialchars($book['Genres']); ?></p>
        <p><strong>ISBN:</strong> <?php echo htmlspecialchars($book['ISBN']); ?></p>
        <p><strong>Publication Year:</strong> <?php echo htmlspecialchars($book['PublicationYear']); ?></p>
        <p><strong>Availability:</strong> <?php echo $is_available ? 'Available' : 'Not Available'; ?></p>
        <?php if ($_SESSION['user']['Role'] === 'member' && $is_available && !$has_pending): ?>
            <button id="reserve-btn-<?php echo $book['ID']; ?>" onclick="reserveBook(<?php echo $book['ID']; ?>)" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">Reserve</button>
        <?php elseif ($has_pending): ?>
            <span class="text-yellow-500 font-bold">Reservation pending</span>
        <?php endif; ?>
    </div>
</body>
</html>