<?php
require_once __DIR__ . '/admin-session.php';
require_admin_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Star Media Review</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>

<?php include __DIR__ . '/admin-nav.php'; ?>

<main class="admin-main">
    <h1>Admin Dashboard</h1>

    <section class="admin-section">
        <h2>Manage Media</h2>
        <ul>
            <li><a href="manage-media.php">Manage Existing Media</a></li>
            <li><a href="admin-add-media.php">Add New Media</a></li>
            <li><a href="manage-taxonomy.php">Manage Types / Genres / Ratings</a></li>
        </ul>
    </section>

    <section class="admin-section">
        <h2>User Activity</h2>
        <ul>
            <li><a href="admin-manage-reviews.php">Manage User Reviews</a></li>
            <li><a href="admin-manage-requests.php">Manage New Media Requests</a></li>
        </ul>
    </section>

</main>

</body>
</html>
