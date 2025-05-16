<?php
header('Content-Type: application/json');
ini_set('display_errors', 0);
error_reporting(E_ALL);

require '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$fname = trim($data['fname'] ?? '');
$lname = trim($data['lname'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';
$address = trim($data['address'] ?? '');
$pnumber = trim($data['pnumber'] ?? '');
$join_date = date('Y-m-d'); // Current date

if (!$fname || !$lname || !$email || !$password || !$address || !$pnumber || !in_array($role, ['member', 'librarian'])) {
    echo json_encode(['error' => 'All fields are required, and role must be member or librarian']);
    exit;
}

try {
    // Check if email exists
    $stmt = $pdo->prepare('SELECT ID FROM Member WHERE Email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['error' => 'Email already exists']);
        exit;
    }

    // Insert member
    $stmt = $pdo->prepare('INSERT INTO Member (FName, LName, Email, Address, JoinDate, PNumber, Password, Role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$fname, $lname, $email, $address, $join_date, $pnumber, $password, $role]);

    echo json_encode(['message' => 'User created successfully']);
} catch (Exception $e) {
    echo json_encode(['error' => 'Failed to create user: ' . $e->getMessage()]);
}
?>