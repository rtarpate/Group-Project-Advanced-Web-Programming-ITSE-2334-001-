<?php
// Load database connection
require_once __DIR__ . '/../includes/DatabaseConnector.php';

// Get PDO connection using your class-based connector
$pdo = DatabaseConnector::getConnection();

if (!$pdo) {
    die("Database connection failed.");
}

// Fetch media list safely
try {
    $stmt = $pdo->query("SELECT media_id, title FROM media ORDER BY title ASC");
    $mediaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mediaList = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Write a Review - Star Media Review</title>

    <link rel="stylesheet" href="/groupproject/Version-035/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-035/assets/css/write-review.css">
</head>

<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="form-page-container">

    <h1 class="page-title">Write a Review</h1>

    <form id="writeReviewForm" class="review-form">

        <!-- Media Dropdown -->
        <label for="media_id">Media Title *</label>
        <select id="media_id" name="media_id" required>
            <option value="">-- Select Media --</option>
            <?php foreach ($mediaList as $media): ?>
                <option value="<?= htmlspecialchars($media['media_id']); ?>">
                    <?= htmlspecialchars($media['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Rating -->
        <label for="rating">Rating (0–10) *</label>
        <input type="number" id="rating" name="rating" min="0" max="10" required>

        <!-- Review Text -->
        <label for="review_text">Review (optional)</label>
        <textarea id="review_text" name="review_text" rows="4" placeholder="Share your thoughts..."></textarea>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn">Submit Review</button>

        <!-- Feedback -->
        <p id="reviewStatus" class="status-msg"></p>

    </form>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script src="/groupproject/Version-035/assets/javascript/form-write-review.js"></script>

</body>
</html>
