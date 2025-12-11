<?php
// admin-manage-reviews.php

require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

$success = '';
$error   = '';

try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    ErrorLogger::log("DB connection failed in admin-manage-reviews: " . $e->getMessage());
    $error = "Database connection failed.";
    $pdo   = null;
}

if ($pdo) {
    // Delete review + update media_ratings
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])) {
        $reviewId = (int) $_POST['delete_review_id'];

        try {
            $pdo->beginTransaction();

            // Find media_id for this review
            $revStmt = $pdo->prepare("SELECT media_id FROM user_reviews WHERE review_id = :rid");
            $revStmt->execute([':rid' => $reviewId]);
            $reviewRow = $revStmt->fetch(PDO::FETCH_ASSOC);

            if ($reviewRow) {
                $mediaId = (int)$reviewRow['media_id'];

                // Delete the review
                $delStmt = $pdo->prepare("DELETE FROM user_reviews WHERE review_id = :rid");
                $delStmt->execute([':rid' => $reviewId]);

                // Recalculate average + count
                $agg = $pdo->prepare("
                    SELECT COUNT(*) AS count, AVG(rating) AS avg
                    FROM user_reviews
                    WHERE media_id = :mid
                ");
                $agg->execute([':mid' => $mediaId]);
                $stats = $agg->fetch(PDO::FETCH_ASSOC);

                $count = (int) $stats['count'];
                $avg   = $count > 0 ? (float)$stats['avg'] : 0.0;

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
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            ErrorLogger::log("Error deleting review: " . $e->getMessage());
            $error = "Failed to delete review.";
        }
    }

    // Load reviews list
    if ($error === '') {
        try {
            $stmt = $pdo->query("
                SELECT 
                    r.review_id,
                    r.media_id,
                    r.rating,
                    r.review_text,
                    r.review_date,
                    m.title
                FROM user_reviews AS r
                JOIN media AS m ON r.media_id = m.media_id
                ORDER BY r.review_date DESC
            ");
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Throwable $e) {
            ErrorLogger::log("Error fetching reviews: " . $e->getMessage());
            $error = "Failed to load user reviews.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Reviews</title>
    <link rel="stylesheet" href="/groupproject/Version-035/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-035/assets/css/admin-style.css">
</head>
<body class="admin-body">
<div class="admin-wrapper">

    <header class="admin-header">
        <div class="admin-header-inner">
            <h1>Admin - Manage User Reviews</h1>
            <?php include __DIR__ . '/admin-nav.php'; ?>
        </div>
    </header>

    <main class="admin-main">
        <section class="admin-panel">
            <h2>User Reviews</h2>

            <?php if ($success): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if (!empty($reviews)): ?>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Media</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($reviews as $rv): ?>
                            <tr>
                                <td><?= $rv['review_id'] ?></td>
                                <td><?= htmlspecialchars($rv['title']) ?></td>
                                <td><?= $rv['rating'] ?></td>
                                <td><?= nl2br(htmlspecialchars($rv['review_text'])) ?></td>
                                <td><?= $rv['review_date'] ?></td>
                                <td>
                                    <form method="post" style="margin:0;">
                                        <input type="hidden" name="delete_review_id" value="<?= $rv['review_id'] ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No reviews found.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="admin-footer">
        <p>&copy; <?php echo date('Y'); ?> Star Media Review — Admin Panel</p>
    </footer>
</div>
</body>
</html>
