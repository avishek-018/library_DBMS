<?php
require 'config.php';
$stmt = $pdo->prepare('SELECT Password FROM Member WHERE Email = ?');
$stmt->execute(['alice@example.com']);
$hash = $stmt->fetch(PDO::FETCH_ASSOC)['Password'];
echo password_verify('alice123', $hash) ? 'Password matches' : 'Password does not match';
?>