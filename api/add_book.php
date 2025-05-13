<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$title = $data['title'] ?? '';
$isbn = $data['isbn'] ?? '';
$year = (int)($data['year'] ?? 0);
$authors = $data['authors'] ?? [];
$genres = $data['genres'] ?? [];

if (!$title || !$isbn || $year <= 0 || empty($authors) || empty($genres)) {
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Insert book
    $stmt = $pdo->prepare('INSERT INTO Book (Title, ISBN, PublicationYear) VALUES (?, ?, ?)');
    $stmt->execute([$title, $isbn, $year]);
    $book_id = $pdo->lastInsertId();

    // Insert authors
    foreach ($authors as $author_id) {
        $stmt = $pdo->prepare('INSERT INTO WrittenBy (Book_ID, Author_ID) VALUES (?, ?)');
        $stmt->execute([$book_id, (int)$author_id]);
    }

    // Insert genres
    foreach ($genres as $genre_id) {
        $stmt = $pdo->prepare('INSERT INTO BookGenre (Book_ID, Genre_ID) VALUES (?, ?)');
        $stmt->execute([$book_id, (int)$genre_id]);
    }

    // Insert one copy (trigger in library_setup.sql handles BookCopy)
    $stmt = $pdo->prepare('INSERT INTO BookCopy (Book_ID, CopyNumber, IsAvailable) VALUES (?, ?, ?)');
    $stmt->execute([$book_id, 1, TRUE]);

    $pdo->commit();
    echo json_encode(['message' => 'Book added successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Failed to add book: ' . $e->getMessage()]);
}
?>