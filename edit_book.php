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

// Fetch book details
$stmt = $pdo->prepare('
    SELECT b.ID, b.Title, b.ISBN, b.PublicationYear,
           GROUP_CONCAT(wb.Author_ID) AS AuthorIDs,
           GROUP_CONCAT(bg.Genre_ID) AS GenreIDs
    FROM Book b
    LEFT JOIN WrittenBy wb ON b.ID = wb.Book_ID
    LEFT JOIN BookGenre bg ON b.ID = bg.Book_ID
    WHERE b.ID = ?
    GROUP BY b.ID
');
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$book) {
    header('Location: dashboard.php');
    exit;
}

// Fetch all authors and genres
$stmt = $pdo->query('SELECT ID, Name FROM Author ORDER BY Name');
$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->query('SELECT ID, Name FROM Genre ORDER BY Name');
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selected_authors = array_filter(explode(',', $book['AuthorIDs'] ?? ''));
$selected_genres = array_filter(explode(',', $book['GenreIDs'] ?? ''));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book - Library</title>
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
        <h1 class="text-2xl font-bold mb-4">Edit Book</h1>
        <form id="edit-book-form" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="title" class="block text-sm font-medium">Title</label>
                <input type="text" id="title" value="<?php echo htmlspecialchars($book['Title']); ?>" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="isbn" class="block text-sm font-medium">ISBN</label>
                <input type="text" id="isbn" value="<?php echo htmlspecialchars($book['ISBN']); ?>" class="border p-2 w-full" required>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium">Publication Year</label>
                <input type="number" id="year" value="<?php echo htmlspecialchars($book['PublicationYear']); ?>" class="border p-2 w-full" min="0" required>
            </div>
            <div>
                <label for="authors" class="block text-sm font-medium">Author(s) (Hold Cmd/Ctrl to select multiple)</label>
                <select id="authors" multiple class="border p-2 w-full" required>
                    <?php foreach ($authors as $author): ?>
                        <option value="<?php echo $author['ID']; ?>" <?php echo in_array($author['ID'], $selected_authors) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($author['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="genres" class="block text-sm font-medium">Genre(s) (Hold Cmd/Ctrl to select multiple)</label>
                <select id="genres" multiple class="border p-2 w-full" required>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?php echo $genre['ID']; ?>" <?php echo in_array($genre['ID'], $selected_genres) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($genre['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Save Changes</button>
            </div>
        </form>
        <div id="error-message" class="text-red-500 mt-4 hidden"></div>
    </div>
    <script>
        document.getElementById('edit-book-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorDiv = document.getElementById('error-message');
            const book_id = <?php echo $book_id; ?>;
            const title = document.getElementById('title').value.trim();
            const isbn = document.getElementById('isbn').value.trim();
            const year = parseInt(document.getElementById('year').value);
            const authors = Array.from(document.getElementById('authors').selectedOptions).map(opt => opt.value);
            const genres = Array.from(document.getElementById('genres').selectedOptions).map(opt => opt.value);
            if (!title || !isbn || !year || authors.length === 0 || genres.length === 0) {
                errorDiv.textContent = 'All fields are required';
                errorDiv.classList.remove('hidden');
                return;
            }
            try {
                const response = await fetch('api/edit_book.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ book_id, title, isbn, year, authors, genres })
                });
                const result = await response.json();
                if (result.error) {
                    errorDiv.textContent = 'Failed to update book: ' + result.error;
                    errorDiv.classList.remove('hidden');
                    console.error('API error:', result.error);
                } else {
                    alert('Book updated successfully!');
                    window.location.href = 'dashboard.php';
                }
            } catch (error) {
                errorDiv.textContent = 'Error updating book: ' + error.message;
                errorDiv.classList.remove('hidden');
                console.error('Fetch error:', error);
            }
        });
    </script>
</body>
</html>