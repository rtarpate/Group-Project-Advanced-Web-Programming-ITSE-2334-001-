<?php
// pages/write-review.php
?>

<h1>Write a Review</h1>

<form id="writeReviewForm" class="review-form">

    <label for="media_id">Media Title *</label>
    <select id="media_id" name="media_id" required>
        <option value="">-- Select Media --</option>
    </select>

    <label for="rating">Your Rating (0–10) *</label>
    <input
        id="rating"
        type="number"
        name="rating"
        min="0"
        max="10"
        required
    >

    <label for="review">Review (optional)</label>
    <textarea
        id="review"
        name="review"
        placeholder="Share your thoughts..."
    ></textarea>

    <button type="submit">Submit Review</button>

    <p id="reviewStatus" class="form-status" style="display:none;"></p>
</form>

<script src="/assets/javascript/load-media-dropdown.js"></script>
<script src="/assets/javascript/form-write-review.js"></script>
