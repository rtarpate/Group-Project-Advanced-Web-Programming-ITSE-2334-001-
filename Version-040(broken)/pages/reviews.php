<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <h1 class="page-title">Browse All Reviews</h1>

    <div class="reviews-controls">
        <div class="search-group">
            <label for="search-bar">Search</label>
            <input type="text" id="search-bar" placeholder="Search by title or description">
        </div>

        <div class="filter-group">
            <label for="type-filter">Type</label>
            <select id="type-filter">
                <option value="all">All Types</option>
                <option value="Movie">Movie</option>
                <option value="TV Show">TV Show</option>
                <option value="Video Game">Video Game</option>
                <option value="Comic Book">Comic Book</option>
                <option value="Manga">Manga</option>
                <option value="Novel">Novel</option>
                <option value="Web Novel">Web Novel</option>
                <option value="Web Series">Web Series</option>
                <option value="Audio Book">Audio Book</option>
            </select>
        </div>

        <div class="sort-group">
            <label for="sort-filter">Sort</label>
            <select id="sort-filter">
                <option value="title-asc">Title (A–Z)</option>
                <option value="title-desc">Title (Z–A)</option>
                <option value="rating-desc">Rating (High → Low)</option>
                <option value="rating-asc">Rating (Low → High)</option>
            </select>
        </div>
    </div>

    <div id="reviews-container" class="reviews-grid">
        <!-- Cards will be injected here by reviews.js -->
    </div>
</section>

<script src="../assets/javascript/reviews.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
