<?php
// pages/write-review.php
?>

<h1>Write a Review</h1>

<form method="POST" action="/router/router.php?action=submitReview" class="review-form">
    <label>Media Title *</label>
    <select name="media_id" id="mediaSelect" required>
        <option value="">-- Select Media --</option>
    </select>

    <label>Your Rating (0–10) *</label>
    <input type="number" name="rating" min="0" max="10" step="1" required>

    <label>Review (optional)</label>
    <textarea name="review"></textarea>

    <button type="submit">Submit Review</button>
</form>

<script src="/assets/javascript/load-media-dropdown.js"></script>
