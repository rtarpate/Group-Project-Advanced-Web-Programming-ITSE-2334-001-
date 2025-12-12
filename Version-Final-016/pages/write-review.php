<?php
// pages/write-review.php
// Header & footer handled by index.php
?>

<h1>Write a Review</h1>

<form id="writeReviewForm" method="POST" action="/router/router.php?action=submitReview" class="review-form">
    <label for="mediaSelect">Media Title *</label>
    <select name="media_id" id="mediaSelect" required>
        <option value="">-- Select Media --</option>
    </select>

    <label for="ratingInput">Your Rating (0–10) *</label>
    <input id="ratingInput" type="number" name="rating" min="0" max="10" step="1" required>

    <label for="reviewText">Review (optional)</label>
    <textarea id="reviewText" name="review" placeholder="Share your thoughts..."></textarea>

    <button type="submit">Submit Review</button>

    <p id="reviewStatus" class="form-status" style="display:none;"></p>
</form>

<script src="/assets/javascript/load-media-dropdown.js"></script>
<script src="/assets/javascript/form-write-review.js"></script>
