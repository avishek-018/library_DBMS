<?php
session_start();
header('Content-Type: application/json');
require '../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'member') {
    echo json_encode(['error' => 'Unauthorized: Member login required']);
    exit;
}

try {
    $stmt = $pdo->prepare('
        SELECT r.ID, r.StartDate, r.EndDate, r.Status, b.Title
        FROM Reservation r
        JOIN ReservationMember rm ON r.ID = rm.Reservation_ID
        JOIN ReservationBookCopy rbc ON r.ID = rbc.Reservation_ID
        JOIN Book b ON rbc.Book_ID = b.ID
        WHERE rm.Member_ID = ?
    ');
    $stmt->execute([$_SESSION['user']['ID']]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['reservations' => $reservations]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to fetch reservations: ' . $e->getMessage()]);
}
?>