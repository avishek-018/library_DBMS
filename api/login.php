<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

session_start();

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['error' => 'Email and password are required']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT ID, FName, LName, Email, Address, PNumber, JoinDate, Role FROM Member WHERE Email = ? AND Password = ?');
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['error' => 'Invalid email or password']);
        exit;
    }

    $_SESSION['user'] = $user;
    echo json_encode(['message' => 'Login successful', 'role' => $user['Role']]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to login: ' . $e->getMessage()]);
}
?>