<?php
header('Content-Type: application/json');
require '../config.php';

$bookId = (int)($_GET['book_id'] ?? 0);

if ($bookId <= 0) {
    echo json_encode(['error' => 'Invalid book ID']);
    exit;
}

$stmt = $pdo->prepare('SELECT COUNT(*) AS available FROM BookCopy WHERE Book_ID = ? AND IsAvailable = TRUE');
$stmt->execute([$bookId]);
$available = $stmt->fetch(PDO::FETCH_ASSOC)['available'];

echo json_encode(['isAvailable' => $available > 0]);
?>