<?php
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    header('Location: index.php');
    exit;
}
?>
<nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between">
        <span class="text-xl font-bold">Library</span>
        <div>
            <span><?php echo htmlspecialchars($_SESSION['user']['FName'] . ' ' . $_SESSION['user']['LName'] . ' (librarian)'); ?></span>
            <a href="dashboard.php" class="ml-4">Dashboard</a>
            <a href="librarian_books.php" class="ml-4">Books</a>
            <a href="create_user.php" class="ml-4">Create User</a>
            <a href="members.php" class="ml-4">Members</a>
            <!-- <a href="reservations.php" class="ml-4">Reservations</a> -->
            <a href="logout.php" class="ml-4 bg-red-500 px-3 py-1 rounded">Logout</a>
        </div>
    </div>
</nav>