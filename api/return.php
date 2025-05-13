<?php
session_start();
header('Content-Type: application/json');
require '../config.php';

// Check if user is librarian
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    echo json_encode(['error' => 'Unauthorized: Librarian login required']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$reservation_id = (int)($data['reservation_id'] ?? 0);

if ($reservation_id <= 0) {
    echo json_encode(['error' => 'Invalid reservation ID']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Verify reservation is checked out
    $stmt = $pdo->prepare('SELECT Status FROM Reservation WHERE ID = ?');
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation || $reservation['Status'] !== 'checked_out') {
        $pdo->rollBack();
        echo json_encode(['error' => 'Invalid or non-checked-out reservation']);
        exit;
    }

    // Get book copy
    $stmt = $pdo->prepare('SELECT Book_ID, CopyNumber FROM ReservationBookCopy WHERE Reservation_ID = ?');
    $stmt->execute([$reservation_id]);
    $copy = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$copy) {
        $pdo->rollBack();
        echo json_encode(['error' => 'No book copy linked to reservation']);
        exit;
    }

    // Update reservation status
    $stmt = $pdo->prepare('UPDATE Reservation SET Status = ? WHERE ID = ?');
    $stmt->execute(['returned', $reservation_id]);

    // Mark copy as available
    $stmt = $pdo->prepare('UPDATE BookCopy SET IsAvailable = TRUE WHERE Book_ID = ? AND CopyNumber = ?');
    $stmt->execute([$copy['Book_ID'], $copy['CopyNumber']]);

    $pdo->commit();
    echo json_encode(['message' => 'Book returned']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Return failed: ' . $e->getMessage()]);
}
?>