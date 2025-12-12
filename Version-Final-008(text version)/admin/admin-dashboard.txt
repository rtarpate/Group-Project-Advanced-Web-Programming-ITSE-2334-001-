<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/admin-session.php';

require_admin();

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

$counts = [
    'media'    => 0,
    'requests' => 0,
    'reviews'  => 0,
    'types'    => 0,
    'genres'   => 0
];

try {
    $counts['media']    = (int)$pdo->query("SELECT COUNT(*) FROM media")->fetchColumn();
    $counts['requests'] = (int)$pdo->query("SELECT COUNT(*) FROM newmediarequest")->fetchColumn();
    $counts['reviews']  = (int)$pdo->query("SELECT COUNT(*) FROM user_reviews")->fetchColumn();
    $counts['types']    = (int)$pdo->query("SELECT COUNT(*) FROM media_types")->fetchColumn();
    $counts['genres']   = (int)$pdo->query("SELECT COUNT(*) FROM genres")->fetchColumn();
} catch (Exception $e) {
    // You could log errors here if desired.
}

$adminName = $_SESSION['admin_name'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Star Media Review</title>
    <link rel="stylesheet" href="/assets/css/admin-style.css">
</head>
<body>

<?php include __DIR__ . '/admin-nav.php'; ?>

<main class="admin-content">

    <h2 class="admin-title">ADMIN DASHBOARD</h2>
    <p class="admin-subtitle">Welcome, <?= htmlspecialchars($adminName); ?>.</p>

    <section class="admin-panel">
        <h3>Overview</h3>
        <p>This panel gives you a quick snapshot of the Star Media Review database.</p>
        <ul>
            <li><strong>Total Media Items:</strong> <?= $counts['media']; ?></li>
            <li><strong>New Media Requests:</strong> <?= $counts['requests']; ?></li>
            <li><strong>User Reviews:</strong> <?= $counts['reviews']; ?></li>
            <li><strong>Media Types:</strong> <?= $counts['types']; ?></li>
            <li><strong>Genres:</strong> <?= $counts['genres']; ?></li>
        </ul>
    </section>

</main>

<footer class="admin-footer">
    © <?= date('Y'); ?> Star Media Review — Admin Panel
</footer>

</body>
</html>
