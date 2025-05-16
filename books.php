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
    <title>Books - Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/scripts.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'navbar_member.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Books</h1>
        <input type="text" id="search" placeholder="Search books..." class="border p-2 mb-4 w-full">
        <div id="books-grid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
        <div class="mt-4 flex justify-between">
            <button id="prev" class="bg-blue-500 text-white px-4 py-2 rounded" disabled>Previous</button>
            <button id="next" class="bg-blue-500 text-white px-4 py-2 rounded" disabled>Next</button>
        </div>
        <div id="error-message" class="text-red-500 mt-4 hidden"></div>
    </div>
    <script>
        let offset = 0;
        const limit = 12;
        async function loadBooks() {
            const search = document.getElementById('search').value.trim();
            const errorDiv = document.getElementById('error-message');
            try {
                const response = await fetch(`api/books.php?search=${encodeURIComponent(search)}&offset=${offset}&limit=${limit}`);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                const result = await response.json();
                if (result.error) {
                    errorDiv.textContent = 'Failed to load books: ' + result.error;
                    errorDiv.classList.remove('hidden');
                    console.error('API error:', result.error);
                    return;
                }
                const grid = document.getElementById('books-grid');
                grid.innerHTML = '';
                if (result.books.length === 0) {
                    grid.innerHTML = '<p class="text-gray-500">No books found.</p>';
                } else {
                    result.books.forEach(book => {
                        const div = document.createElement('div');
                        div.className = 'bg-white p-4 shadow rounded';
                        div.innerHTML = `
                            <h2 class="text-xl font-bold">${book.Title || 'N/A'}</h2>
                            <p><strong>Author(s):</strong> ${book.Authors || 'Unknown'}</p>
                            <p><strong>Genre(s):</strong> ${book.Genres || 'Unknown'}</p>
                            <p><strong>ISBN:</strong> ${book.ISBN || 'N/A'}</p>
                            <p><strong>Year:</strong> ${book.PublicationYear || 'N/A'}</p>
                            <a href="book_details.php?id=${book.ID}" class="text-blue-500">View Details</a>
                        `;
                        grid.appendChild(div);
                    });
                }
                document.getElementById('prev').disabled = offset === 0;
                document.getElementById('next').disabled = offset + limit >= result.totalBooks;
                errorDiv.classList.add('hidden');
            } catch (error) {
                errorDiv.textContent = 'Error loading books: ' + error.message;
                errorDiv.classList.remove('hidden');
                console.error('Fetch error:', error);
            }
        }
        document.getElementById('search').addEventListener('input', () => {
            offset = 0;
            loadBooks();
        });
        document.getElementById('prev').addEventListener('click', () => {
            offset = Math.max(0, offset - limit);
            loadBooks();
        });
        document.getElementById('next').addEventListener('click', () => {
            offset += limit;
            loadBooks();
        });
        document.addEventListener('DOMContentLoaded', loadBooks);
    </script>
</body>
</html>