<?php
// pages/reviews.php
?>

<section class="page-section">
    <h2 class="page-title">Media Reviews</h2>

    <div class="reviews-controls">
        <input
            type="text"
            id="searchInput"
            placeholder="Search titles..."
        >

        <select id="typeFilter">
            <option value="">All Types</option>
        </select>

        <select id="sortOrder">
            <option value="title_asc">Title (A–Z)</option>
            <option value="title_desc">Title (Z–A)</option>
        </select>
    </div>

    <div id="mediaContainer" class="reviews-grid"></div>
</section>

<script src="/assets/javascript/load-media-types.js"></script>
<script src="/assets/javascript/reviews.js"></script>
