<?php
session_start();
header('Content-Type: application/json');
require '../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$stmt = $pdo->prepare('
    SELECT r.ID, b.Title, m.Name AS MemberName, r.ReservationDate, r.IsPending
    FROM Reservation r
    JOIN Book b ON r.Book_ID = b.ID
    JOIN Member m ON r.Member_ID = m.ID
');
$stmt->execute();
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($reservations);
?>