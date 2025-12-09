<?php
//------------------------------------------------------
// admin-dashboard.php (FINAL VERSION)
//------------------------------------------------------
session_start();
require_once __DIR__ . '/admin-session.php';
require_admin_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Star Media Review</title>
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/admin-style.css">
</head>
<body class="admin-body">
<div class="admin-wrapper">

    <header class="admin-header">
        <div class="admin-header-inner">
            <h1>Admin Dashboard</h1>
            <?php include __DIR__ . '/admin-nav.php'; ?>
        </div>
    </header>

    <main class="admin-main">
        <section class="admin-panel">
            <h2>Welcome</h2>

            <p>
                Welcome back,
                <strong><?php echo htmlspecialchars(get_admin_name()); ?></strong>!
                This is your administrative control panel for the Star Media Review website.
            </p>

            <ul>
                <li><strong>Add New Media:</strong> Upload new movies, shows, games, manga, comics, novels, etc.</li>
                <li><strong>Manage Existing Media:</strong> Edit or delete media already in the database.</li>
                <li><strong>Manage Genres &amp; Types:</strong> Add, rename, or delete genres, media types, and content ratings.</li>
                <li><strong>Media Requests:</strong> Review and approve submitted new-media requests from users.</li>
                <li><strong>User Reviews:</strong> Moderate or remove inappropriate reviews submitted by users.</li>
            </ul>
        </section>
    </main>

    <footer class="admin-footer">
        <p>&copy; <?php echo date("Y"); ?> Star Media Review — Admin Control Panel</p>
    </footer>
</div>
</body>
</html>
