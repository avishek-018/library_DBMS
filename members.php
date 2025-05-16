<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    header('Location: index.php');
    exit;
}

require 'config.php';

try {
    $stmt = $pdo->query('SELECT ID, FName, LName, Email, Role, JoinDate FROM Member ORDER BY JoinDate DESC');
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'Failed to fetch members: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'navbar_librarian.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Manage Members</h1>
        <a href="create_user.php" class="bg-green-500 text-white px-4 py-2 rounded mb-4 inline-block">Add New Member</a>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p class="text-green-500 mb-4"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Name</th>
                    <th class="p-2 text-left">Email</th>
                    <th class="p-2 text-left">Role</th>
                    <th class="p-2 text-left">Join Date</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($members)): ?>
                    <tr><td colspan="5" class="p-2 text-center">No members found</td></tr>
                <?php else: ?>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td class="p-2"><?php echo htmlspecialchars($member['FName'] . ' ' . $member['LName']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($member['Email']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($member['Role']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($member['JoinDate']); ?></td>
                            <td class="p-2">
                                <a href="delete_member.php?id=<?php echo $member['ID']; ?>" class="text-red-500" onclick="return confirm('Are you sure you want to delete this member?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>