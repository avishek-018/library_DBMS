<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    header('Location: index.php');
    exit;
}

require 'config.php';

try {
    // Fetch pending and checked_out reservations
    $stmt = $pdo->query('
        SELECT r.ID, r.StartDate, r.EndDate, r.Status,
               m.FName, m.LName, m.Email,
               b.Title, bc.CopyNumber
        FROM Reservation r
        JOIN ReservationMember rm ON r.ID = rm.Reservation_ID
        JOIN Member m ON rm.Member_ID = m.ID
        JOIN ReservationBookCopy rbc ON r.ID = rbc.Reservation_ID
        JOIN BookCopy bc ON rbc.Book_ID = bc.Book_ID AND rbc.CopyNumber = bc.CopyNumber
        JOIN Book b ON bc.Book_ID = b.ID
        WHERE r.Status IN ("pending", "checked_out")
        ORDER BY r.StartDate ASC
    ');
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch stats for overview
    $totalBooks = $pdo->query('SELECT COUNT(*) FROM Book')->fetchColumn();
    $totalMembers = $pdo->query('SELECT COUNT(*) FROM Member')->fetchColumn();
    $pendingReservations = $pdo->query('SELECT COUNT(*) FROM Reservation WHERE Status = "pending"')->fetchColumn();
} catch (Exception $e) {
    $error = 'Failed to fetch data: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'navbar_librarian.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Librarian Dashboard</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($_GET['success'])): ?>
            <p class="text-green-500 mb-4"><?php echo htmlspecialchars($_GET['success']); ?></p>
        <?php elseif (isset($_GET['error'])): ?>
            <p class="text-red-500 mb-4"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold">Total Books</h2>
                <p class="text-2xl"><?php echo $totalBooks ?? 'N/A'; ?></p>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold">Total Members</h2>
                <p class="text-2xl"><?php echo $totalMembers ?? 'N/A'; ?></p>
            </div>
            <div class="bg-white p-4 shadow rounded">
                <h2 class="text-lg font-semibold">Pending Reservations</h2>
                <p class="text-2xl"><?php echo $pendingReservations ?? 'N/A'; ?></p>
            </div>
        </div>
        <!-- Reservation Requests -->
        <h2 class="text-xl font-bold mb-4">Reservation Requests</h2>
        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Member</th>
                    <th class="p-2 text-left">Book</th>
                    <th class="p-2 text-left">Copy</th>
                    <th class="p-2 text-left">Start Date</th>
                    <th class="p-2 text-left">End Date</th>
                    <th class="p-2 text-left">Status</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($reservations)): ?>
                    <tr><td colspan="7" class="p-2 text-center">No pending or checked out reservations</td></tr>
                <?php else: ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td class="p-2"><?php echo htmlspecialchars($reservation['FName'] . ' ' . $reservation['LName'] . ' (' . $reservation['Email'] . ')'); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($reservation['Title']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($reservation['CopyNumber']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($reservation['StartDate']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($reservation['EndDate']); ?></td>
                            <td class="p-2"><?php echo htmlspecialchars($reservation['Status']); ?></td>
                            <td class="p-2">
                                <?php if ($reservation['Status'] === 'pending'): ?>
                                    <a href="approve_reservation.php?id=<?php echo $reservation['ID']; ?>" class="bg-green-500 text-white px-2 py-1 rounded mr-1">Approve</a>
                                    <a href="cancel_reservation.php?id=<?php echo $reservation['ID']; ?>" class="bg-red-500 text-white px-2 py-1 rounded" onclick="return confirm('Are you sure you want to cancel this reservation?');">Cancel</a>
                                <?php elseif ($reservation['Status'] === 'checked_out'): ?>
                                    <a href="return_reservation.php?id=<?php echo $reservation['ID']; ?>" class="bg-blue-500 text-white px-2 py-1 rounded mr-1">Return</a>
                                    
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>