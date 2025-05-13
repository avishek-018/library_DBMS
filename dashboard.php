<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['Role'] !== 'librarian') {
    header('Location: index.php');
    exit;
}
require 'config.php';

// Fetch authors and genres for the form
$stmt = $pdo->query('SELECT ID, Name FROM Author');
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->query('SELECT ID, Name FROM Genre');
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian Dashboard - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/scripts.js"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between">
            <span class="text-xl font-bold">Library</span>
            <div>
                <span><?php echo htmlspecialchars($_SESSION['user']['Name']) . ' (librarian)'; ?></span>
                <a href="dashboard.php" class="ml-4">Dashboard</a>
                <a href="#reservation-requests" class="ml-4">Reservation Requests</a>
                <a href="logout.php" class="ml-4 bg-red-500 px-3 py-1 rounded">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Librarian Dashboard</h1>
        <h2 class="text-xl font-bold mb-2">Add New Book</h2>
        <form id="add-book-form" class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="title" class="block text-sm font-medium">Title</label>
                <input type="text" id="title" placeholder="Title" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="isbn" class="block text-sm font-medium">ISBN</label>
                <input type="text" id="isbn" placeholder="ISBN" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium">Publication Year</label>
                <input type="number" id="year" placeholder="Publication Year" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="authors" class="block text-sm font-medium">Author(s)</label>
                <select id="authors" multiple class="border p-2 w-full">
                    <?php foreach ($authors as $author): ?>
                        <option value="<?php echo $author['ID']; ?>"><?php echo htmlspecialchars($author['Name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="genres" class="block text-sm font-medium">Genre(s)</label>
                <select id="genres" multiple class="border p-2 w-full">
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?php echo $genre['ID']; ?>"><?php echo htmlspecialchars($genre['Name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Add Book</button>
            </div>
        </form>
        <h2 class="text-xl font-bold mb-2">Books</h2>
        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Title</th>
                    <th class="p-2 text-left">Author(s)</th>
                    <th class="p-2 text-left">Genre(s)</th>
                    <th class="p-2 text-left">ISBN</th>
                    <th class="p-2 text-left">Year</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="books-table">
            </tbody>
        </table>
        <h2 class="text-xl font-bold mb-2 mt-8" id="reservation-requests">Reservation Requests</h2>
        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Book Title</th>
                    <th class="p-2 text-left">Member</th>
                    <th class="p-2 text-left">Start Date</th>
                    <th class="p-2 text-left">End Date</th>
                    <th class="p-2 text-left">Status</th>
                    <th class="p-2 text-left">Action</th>
                </tr>
            </thead>
            <tbody id="requests-table">
            </tbody>
        </table>
    </div>
    <script>
        async function loadBooks() {
            try {
                const response = await fetch('api/books.php?limit=100');
                const result = await response.json();
                if (result.error) {
                    console.error('Failed to fetch books:', result.error);
                    return;
                }
                const tableBody = document.getElementById('books-table');
                tableBody.innerHTML = '';
                result.books.forEach(book => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="p-2">${book.Title}</td>
                        <td class="p-2">${book.Authors || 'Unknown'}</td>
                        <td class="p-2">${book.Genres || 'Unknown'}</td>
                        <td class="p-2">${book.ISBN}</td>
                        <td class="p-2">${book.PublicationYear}</td>
                        <td class="p-2">
                            <a href="edit_book.php?id=${book.ID}" class="bg-yellow-500 text-white px-2 py-1 rounded mr-1">Edit</a>
                            <button onclick="deleteBook(${book.ID})" class="bg-red-500 text-white px-2 py-1 rounded mr-1">Delete</button>
                            <a href="add_copy.php?id=${book.ID}" class="bg-green-500 text-white px-2 py-1 rounded">Add Copy</a>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            } catch (error) {
                console.error('Fetch error:', error);
            }
        }
        async function loadReservationRequests() {
            const requests = await fetchReservationRequests();
            const tableBody = document.getElementById('requests-table');
            tableBody.innerHTML = '';
            if (requests.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="p-2 text-center">No reservation requests</td></tr>';
                return;
            }
            requests.forEach(r => {
                const row = document.createElement('tr');
                const action = r.Status === 'pending'
                    ? `<button onclick="checkoutReservation(${r.ID})" class="bg-green-500 text-white px-2 py-1 rounded">Checkout</button>`
                    : `<button onclick="returnReservation(${r.ID})" class="bg-blue-500 text-white px-2 py-1 rounded">Return</button>`;
                row.innerHTML = `
                    <td class="p-2">${r.Title}</td>
                    <td class="p-2">${r.FName} ${r.LName}</td>
                    <td class="p-2">${r.StartDate}</td>
                    <td class="p-2">${r.EndDate}</td>
                    <td class="p-2">${r.Status.replace('_', ' ').toUpperCase()}</td>
                    <td class="p-2">${action}</td>
                `;
                tableBody.appendChild(row);
            });
        }
        document.getElementById('add-book-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = document.getElementById('title').value;
            const isbn = document.getElementById('isbn').value;
            const year = document.getElementById('year').value;
            const authors = Array.from(document.getElementById('authors').selectedOptions).map(opt => opt.value);
            const genres = Array.from(document.getElementById('genres').selectedOptions).map(opt => opt.value);
            try {
                const response = await fetch('api/add_book.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ title, isbn, year, authors, genres })
                });
                const result = await response.json();
                if (result.error) {
                    alert('Failed to add book: ' + result.error);
                } else {
                    alert('Book added successfully!');
                    document.getElementById('add-book-form').reset();
                    loadBooks();
                }
            } catch (error) {
                alert('Error adding book: ' + error.message);
            }
        });
        document.addEventListener('DOMContentLoaded', () => {
            loadBooks();
            loadReservationRequests();
        });
    </script>
</body>
</html>