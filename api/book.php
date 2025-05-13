<?php
header('Content-Type: application/json');
require '../config.php';

$bookId = (int)($_GET['id'] ?? 0);

if ($bookId <= 0) {
    echo json_encode(['error' => 'Invalid book ID']);
    exit;
}

$stmt = $pdo->prepare('
    SELECT b.ID, b.Title, b.ISBN, b.PublicationYear,
           GROUP_CONCAT(DISTINCT a.Name) AS Authors,
           GROUP_CONCAT(DISTINCT g.Name) AS Genre
    FROM Book b
    LEFT JOIN WrittenBy wb ON b.ID = wb.Book_ID
    LEFT JOIN Author a ON wb.Author_ID = a.ID
    LEFT JOIN BookGenre bg ON b.ID = bg.Book_ID
    LEFT JOIN Genre g ON bg.Genre_ID = g.ID
    WHERE b.ID = ?
    GROUP BY b.ID, b.Title, b.ISBN, b.PublicationYear
');
$stmt->execute([$bookId]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if ($book) {
    echo json_encode($book);
} else {
    echo json_encode(['error' => 'Book not found']);
}
?>