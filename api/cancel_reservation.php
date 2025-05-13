<?php
session_start();
header('Content-Type: application/json');
require '../config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$reservationId = (int)($_GET['id'] ?? 0);

if ($reservationId <= 0) {
    echo json_encode(['error' => 'Invalid reservation ID']);
    exit;
}

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare('SELECT Book_ID, CopyNumber FROM Reservation WHERE ID = ? AND IsPending = TRUE');
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        $pdo->rollBack();
        echo json_encode(['error' => 'Reservation not found or already processed']);
        exit;
    }

    $stmt = $pdo->prepare('UPDATE BookCopy SET IsAvailable = TRUE WHERE Book_ID = ? AND CopyNumber = ?');
    $stmt->execute([$reservation['Book_ID'], $reservation['CopyNumber']]);

    $stmt = $pdo->prepare('DELETE FROM Reservation WHERE ID = ?');
    $stmt->execute([$reservationId]);

    $pdo->commit();
    echo json_encode(['message' => 'Reservation cancelled successfully']);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Failed to cancel reservation']);
}
?>