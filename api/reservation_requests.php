<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

try {
    $stmt = $pdo->query('
        SELECT r.ID, r.StartDate, r.EndDate, r.Status, b.Title, m.FName, m.LName
        FROM Reservation r
        JOIN ReservationBookCopy rbc ON r.ID = rbc.Reservation_ID
        JOIN Book b ON rbc.Book_ID = b.ID
        JOIN ReservationMember rm ON r.ID = rm.Reservation_ID
        JOIN Member m ON rm.Member_ID = m.ID
        WHERE r.Status IN ("pending", "checked_out")
    ');
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($requests);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch reservation requests: ' . $e->getMessage()]);
}
?>