<?php
session_start();
header('Content-Type: application/json');
require '../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    echo json_encode(['error' => 'Unauthorized: Librarian login required']);
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT r.ID, r.StartDate, r.EndDate, r.Status, b.Title, m.FName, m.LName
        FROM Reservation r
        JOIN ReservationMember rm ON r.ID = rm.Reservation_ID
        JOIN ReservationBookCopy rbc ON r.ID = rbc.Reservation_ID
        JOIN Book b ON rbc.Book_ID = b.ID
        JOIN Member m ON rm.Member_ID = m.ID
        WHERE r.Status IN ("pending", "checked_out")
    ');
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['requests' => $requests]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch reservation requests: ' . $e->getMessage()]);
}
?>