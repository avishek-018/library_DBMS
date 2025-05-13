async function handleLogin(event) {
    event.preventDefault();
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    try {
        const response = await fetch('api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });
        const result = await response.json();
        if (result.error) {
            alert('Login failed: ' + result.error);
        } else {
            window.location.href = result.Role === 'librarian' ? 'dashboard.php' : 'books.php';
        }
    } catch (error) {
        alert('Login error: ' + error.message);
    }
}

async function reserveBook(bookId) {
    try {
        const response = await fetch('api/reserve.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ book_id: bookId })
        });
        const result = await response.json();
        if (result.error) {
            alert('Failed to reserve book: ' + result.error);
            console.error('Reservation error:', result.error);
        } else {
            document.getElementById(`reserve-btn-${bookId}`).outerHTML = '<span class="text-yellow-500 font-bold">Reservation pending</span>';
            alert('Reservation pending!');
        }
    } catch (error) {
        alert('Reservation error: ' + error.message);
        console.error('Fetch error:', error);
    }
}

async function fetchReservations() {
    try {
        const response = await fetch('api/reservations.php');
        const result = await response.json();
        if (result.error) {
            console.error('Failed to fetch reservations:', result.error);
            return [];
        }
        return result.reservations || [];
    } catch (error) {
        console.error('Failed to fetch reservations:', error);
        return [];
    }
}

async function fetchReservationRequests() {
    try {
        const response = await fetch('api/reservation_requests.php');
        const result = await response.json();
        if (result.error) {
            console.error('Failed to fetch reservation requests:', result.error);
            return [];
        }
        return result.requests || [];
    } catch (error) {
        console.error('Failed to fetch reservation requests:', error);
        return [];
    }
}

async function checkoutReservation(reservationId) {
    try {
        const response = await fetch('api/checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reservation_id: reservationId })
        });
        const result = await response.json();
        if (result.error) {
            alert('Checkout failed: ' + result.error);
        } else {
            alert('Book checked out!');
            window.location.reload();
        }
    } catch (error) {
        alert('Checkout error: ' + error.message);
    }
}

async function returnReservation(reservationId) {
    try {
        const response = await fetch('api/return.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ reservation_id: reservationId })
        });
        const result = await response.json();
        if (result.error) {
            alert('Return failed: ' + result.error);
        } else {
            alert('Book returned!');
            window.location.reload();
        }
    } catch (error) {
        alert('Return error: ' + error.message);
    }
}

async function deleteBook(bookId) {
    if (!confirm('Are you sure you want to delete this book?')) return;
    try {
        const response = await fetch('api/delete_book.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ book_id: bookId })
        });
        const result = await response.json();
        if (result.error) {
            alert('Failed to delete book: ' + result.error);
        } else {
            alert('Book deleted successfully!');
            window.location.reload();
        }
    } catch (error) {
        alert('Error deleting book: ' + error.message);
    }
}