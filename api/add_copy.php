<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$book_id = (int)($data['book_id'] ?? 0);
$num_copies = (int)($data['num_copies'] ?? 0);

if ($book_id <= 0 || $num_copies <= 0) {
    echo json_encode(['error' => 'Invalid book ID or number of copies']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Get the highest CopyNumber for this book
    $stmt = $pdo->prepare('SELECT COALESCE(MAX(CopyNumber), 0) AS max_copy FROM BookCopy WHERE Book_ID = ?');
    $stmt->execute([$book_id]);
    $max_copy = $stmt->fetch(PDO::FETCH_ASSOC)['max_copy'];

    // Insert new copies
    for ($i = 1; $i <= $num_copies; $i++) {
        $copy_number = $max_copy + $i;
        $stmt = $pdo->prepare('INSERT INTO BookCopy (Book_ID, CopyNumber, IsAvailable) VALUES (?, ?, ?)');
        $stmt->execute([$book_id, $copy_number, TRUE]);
    }

    $pdo->commit();
    echo json_encode(['message' => 'Copies added successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Failed to add copies: ' . $e->getMessage()]);
}
?>