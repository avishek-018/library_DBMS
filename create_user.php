<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/scripts.js"></script>
</head>
<body class="bg-gray-100">
        <?php include 'navbar_librarian.php'; ?>

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Create User</h1>
        <form id="create-user-form" class="max-w-md grid gap-4">
            <div>
                <label for="fname" class="block text-sm font-medium">First Name</label>
                <input type="text" id="fname" placeholder="First Name" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="lname" class="block text-sm font-medium">Last Name</label>
                <input type="text" id="lname" placeholder="Last Name" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" id="email" placeholder="Email" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="address" class="block text-sm font-medium">Address</label>
                <input type="text" id="address" placeholder="Address" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="pnumber" class="block text-sm font-medium">Phone Number</label>
                <input type="text" id="pnumber" placeholder="Phone Number" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" id="password" placeholder="Password" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="role" class="block text-sm font-medium">Role</label>
                <select id="role" class="border p-2 w-full" required>
                    <option value="member">Member</option>
                    <option value="librarian">Librarian</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Create User</button>
            </div>
        </form>
        <div id="error-message" class="text-red-500 mt-4 hidden"></div>
    </div>
    <script>
        document.getElementById('create-user-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorDiv = document.getElementById('error-message');
            const fname = document.getElementById('fname').value.trim();
            const lname = document.getElementById('lname').value.trim();
            const email = document.getElementById('email').value.trim();
            const address = document.getElementById('address').value.trim();
            const pnumber = document.getElementById('pnumber').value.trim();
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;
            if (!fname || !lname || !email || !address || !pnumber || !password || !role) {
                errorDiv.textContent = 'All fields are required';
                errorDiv.classList.remove('hidden');
                return;
            }
            try {
                const response = await fetch('api/create_user.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ fname, lname, email, address, pnumber, password, role })
                });
                const result = await response.json();
                if (result.error) {
                    errorDiv.textContent = 'Failed to create user: ' + result.error;
                    errorDiv.classList.remove('hidden');
                } else {
                    alert('User created successfully!');
                    document.getElementById('create-user-form').reset();
                    errorDiv.classList.add('hidden');
                }
            } catch (error) {
                errorDiv.textContent = 'Error creating user: ' + error.message;
                errorDiv.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>