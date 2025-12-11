<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <h1 class="page-title">Write a Review</h1>

    <p class="page-intro">
        Choose a piece of media from the list, give it a rating from 0–10, and (optionally) share your thoughts.
    </p>

    <div id="form-message" class="form-message"></div>

    <form id="write-review-form" class="styled-form">
        <div class="form-row">
            <label for="media_id">Media Title <span class="required">*</span></label>
            <select name="media_id" id="media_id" required>
                <option value="">Loading titles...</option>
            </select>
        </div>

        <div class="form-row">
            <label for="rating">Rating (0–10) <span class="required">*</span></label>
            <input type="number" name="rating" id="rating" min="0" max="10" step="1" required>
        </div>

        <div class="form-row">
            <label for="review">Review <span class="optional">(optional)</span></label>
            <textarea name="review" id="review" rows="4" placeholder="Share your thoughts (optional)"></textarea>
        </div>

        <div class="form-row">
            <button type="submit" class="primary-btn">Submit Review</button>
        </div>
    </form>
</section>

<script src="../assets/javascript/form-write-review.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
