<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System - Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4 flex justify-between items-center shadow-md">
        <h1 class="text-xl font-bold hover:text-blue-200 transition cursor-pointer" onclick="window.location.href='<?php echo $user['Role'] === 'librarian' ? 'dashboard.php' : 'books.php'; ?>'">Library Management System</h1>
        <div class="flex items-center space-x-4">
            <span class="text-sm"><?php echo htmlspecialchars($user['Name']); ?> (<?php echo htmlspecialchars($user['Role']); ?>)</span>
            <?php if ($user['Role'] === 'member'): ?>
                <button onclick="window.location.href='books.php'" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Books</button>
                <button onclick="window.location.href='reservations.php'" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">My Reservations</button>
            <?php endif; ?>
            <button onclick="window.location.href='api/logout.php'" class="bg-red-500 px-4 py-2 rounded hover:bg-red-600">Logout</button>
        </div>
    </nav>
    <div class="p-6 max-w-7xl mx-auto">
        <h2 class="text-2xl font-bold mb-6">Profile</h2>
        <div class="bg-white p-4 rounded-lg shadow-md">
            <p class="text-gray-600 mb-2"><strong>Name:</strong> <?php echo htmlspecialchars($user['Name']); ?></p>
            <p class="text-gray-600 mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
            <p class="text-gray-600 mb-2"><strong>Role:</strong> <?php echo htmlspecialchars($user['Role']); ?></p>
        </div>
    </div>
</body>
</html>