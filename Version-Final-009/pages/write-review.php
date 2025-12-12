<?php
// -----------------------------------------------------------
// write-review.php (User-facing review form)
// -----------------------------------------------------------

require_once __DIR__ . '/../includes/DatabaseConnector.php';

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

// -----------------------------------------------------------
// FETCH MEDIA TITLES (dynamic dropdown)
// -----------------------------------------------------------

$mediaOptions = [];

try {
    $stmt = $pdo->query("SELECT media_id, title FROM media ORDER BY title ASC");
    $mediaOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // If this fails, we'll just show an empty dropdown with a message.
    $mediaOptions = [];
}

// Set page title for header.php (optional)
$pageTitle = 'Write a Review';

?>
<section class="form-page-container">
    <h2 class="page-title">Write a Review</h2>

    <form id="writeReviewForm" class="review-form">

        <!-- Media Title -->
        <label for="media_id">Media Title *</label>
        <select name="media_id" id="media_id" required>
            <option value="">-- Select Media --</option>
            <?php foreach ($mediaOptions as $media): ?>
                <option value="<?= (int)$media['media_id']; ?>">
                    <?= htmlspecialchars($media['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Rating -->
        <label for="rating">Your Rating (0–10) *</label>
        <input
            type="number"
            name="rating"
            id="rating"
            min="0"
            max="10"
            step="1"
            required
        >

        <!-- Optional Review Text -->
        <label for="review">Review (optional)</label>
        <textarea
            name="review"
            id="review"
            rows="4"
            placeholder="Share your thoughts..."
        ></textarea>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn">Submit Review</button>

        <!-- Status / Feedback -->
        <p id="reviewStatus" class="status-msg"></p>

    </form>
</section>

<!-- JS -->
<script src="/assets/javascript/form-write-review.js"></script>
