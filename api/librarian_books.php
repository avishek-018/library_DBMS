<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

try {
    if (!$pdo) {
        throw new Exception('Database connection failed');
    }

    $offset = max(0, (int)($_GET['offset'] ?? 0));
    $limit = max(1, (int)($_GET['limit'] ?? 10));
    $search = trim($_GET['search'] ?? '');

    // Count total books for pagination
    $countQuery = 'SELECT COUNT(DISTINCT b.ID) FROM Book b';
    $countParams = [];
    if ($search) {
        $countQuery .= ' WHERE b.Title LIKE ? OR b.ISBN LIKE ?';
        $countParams = ["%$search%", "%$search%"];
        $countQuery .= ' OR b.ID IN (SELECT wb.Book_ID FROM WrittenBy wb JOIN Author a ON wb.Author_ID = a.ID WHERE a.Name LIKE ?)';
        $countParams[] = "%$search%";
    }
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($countParams);
    $totalBooks = $countStmt->fetchColumn();

    // Fetch books with total and available copies
    $query = '
        SELECT b.ID, b.Title, b.ISBN, b.PublicationYear,
               GROUP_CONCAT(DISTINCT a.Name SEPARATOR ", ") as Authors,
               GROUP_CONCAT(DISTINCT g.Name SEPARATOR ", ") as Genres,
               (
                   SELECT COUNT(*)
                   FROM BookCopy bc
                   WHERE bc.Book_ID = b.ID
                   AND bc.IsAvailable = 1
               ) as AvailableCopies,
               (
                   SELECT COUNT(*)
                   FROM BookCopy bc
                   WHERE bc.Book_ID = b.ID
               ) as TotalCopies
        FROM Book b
        LEFT JOIN WrittenBy wb ON b.ID = wb.Book_ID
        LEFT JOIN Author a ON wb.Author_ID = a.ID
        LEFT JOIN BookGenre bg ON b.ID = bg.Book_ID
        LEFT JOIN Genre g ON bg.Genre_ID = g.ID
    ';
    $params = [];
    if ($search) {
        $query .= ' WHERE (b.Title LIKE ? OR b.ISBN LIKE ?)';
        $params = ["%$search%", "%$search%"];
        $query .= ' OR b.ID IN (SELECT wb.Book_ID FROM WrittenBy wb JOIN Author a ON wb.Author_ID = a.ID WHERE a.Name LIKE ?)';
        $params[] = "%$search%";
    }
    $query .= ' GROUP BY b.ID, b.Title, b.ISBN, b.PublicationYear';
    $query .= ' LIMIT ' . $limit . ' OFFSET ' . $offset;

    $stmt = $pdo->prepare($query);
    if (!$stmt) {
        throw new Exception('Failed to prepare query: ' . implode(', ', $pdo->errorInfo()));
    }
    error_log("Executing query: $query with params: " . json_encode($params));
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['books' => $books, 'totalBooks' => $totalBooks]);
} catch (Exception $e) {
    error_log('API Error: ' . $e->getMessage());
    echo json_encode(['error' => 'Failed to fetch books: ' . $e->getMessage()]);
}
?>