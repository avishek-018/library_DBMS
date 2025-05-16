<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$isbn = trim($data['isbn'] ?? '');
$year = (int)($data['year'] ?? 0);
$authors = array_map('intval', $data['authors'] ?? []);
$new_author = trim($data['new_author'] ?? '');
$genres = array_map('intval', $data['genres'] ?? []);
$new_genre = trim($data['new_genre'] ?? '');

if (!$title || !$isbn || $year <= 0 || (empty($authors) && !$new_author) || (empty($genres) && !$new_genre)) {
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Handle new authors
    $new_author_ids = [];
    if ($new_author) {
        $new_authors = array_map('trim', explode(',', $new_author));
        foreach ($new_authors as $name) {
            if ($name) {
                $stmt = $pdo->prepare('INSERT INTO Author (Name) VALUES (?)');
                $stmt->execute([$name]);
                $new_author_ids[] = $pdo->lastInsertId();
            }
        }
    }
    $all_author_ids = array_merge($authors, $new_author_ids);

    // Handle new genres
    $new_genre_ids = [];
    if ($new_genre) {
        $new_genres = array_map('trim', explode(',', $new_genre));
        foreach ($new_genres as $name) {
            if ($name) {
                $stmt = $pdo->prepare('INSERT INTO Genre (Name) VALUES (?)');
                $stmt->execute([$name]);
                $new_genre_ids[] = $pdo->lastInsertId();
            }
        }
    }
    $all_genre_ids = array_merge($genres, $new_genre_ids);

    // Insert book
    $stmt = $pdo->prepare('INSERT INTO Book (Title, ISBN, PublicationYear) VALUES (?, ?, ?)');
    $stmt->execute([$title, $isbn, $year]);
    $book_id = $pdo->lastInsertId();

    // Insert authors
    foreach ($all_author_ids as $author_id) {
        $stmt = $pdo->prepare('INSERT INTO WrittenBy (Book_ID, Author_ID) VALUES (?, ?)');
        $stmt->execute([$book_id, $author_id]);
    }

    // Insert genres
    foreach ($all_genre_ids as $genre_id) {
        $stmt = $pdo->prepare('INSERT INTO BookGenre (Book_ID, Genre_ID) VALUES (?, ?)');
        $stmt->execute([$book_id, $genre_id]);
    }

    // Insert one copy
    $stmt = $pdo->prepare('INSERT INTO BookCopy (Book_ID, CopyNumber, IsAvailable) VALUES (?, ?, ?)');
    $stmt->execute([$book_id, 1, TRUE]);

    $pdo->commit();
    echo json_encode(['message' => 'Book added successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Failed to add book: ' . $e->getMessage()]);
}
?>