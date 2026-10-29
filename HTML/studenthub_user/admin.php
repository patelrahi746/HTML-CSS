<?php
// admin.php – Admin dashboard with RBAC protection
session_start();
if (!isset($_SESSION['user_id'])) {
    // Not logged in – redirect to login
    header('Location: login.html');
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    // Not an admin – show error or redirect
    echo '<p>Access denied. Admins only.</p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard – StudentHub</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="CSS/admin.css">
    <script src="js/main.js" defer></script>
    <script src="js/student.js" defer></script>
    <script src="js/event.js" defer></script>
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="#students">Students</a> |
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="#events">Events</a> |
                <a href="#stats">Statistics</a> |
            <?php endif; ?>
            <a href="index.html">Home</a>
        </nav>
    </header>

    <main>
        <!-- Statistics Section (admin only) -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <section id="stats">
            <h2>Statistics</h2>
            <div class="stats-grid" id="stats-container">
                <div class="stat-card"><strong>Total Students:</strong> <span id="stat-students">…</span></div>
                <div class="stat-card"><strong>Total Events:</strong> <span id="stat-events">…</span></div>
                <div class="stat-card"><strong>Total Registrations:</strong> <span id="stat-registrations">…</span></div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Student Management Section -->
        <section id="students">
            <h2>Student Management</h2>
            <div id="student-container"></div>
        </section>

        <!-- Event Management Section (admin only) -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <section id="events">
            <h2>Event Management</h2>
            <button id="add-event-btn">Add Event</button>
            <table id="event-table" class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Poster</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </section>
        <?php endif; ?>
    </main>

    <!-- Event Modal (admin only) -->
    <?php if ($_SESSION['role'] === 'admin'): ?>
    <div id="event-modal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" id="close-event-modal">&times;</span>
            <h3 id="modal-title">Add Event</h3>
            <form id="event-form" enctype="multipart/form-data">
                <input type="hidden" name="id" id="event-id">
                <label>Title:<br>
                    <input type="text" name="title" id="event-title" required>
                </label><br>
                <label>Date:<br>
                    <input type="date" name="event_date" id="event-date" required>
                </label><br>
                <label>Description:<br>
                    <textarea name="description" id="event-description" rows="4"></textarea>
                </label><br>
                <label>Poster (optional):<br>
                    <input type="file" name="poster" id="event-poster" accept="image/*">
                </label><br>
                <button type="submit" id="event-submit">Save</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script>
    // Load statistics (admin only)
    <?php if ($_SESSION['role'] === 'admin'): ?>
    fetch('backend/registration_stats.php')
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('stat-students').textContent = data.stats.students;
                document.getElementById('stat-events').textContent = data.stats.events;
                document.getElementById('stat-registrations').textContent = data.stats.registrations;
            }
        })
        .catch(() => {});
    <?php endif; ?>
    </script>
</body>
</html>
