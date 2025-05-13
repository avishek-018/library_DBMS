<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['Role'] === 'librarian' ? 'dashboard.php' : 'books.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/scripts.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        <div id="error" class="text-red-500 mb-4 hidden"></div>
        <form id="loginForm" onsubmit="handleLogin(event)">
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" id="email" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" id="password" class="w-full p-2 border rounded" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700 transition">Login</button>
        </form>
    </div>
</body>
</html>