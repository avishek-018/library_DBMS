<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'member') {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'navbar_member.php'; ?>
    <div class="container mx-auto p-4 max-w-md">
        <h1 class="text-2xl font-bold mb-4">Profile</h1>
        <div class="bg-white p-4 rounded shadow">
            <p><strong>Name:</strong> <?php echo htmlspecialchars((isset($_SESSION['user']['FName']) ? $_SESSION['user']['FName'] . ' ' : '') . (isset($_SESSION['user']['LName']) ? $_SESSION['user']['LName'] : 'N/A')); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user']['Email'] ?? 'N/A'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($_SESSION['user']['Address'] ?? 'N/A'); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($_SESSION['user']['PNumber'] ?? 'N/A'); ?></p>
            <p><strong>Joined:</strong> <?php echo htmlspecialchars($_SESSION['user']['JoinDate'] ?? 'N/A'); ?></p>
        </div>
    </div>
</body>
</html>