<?php
// admin-dashboard.php
// Main admin landing page

require_once __DIR__ . '/admin-session.php';
require_admin_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Star Media Review</title>

    <!-- Same note as before: adjust the base path if needed -->
    <link rel="stylesheet" href="/groupproject/Version-041/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-041/assets/css/admin-style.css">
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="admin-header-content">
            <h1>Admin Dashboard</h1>
            <div class="admin-header-user">
                Logged in as:
                <strong><?php echo htmlspecialchars(get_admin_name(), ENT_QUOTES, 'UTF-8'); ?></strong>
                &nbsp;|&nbsp;
                <a href="admin-logout.php">Logout</a>
                &nbsp;|&nbsp;
                <a href="/groupproject/Version-041/index.php">Return to Website</a>
            </div>
        </div>
    </header>

    <nav class="admin-nav">
        <?php require_once __DIR__ . '/admin-nav.php'; ?>
    </nav>

    <main class="admin-main">
        <section class="admin-section">
            <h2>Welcome to Star Media Review Admin Panel</h2>
            <p>
                Use the navigation above to manage media items, user reviews, and new media requests.
            </p>
        </section>
    </main>
</body>
</html>
