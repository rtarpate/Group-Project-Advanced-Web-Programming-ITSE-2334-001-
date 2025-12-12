<?php
// reviews.php — loaded via index.php?page=reviews

require_once __DIR__ . '/../includes/DatabaseConnector.php';

$pdo = DatabaseConnector::getConnection();

// -----------------------------------------------------------
// FETCH FILTER DATA FROM DATABASE (PUBLIC SITE)
// Only "Types" are currently used as filters.
// Genres and ratings removed from UI but kept available.
// -----------------------------------------------------------

$types = [];

try {
    $types = $pdo->query("
        SELECT media_type_id, type_name
        FROM media_types
        ORDER BY type_name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $types = [];
}

// Load header
include __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Media Reviews</h1>

<section class="reviews-controls">

    <!-- Search Bar -->
    <input type="text" id="search-bar" placeholder="Search titles…">

    <!-- MEDIA TYPE FILTER (Dynamic) -->
    <select id="type-filter">
        <option value="all">All Types</option>
        <?php foreach ($types as $t): ?>
            <option value="<?= htmlspecialchars($t['type_name']); ?>">
                <?= htmlspecialchars($t['type_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- SORT FILTER -->
    <select id="sort-filter">
    <option value="title-asc">Title (A–Z)</option>
    <option value="title-desc">Title (Z–A)</option>
    <option value="rating-desc">Rating (High–Low)</option>
    <option value="rating-asc">Rating (Low–High)</option>
</select>


</section>

<!-- MEDIA DISPLAY GRID -->
<section id="reviews-container" class="reviews-grid"></section>

<!-- Frontend Review Logic -->
<script src="/assets/javascript/reviews.js"></script>

