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
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<header>
    <h1>Admin Dashboard</h1>

    <nav>
        <a href="admin-dashboard.php" class="active">Dashboard</a>
        <a href="admin-add-media.php">Add New Media</a>
        <a href="manage-media.php">Manage Existing Media</a>
        <a href="manage-taxonomy.php">Manage Genres & Media Types</a>
        <a href="admin-manage-requests.php">Manage Media Requests</a>
        <a href="admin-manage-reviews.php">Manage User Reviews</a>
        <a href="../admin/admin-logout.php">Logout</a>
        <a href="../index.html">Return to Website</a>
    </nav>
</header>

<main class="content">

    <h2>Welcome back, <?php echo htmlspecialchars(get_admin_name()); ?>!</h2>

    <p>This is your administrative control panel for the Star Media Review website.</p>

    <ul>
        <li><strong>Add New Media:</strong> Upload new movies, shows, games, manga, comics, novels, etc.</li>
        <li><strong>Manage Existing Media:</strong> Edit or delete media already in the database.</li>
        <li><strong>Manage Genres & Types:</strong> Add, rename, or delete genres, media types, and content ratings.</li>
        <li><strong>Media Requests:</strong> Review and approve submitted new-media requests from users.</li>
        <li><strong>User Reviews:</strong> Moderate or remove inappropriate reviews submitted by users.</li>
    </ul>

</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Star Media Review — Admin Control Panel</p>
</footer>

</body>
</html>
