<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/scripts.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'navbar_member.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">My Reservations</h1>
        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Book Title</th>
                    <th class="p-2 text-left">Start Date</th>
                    <th class="p-2 text-left">End Date</th>
                    <th class="p-2 text-left">Status</th>
                </tr>
            </thead>
            <tbody id="reservations-table">
            </tbody>
        </table>
    </div>
    <script>
        async function loadReservations() {
            const reservations = await fetchReservations();
            const tableBody = document.getElementById('reservations-table');
            tableBody.innerHTML = '';
            if (reservations.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="4" class="p-2 text-center">No reservations</td></tr>';
                return;
            }
            reservations.forEach(r => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="p-2">${r.Title}</td>
                    <td class="p-2">${r.StartDate}</td>
                    <td class="p-2">${r.EndDate}</td>
                    <td class="p-2">${r.Status.replace('_', ' ').toUpperCase()}</td>
                `;
                tableBody.appendChild(row);
            });
        }
        document.addEventListener('DOMContentLoaded', loadReservations);
    </script>
</body>
</html>