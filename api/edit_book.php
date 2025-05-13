<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

try {
    require '../config.php';
} catch (Exception $e) {
    echo json_encode(['error' => 'Configuration error: ' . $e->getMessage()]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$book_id = (int)($data['book_id'] ?? 0);
$title = trim($data['title'] ?? '');
$isbn = trim($data['isbn'] ?? '');
$year = (int)($data['year'] ?? 0);
$authors = array_map('intval', $data['authors'] ?? []);
$genres = array_map('intval', $data['genres'] ?? []);

if ($book_id <= 0 || !$title || !$isbn || $year <= 0 || empty($authors) || empty($genres)) {
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Verify book exists
    $stmt = $pdo->prepare('SELECT ID FROM Book WHERE ID = ?');
    $stmt->execute([$book_id]);
    if (!$stmt->fetch()) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Book not found']);
        exit;
    }

    // Update book
    $stmt = $pdo->prepare('UPDATE Book SET Title = ?, ISBN = ?, PublicationYear = ? WHERE ID = ?');
    $stmt->execute([$title, $isbn, $year, $book_id]);

    // Delete existing authors and genres
    $stmt = $pdo->prepare('DELETE FROM WrittenBy WHERE Book_ID = ?');
    $stmt->execute([$book_id]);
    $stmt = $pdo->prepare('DELETE FROM BookGenre WHERE Book_ID = ?');
    $stmt->execute([$book_id]);

    // Insert new authors
    foreach ($authors as $author_id) {
        $stmt = $pdo->prepare('INSERT INTO WrittenBy (Book_ID, Author_ID) VALUES (?, ?)');
        $stmt->execute([$book_id, $author_id]);
    }

    // Insert new genres
    foreach ($genres as $genre_id) {
        $stmt = $pdo->prepare('INSERT INTO BookGenre (Book_ID, Genre_ID) VALUES (?, ?)');
        $stmt->execute([$book_id, $genre_id]);
    }

    $pdo->commit();
    echo json_encode(['message' => 'Book updated successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Failed to update book: ' . $e->getMessage()]);
}
?>