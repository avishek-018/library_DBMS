<?php
session_start();
header('Content-Type: application/json');
require '../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$bookId = (int)($_GET['id'] ?? 0);

if ($bookId <= 0) {
    echo json_encode(['error' => 'Invalid book ID']);
    exit;
}

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('DELETE FROM WrittenBy WHERE Book_ID = ?');
    $stmt->execute([$bookId]);
    $stmt = $pdo->prepare('DELETE FROM BookGenre WHERE Book_ID = ?');
    $stmt->execute([$bookId]);
    $stmt = $pdo->prepare('DELETE FROM Reservation WHERE Book_ID = ?');
    $stmt->execute([$bookId]);
    $stmt = $pdo->prepare('DELETE FROM BookCopy WHERE Book_ID = ?');
    $stmt->execute([$bookId]);
    $stmt = $pdo->prepare('DELETE FROM Book WHERE ID = ?');
    $stmt->execute([$bookId]);

    $pdo->commit();
    echo json_encode(['message' => 'Book deleted successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Failed to delete book']);
}
?>