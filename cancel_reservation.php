<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    header('Location: index.php');
    exit;
}

require 'config.php';

$reservation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($reservation_id <= 0) {
    header('Location: dashboard.php?error=Invalid+reservation+ID');
    exit;
}

try {
    $pdo->beginTransaction();

    // Update reservation status to cancelled
    $stmt = $pdo->prepare('UPDATE Reservation SET Status = "cancelled" WHERE ID = ? AND Status IN ("pending", "checked_out")');
    $stmt->execute([$reservation_id]);

    if ($stmt->rowCount() === 0) {
        throw new Exception('Reservation not found or not cancellable');
    }

    // Restore book copy availability
    $stmt = $pdo->prepare('
        UPDATE BookCopy bc
        JOIN ReservationBookCopy rbc ON bc.Book_ID = rbc.Book_ID AND bc.CopyNumber = rbc.CopyNumber
        SET bc.IsAvailable = 1
        WHERE rbc.Reservation_ID = ?
    ');
    $stmt->execute([$reservation_id]);

    $pdo->commit();
    header('Location: dashboard.php?success=Reservation+cancelled');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    header('Location: dashboard.php?error=Failed+to+cancel+reservation:+' . urlencode($e->getMessage()));
    exit;
}
?>