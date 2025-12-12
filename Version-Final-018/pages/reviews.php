<?php
// pages/reviews.php
?>

<section class="reviews-page">
    <h1>Media Reviews</h1>

    <div class="review-controls">
        <input
            type="text"
            id="searchInput"
            placeholder="Search titles..."
        >

        <select id="typeFilter">
            <!-- populated by JS -->
        </select>

        <select id="sortOrder">
            <option value="title_asc">Title (A–Z)</option>
            <option value="title_desc">Title (Z–A)</option>
        </select>
    </div>

    <div id="mediaContainer" class="media-grid"></div>
</section>

<script src="/assets/javascript/load-media-types.js"></script>
<script src="/assets/javascript/reviews.js"></script>
