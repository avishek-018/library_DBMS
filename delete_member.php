<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    header('Location: index.php');
    exit;
}

require 'config.php';

$member_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($member_id <= 0) {
    header('Location: members.php?error=Invalid+member+ID');
    exit;
}

try {
    $stmt = $pdo->prepare('DELETE FROM Member WHERE ID = ?');
    $stmt->execute([$member_id]);
    header('Location: members.php?success=Member+deleted');
    exit;
} catch (Exception $e) {
    header('Location: members.php?error=Failed+to+delete+member:+' . urlencode($e->getMessage()));
    exit;
}
?>