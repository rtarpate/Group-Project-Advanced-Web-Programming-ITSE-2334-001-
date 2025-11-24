<?php
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/starstarmediareviewdatabase.php';
require_once __DIR__ . '/../../assets/logs/ErrorLogger.php';

$success = '';
$error   = '';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_review_id'])) {
    $reviewId = (int) $_POST['delete_review_id'];

    if ($reviewId > 0) {
        if (StarStarMediaDatabase::deleteUserReview($reviewId)) {
            $success = 'Review deleted and ratings updated.';
        } else {
            $error = 'Failed to delete review.';
        }
    }
}

$reviews = StarStarMediaDatabase::getAllUserReviewsWithMedia();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage User Reviews - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<header>
    <h1>Admin - Manage User Reviews</h1>
</header>

<div class="container">
    <p><a href="admin-dashboard.php">&larr; Back to Dashboard</a></p>

    <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (empty($reviews)): ?>
        <p>No reviews found.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Media</th>
                <th>Rating</th>
                <th>Review Text</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($reviews as $rv): ?>
                <tr>
                    <td><?php echo (int)$rv['review_id']; ?></td>
                    <td><?php echo htmlspecialchars($rv['title']); ?></td>
                    <td><?php echo (int)$rv['rating']; ?></td>
                    <td><?php echo nl2br(htmlspecialchars($rv['review_text'])); ?></td>
                    <td><?php echo htmlspecialchars($rv['review_date']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_review_id" value="<?php echo (int)$rv['review_id']; ?>">
                            <button type="submit" onclick="return confirm('Delete this review?');">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
