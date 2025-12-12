<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/admin-session.php';

require_admin();

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

$reviews = [];

// Handle delete review request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])) {
    $deleteId = (int)$_POST['delete_review_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM user_reviews WHERE review_id = :id");
        $stmt->execute([':id' => $deleteId]);

        $statusMessage = "Review #{$deleteId} deleted successfully.";
        $statusType = "success";
    } catch (Exception $e) {
        $statusMessage = "Error deleting review: " . $e->getMessage();
        $statusType = "error";
    }
}


try {
    $sql = "
        SELECT 
            r.review_id,
            r.media_id,
            r.rating,
            r.review_text,
            r.review_date,
            m.title AS media_title
        FROM user_reviews r
        LEFT JOIN media m ON r.media_id = m.media_id
        ORDER BY r.review_id DESC
    ";
    $stmt = $pdo->query($sql);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (Exception $e) {
    // optional: log
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage User Reviews - Admin</title>
    <link rel="stylesheet" href="/assets/css/admin-style.css">
</head>
<body>

<?php include __DIR__ . '/admin-nav.php'; ?>

<main class="admin-content">
    <h2 class="admin-title">MANAGE USER REVIEWS</h2>
    <p class="admin-subtitle">View all user-submitted reviews.</p>

    <section class="admin-panel">

        <?php if (!empty($statusMessage)) : ?>
            <p class="admin-status <?= $statusType === 'success' ? 'success' : 'error' ?>">
                <?= htmlspecialchars($statusMessage); ?>
            </p>
        <?php endif; ?>

        <h2>User Reviews</h2>
        <p>Below are all user-submitted reviews. Admins may delete inappropriate content.</p>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Media Title</th>
                        <th>Rating</th>
                        <th>Review Text</th>
                        <th>Review Date</th>
                        <th>User</th>
                        <th>Delete</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($reviews as $rev): ?>
                        <tr>
                            <td><?= htmlspecialchars($rev['review_id']); ?></td>
                            <td><?= htmlspecialchars($rev['media_title'] ?? 'Unknown'); ?></td>
                            <td><?= htmlspecialchars($rev['rating']); ?></td>
                            <td><?= htmlspecialchars($rev['review_text']); ?></td>
                            <td><?= htmlspecialchars($rev['review_date']); ?></td>

                            <!-- Anonymous users -->
                            <td>Anonymous</td>

                            <td>
                                <form method="post" 
                                    onsubmit="return confirm('Delete this review?');">
                                    <input type="hidden" name="delete_review_id" 
                                        value="<?= (int)$rev['review_id']; ?>">
                                    <button type="submit" 
                                            class="table-action-btn table-delete-btn">
                                        DELETE
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </section>



</main>

<footer class="admin-footer">
    © <?= date('Y'); ?> Star Media Review — Admin Panel
</footer>

</body>
</html>
