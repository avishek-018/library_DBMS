<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

$search = $_GET['search'] ?? '';

try {
    $query = 'SELECT ID, FName, LName, Email, Address, JoinDate, PNumber, Role FROM Member';
    $params = [];
    if ($search) {
        $query .= ' WHERE FName LIKE ? OR LName LIKE ? OR Email LIKE ?';
        $params = ["%$search%", "%$search%", "%$search%"];
    }
    $query .= ' ORDER BY FName, LName';

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['members' => $members]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch members: ' . $e->getMessage()]);
}
?>