<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['Role'] === 'librarian' ? 'dashboard.php' : 'books.php'));
    exit;
}
require 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4 max-w-md">
        <h1 class="text-2xl font-bold mb-4 text-center">Library Login</h1>
        <form id="login-form" class="grid gap-4">
            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" id="email" placeholder="Email" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" id="password" placeholder="Password" class="border p-2 w-full" required>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Login</button>
            </div>
        </form>
        <div id="error-message" class="text-red-500 mt-4 hidden"></div>
    </div>
    <script>
        document.getElementById('login-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorDiv = document.getElementById('error-message');
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            if (!email || !password) {
                errorDiv.textContent = 'Email and password are required';
                errorDiv.classList.remove('hidden');
                return;
            }
            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });
                const result = await response.json();
                if (result.error) {
                    errorDiv.textContent = result.error;
                    errorDiv.classList.remove('hidden');
                } else {
                    window.location.href = result.role === 'librarian' ? 'dashboard.php' : 'books.php';
                }
            } catch (error) {
                errorDiv.textContent = 'Error logging in: ' + error.message;
                errorDiv.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>