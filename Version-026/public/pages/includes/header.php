<?php
/**
 * Global Header for Star Media Reviews
 * Shared across all pages
 */
?>

<header>
    <h1>Star Media Reviews</h1>

    <nav>
        <ul>
            <a href="index.html" class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.html' ? 'active' : ''; ?>">Home</a>
            <a href="reviews.html" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reviews.html' ? 'active' : ''; ?>">Reviews</a>
            <a href="write-review.html" class="<?php echo basename($_SERVER['PHP_SELF']) === 'write-review.html' ? 'active' : ''; ?>">Write a Review</a>
            <a href="request-new-media.html" class="<?php echo basename($_SERVER['PHP_SELF']) === 'request-new-media.html' ? 'active' : ''; ?>">Request New Media</a>
        </ul>
    </nav>
</header>
