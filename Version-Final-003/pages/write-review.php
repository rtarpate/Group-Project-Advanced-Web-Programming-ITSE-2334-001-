<?php
// -----------------------------------------------------------
// write-review.php (User-facing review form)
// -----------------------------------------------------------

// Load database connection
require_once __DIR__ . '/../includes/DatabaseConnector.php';

// Get PDO connection
$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

// -----------------------------------------------------------
// FETCH MEDIA TITLES (dynamic dropdown)
// -----------------------------------------------------------
try {
    $stmt = $pdo->query("SELECT media_id, title FROM media ORDER BY title ASC");
    $mediaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mediaList = [];
}

// Include shared header
include __DIR__ . '/../includes/header.php';
?>

<section class="form-page-container">

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

        <!-- USER RATING FIELD -->
        <label for="rating">Your Rating (0–10) *</label>
        <input 
            type="number" 
            id="rating" 
            name="rating" 
            min="0" 
            max="10" 
            required
        >

        <!-- USER REVIEW FIELD -->
        <label for="review_text">Review (optional)</label>
        <textarea 
            id="review_text" 
            name="review_text" 
            rows="4"
            placeholder="Share your thoughts..."
        ></textarea>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn">Submit Review</button>

        <!-- Feedback -->
        <p id="reviewStatus" class="status-msg"></p>

    </form>

</section>

<!-- JS -->
<script src="/assets/javascript/form-write-review.js"></script>

<?php
// Footer
include __DIR__ . '/../includes/footer.php';
?>
