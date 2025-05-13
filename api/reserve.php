<?php
session_start();
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'member') {
    echo json_encode(['error' => 'Unauthorized: Member login required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$book_id = (int)($data['book_id'] ?? 0);

if ($book_id <= 0) {
    echo json_encode(['error' => 'Invalid book ID']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Find an available copy
    $stmt = $pdo->prepare('SELECT Book_ID, CopyNumber FROM BookCopy WHERE Book_ID = ? AND IsAvailable = TRUE LIMIT 1');
    $stmt->execute([$book_id]);
    $copy = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$copy) {
        $pdo->rollBack();
        echo json_encode(['error' => 'No available copies']);
        exit;
    }

    // Create reservation
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime('+14 days'));
    $stmt = $pdo->prepare('INSERT INTO Reservation (StartDate, EndDate, Status) VALUES (?, ?, ?)');
    $stmt->execute([$start_date, $end_date, 'pending']);
    $reservation_id = $pdo->lastInsertId();

    // Link reservation to member
    $member_id = $_SESSION['user']['ID'];
    $stmt = $pdo->prepare('INSERT INTO ReservationMember (Reservation_ID, Member_ID) VALUES (?, ?)');
    $stmt->execute([$reservation_id, $member_id]);

    // Link reservation to book copy
    $stmt = $pdo->prepare('INSERT INTO ReservationBookCopy (Reservation_ID, Book_ID, CopyNumber) VALUES (?, ?, ?)');
    $stmt->execute([$reservation_id, $copy['Book_ID'], $copy['CopyNumber']]);

    $pdo->commit();
    echo json_encode(['message' => 'Reservation pending', 'reservation_id' => $reservation_id]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Reservation failed: ' . $e->getMessage()]);
}
?>