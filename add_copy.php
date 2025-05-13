<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    header('Location: index.php');
    exit;
}
require 'config.php';

$book_id = (int)($_GET['id'] ?? 0);
if ($book_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// Fetch book title
$stmt = $pdo->prepare('SELECT Title FROM Book WHERE ID = ?');
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$book) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Copies - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/scripts.js"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <span class="text-xl font-bold">Library</span>
            <div>
                <a href="dashboard.php" class="ml-4">Dashboard</a>
                <a href="logout.php" class="ml-4 bg-red-500 px-3 py-1 rounded">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Add Copies for <?php echo htmlspecialchars($book['Title']); ?></h1>
        <form id="add-copy-form" class="max-w-md">
            <div>
                <label for="num_copies" class="block text-sm font-medium">Number of Copies</label>
                <input type="number" id="num_copies" min="1" class="border p-2 w-full" required>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Add Copies</button>
        </form>
    </div>
    <script>
        document.getElementById('add-copy-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const book_id = <?php echo $book_id; ?>;
            const num_copies = parseInt(document.getElementById('num_copies').value);
            if (num_copies <= 0) {
                alert('Please enter a valid number of copies');
                return;
            }
            try {
                const response = await fetch('api/add_copy.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ book_id, num_copies })
                });
                const result = await response.json();
                if (result.error) {
                    alert('Failed to add copies: ' + result.error);
                } else {
                    alert('Copies added successfully!');
                    window.location.href = 'dashboard.php';
                }
            } catch (error) {
                alert('Error adding copies: ' + error.message);
            }
        });
    </script>
</body>
</html>