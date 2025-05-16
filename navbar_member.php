<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'member') {
    header('Location: index.php');
    exit;
}
?>
<nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between">
        <span class="text-xl font-bold">Library</span>
        <div>
            <span><?php echo htmlspecialchars((isset($_SESSION['user']['FName']) ? $_SESSION['user']['FName'] . ' ' : '') . (isset($_SESSION['user']['LName']) ? $_SESSION['user']['LName'] : $_SESSION['user']['Email'] ?? 'Unknown') . ' (member)'); ?></span>
            <a href="books.php" class="ml-4">Books</a>
            <a href="profile.php" class="ml-4">Profile</a>
            <a href="reservations.php" class="ml-4">My Reservations</a>
            <a href="logout.php" class="ml-4 bg-red-500 px-3 py-1 rounded">Logout</a>
        </div>
    </div>
</nav>