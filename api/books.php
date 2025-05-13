<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

$search = $_GET['search'] ?? '';
$offset = (int)($_GET['offset'] ?? 0);
$limit = (int)($_GET['limit'] ?? 10);

try {
    $query = '
        SELECT b.ID, b.Title, b.ISBN, b.PublicationYear,
               GROUP_CONCAT(a.Name) AS Authors,
               GROUP_CONCAT(g.Name) AS Genres
        FROM Book b
        LEFT JOIN WrittenBy wb ON b.ID = wb.Book_ID
        LEFT JOIN Author a ON wb.Author_ID = a.ID
        LEFT JOIN BookGenre bg ON b.ID = bg.Book_ID
        LEFT JOIN Genre g ON bg.Genre_ID = g.ID
    ';
    $params = [];
    if ($search) {
        $query .= ' WHERE b.Title LIKE ? OR a.Name LIKE ? OR g.Name LIKE ?';
        $params = ["%$search%", "%$search%", "%$search%"];
    }
    $query .= ' GROUP BY b.ID LIMIT ? OFFSET ?';

    $stmt = $pdo->prepare($query);
    // Bind parameters
    $paramIndex = 1;
    if ($search) {
        $stmt->bindValue($paramIndex++, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue($paramIndex++, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue($paramIndex++, "%$search%", PDO::PARAM_STR);
    }
    $stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM Book' . ($search ? ' WHERE Title LIKE ?' : ''));
    $countStmt->execute($search ? ["%$search%"] : []);
    $total = $countStmt->fetchColumn();

    echo json_encode(['books' => $books, 'total' => $total]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch books: ' . $e->getMessage()]);
}
?>