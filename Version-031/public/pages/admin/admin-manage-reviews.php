<?php
// admin-manage-reviews.php

require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../../assets/logs/ErrorLogger.php';

$success = '';
$error   = '';

try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    ErrorLogger::log("DB connection failed in admin-manage-reviews: " . $e->getMessage());
    $error = 'Database connection error.';
    $pdo = null;
}

// Handle delete
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])) {

    $reviewId = (int) $_POST['delete_review_id'];

    try {
        $pdo->beginTransaction();

        // Find which media the review belongs to
        $select = $pdo->prepare("SELECT media_id FROM user_reviews WHERE review_id = :id");
        $select->execute([':id' => $reviewId]);
        $review = $select->fetch(PDO::FETCH_ASSOC);

        if ($review) {
            $mediaId = (int) $review['media_id'];

            // Delete review
            $del = $pdo->prepare("DELETE FROM user_reviews WHERE review_id = :id");
            $del->execute([':id' => $reviewId]);

            // Recalculate ratings
            $agg = $pdo->prepare("
                SELECT COUNT(*) AS count, COALESCE(AVG(rating), 0) AS avg 
                FROM user_reviews 
                WHERE media_id = :mid
            ");
            $agg->execute([':mid' => $mediaId]);
            $stats = $agg->fetch(PDO::FETCH_ASSOC);

            $count = (int) $stats['count'];
            $avg   = (float) $stats['avg'];

            // Update media_ratings
            $upd = $pdo->prepare("
                INSERT INTO media_ratings (media_id, average_rating, total_ratings)
                VALUES (:mid, :avg, :cnt)
                ON DUPLICATE KEY UPDATE 
                    average_rating = VALUES(average_rating),
                    total_ratings = VALUES(total_ratings)
            ");
            $upd->execute([
                ':mid' => $mediaId,
                ':avg' => $avg,
                ':cnt' => $count,
            ]);

            $pdo->commit();
            $success = "Review deleted and rating updated.";

        } else {
            $pdo->rollBack();
            $error = "Review not found.";
        }

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        ErrorLogger::log("Error deleting review: " . $e->getMessage());
        $error = "Failed to delete review.";
    }
}

// Fetch all reviews
$reviews = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT r.review_id, m.title, r.rating, r.review_text, r.review_date
            FROM user_reviews r
            INNER JOIN media m ON r.media_id = m.media_id
            ORDER BY r.review_date DESC
        ");
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (Throwable $e) {
        ErrorLogger::log("Error loading reviews: " . $e->getMessage());
        $error = "Failed to load reviews.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Reviews</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<h1>Manage User Reviews</h1>

<?php if ($success): ?>
<p class="success-message"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
<p class="error-message"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Media</th>
        <th>Rating</th>
        <th>Review</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>

<?php foreach ($reviews as $rv): ?>
<tr>
    <td><?= $rv['review_id'] ?></td>
    <td><?= htmlspecialchars($rv['title']) ?></td>
    <td><?= $rv['rating'] ?></td>
    <td><?= nl2br(htmlspecialchars($rv['review_text'])) ?></td>
    <td><?= $rv['review_date'] ?></td>
    <td>
        <form method="post">
            <input type="hidden" name="delete_review_id" value="<?= $rv['review_id'] ?>">
            <button type="submit">Delete</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>

<?php include '../includes/footer.php'; ?>

</body>
</html>
