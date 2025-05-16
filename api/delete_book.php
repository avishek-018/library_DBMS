<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception('Invalid request method');
    }

    $book_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($book_id <= 0) {
        throw new Exception('Invalid book ID');
    }

    $stmt = $pdo->prepare('DELETE FROM Book WHERE ID = ?');
    $stmt->execute([$book_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Book not found');
    }

    echo json_encode(['success' => 'Book deleted successfully']);
} catch (Exception $e) {
    error_log('API Error: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to delete book: ' . $e->getMessage()]);
}
?>