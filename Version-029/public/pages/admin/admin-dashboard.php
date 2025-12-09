<?php
require_once __DIR__ . '/admin-session.php';
require_admin_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Star Media Reviews</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<header>
    <h1>Star Media Reviews - Admin Dashboard</h1>
</header>

<div class="container">
    <p>Welcome, <?php echo htmlspecialchars(get_admin_name()); ?>!</p>

    <ul>
        <li><a href="admin-add-media.php">Add New Media</a></li>
        <li><a href="admin-manage-requests.php">Manage New Media Requests</a></li>
        <li><a href="admin-manage-reviews.php">Manage User Reviews</a></li>
        <li><a href="admin-logout.php">Log Out</a></li>
        <li><a href="../index.html">Back to Website Home</a></li>

    </ul>
</div>

</body>
</html>
