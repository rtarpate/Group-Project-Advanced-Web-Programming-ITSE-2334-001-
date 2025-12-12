<?php
// pages/reviews.php
?>

<h1>Media Reviews</h1>

<div class="filters">
    <input type="text" id="searchInput" placeholder="Search titles…">
    <select id="typeFilter">
        <option value="">All Types</option>
    </select>
    <select id="sortOrder">
        <option value="title_asc">Title (A–Z)</option>
        <option value="title_desc">Title (Z–A)</option>
    </select>
</div>

<div id="mediaContainer"></div>

<script src="/assets/javascript/load-media-types.js"></script>
<script src="/assets/javascript/reviews.js"></script>
