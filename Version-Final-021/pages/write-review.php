<?php
// pages/write-review.php
// Header & footer are handled by index.php
?>

<h1>Write a Review</h1>

<form
    id="writeReviewForm"
    method="POST"
    action="/router/router.php?action=submitReview"
    class="review-form"
>

    <!-- Media Title -->
    <label for="media_id">Media Title *</label>
    <select id="media_id" name="media_id" required>
        <option value="">-- Select Media --</option>
    </select>

    <!-- Rating -->
    <label for="rating">Your Rating (0–10) *</label>
    <input
        id="rating"
        type="number"
        name="rating"
        min="0"
        max="10"
        step="1"
        required
    >

    <!-- Review Text -->
    <label for="review_text">Review (optional)</label>
    <textarea
        id="review_text"
        name="review"
        placeholder="Share your thoughts..."
    ></textarea>

    <!-- Submit -->
    <button type="submit">Submit Review</button>

    <!-- Status Message -->
    <p
        id="reviewStatus"
        class="form-status"
        style="display:none;"
    ></p>

</form>

<!-- Page-specific JavaScript -->
<script src="/assets/javascript/load-media-dropdown.js" defer></script>
<script src="/assets/javascript/form-write-review.js" defer></script>
