<?php
session_start();
header('Content-Type: application/json');
require '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['error' => 'Email and password required']);
    exit;
}

$stmt = $pdo->prepare('SELECT ID, CONCAT(FName, " ", LName) AS Name, Email, Password, Role FROM Member WHERE Email = ?');
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && $user['Password'] === $password) {
    $_SESSION['user'] = [
        'ID' => $user['ID'],
        'Name' => $user['Name'],
        'Email' => $user['Email'],
        'Role' => $user['Role']
    ];
    echo json_encode($_SESSION['user']);
} else {
    echo json_encode(['error' => 'Invalid credentials']);
}
?>