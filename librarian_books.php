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
    <title>Books - Librarian - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include 'navbar_librarian.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Books</h1>
        <div class="mb-4 flex justify-between items-center">
            <input type="text" id="search" placeholder="Search by title, author, or ISBN..." class="border p-2 w-1/3">
            <button id="search-button" class="bg-blue-500 text-white px-4 py-2 rounded ml-2">Search</button>
        </div>
        <table class="w-full bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Title</th>
                    <th class="p-2 text-left">Author(s)</th>
                    <th class="p-2 text-left">Genre(s)</th>
                    <th class="p-2 text-left">ISBN</th>
                    <th class="p-2 text-left">Year</th>
                    <th class="p-2 text-left">Total Copies</th>
                    <th class="p-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody id="books-table">
            </tbody>
        </table>
        <div class="mt-4 flex justify-between items-center">
            <button id="prev-page" class="bg-gray-300 px-4 py-2 rounded disabled:opacity-50" disabled>Previous</button>
            <span>Page <span id="current-page">1</span> of <span id="total-pages">1</span></span>
            <button id="next-page" class="bg-gray-300 px-4 py-2 rounded disabled:opacity-50" disabled>Next</button>
        </div>
        <div id="error-message" class="text-red-500 mt-4 hidden"></div>
    </div>
    <script>
        let currentPage = 1;
        const booksPerPage = 10;

        async function loadBooks(page = 1, search = '') {
            const errorDiv = document.getElementById('error-message');
            try {
                const offset = (page - 1) * booksPerPage;
                const response = await fetch(`api/librarian_books.php?offset=${offset}&limit=${booksPerPage}&search=${encodeURIComponent(search)}`, {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                const result = await response.json();
                console.log('API Response:', result); // Log for debugging
                if (result.error) {
                    errorDiv.textContent = 'Failed to fetch books: ' + result.error;
                    errorDiv.classList.remove('hidden');
                    return;
                }
                const tableBody = document.getElementById('books-table');
                tableBody.innerHTML = '';
                if (result.books.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="p-2 text-center">No books found</td></tr>';
                } else {
                    result.books.forEach(book => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td class="p-2">${book.Title || 'N/A'}</td>
                            <td class="p-2">${book.Authors || 'Unknown'}</td>
                            <td class="p-2">${book.Genres || 'Unknown'}</td>
                            <td class="p-2">${book.ISBN || 'N/A'}</td>
                            <td class="p-2">${book.PublicationYear || 'N/A'}</td>
                            <td class="p-2">${book.TotalCopies || 0}</td>
                            <td class="p-2">
                                <a href="edit_book.php?id=${book.ID}" class="bg-yellow-500 text-white px-2 py-1 rounded mr-1">Edit</a>
                                <button onclick="deleteBook(${book.ID})" class="bg-red-500 text-white px-2 py-1 rounded mr-1">Delete</button>
                                <a href="add_copy.php?id=${book.ID}" class="bg-green-500 text-white px-2 py-1 rounded">Add Copy</a>
                            </td>
                        `;
                        tableBody.appendChild(row);
                    });
                }

                // Update pagination
                const totalBooks = result.totalBooks || 0;
                const totalPages = Math.ceil(totalBooks / booksPerPage);
                document.getElementById('current-page').textContent = page;
                document.getElementById('total-pages').textContent = totalPages;
                document.getElementById('prev-page').disabled = page <= 1;
                document.getElementById('next-page').disabled = page >= totalPages;

                errorDiv.classList.add('hidden');
            } catch (error) {
                console.error('Fetch Error:', error);
                errorDiv.textContent = 'Error loading books: ' + error.message;
                errorDiv.classList.remove('hidden');
            }
        }

        async function deleteBook(bookId) {
            if (confirm('Are you sure you want to delete this book?')) {
                try {
                    const response = await fetch(`api/delete_book.php?id=${bookId}`, {
                        method: 'DELETE',
                        headers: { 'Accept': 'application/json' }
                    });
                    const result = await response.json();
                    if (result.error) {
                        alert('Failed to delete book: ' + result.error);
                    } else {
                        alert('Book deleted successfully');
                        loadBooks(currentPage);
                    }
                } catch (error) {
                    alert('Error deleting book: ' + error.message);
                }
            }
        }

        document.getElementById('search-button').addEventListener('click', () => {
            currentPage = 1;
            const search = document.getElementById('search').value.trim();
            loadBooks(currentPage, search);
        });

        document.getElementById('search').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                currentPage = 1;
                const search = document.getElementById('search').value.trim();
                loadBooks(currentPage, search);
            }
        });

        document.getElementById('prev-page').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                const search = document.getElementById('search').value.trim();
                loadBooks(currentPage, search);
            }
        });

        document.getElementById('next-page').addEventListener('click', () => {
            const totalPages = parseInt(document.getElementById('total-pages').textContent);
            if (currentPage < totalPages) {
                currentPage++;
                const search = document.getElementById('search').value.trim();
                loadBooks(currentPage, search);
            }
        });

        document.addEventListener('DOMContentLoaded', () => loadBooks(currentPage));
    </script>
</body>
</html>