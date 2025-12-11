<?php
// index.php – single public entry point

$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'reviews':
        // Reviews listing page
        require __DIR__ . '/pages/reviews.php';
        break;

    case 'write-review':
        // Write a new review
        require __DIR__ . '/pages/write-review.php';
        break;

    case 'request-new-media':
        // Request new media form
        require __DIR__ . '/pages/request-new-media.php';
        break;

    case 'media-details':
        // Show details for a single media item (expects ?media_id=123)
        require __DIR__ . '/pages/media-details.php';
        break;

    case 'admin-login':
        // Direct to admin login (full standalone admin layout)
        require __DIR__ . '/admin/admin-login.php';
        break;

    default:
        // HOME PAGE (uses shared header/footer)
        require __DIR__ . '/includes/header.php';
        ?>

        <!-- Decorative Star (click 5 times to reveal admin access) -->
        <div id="secret-star-container">
            <img src="/assets/images/0.jpg" id="secret-star" alt="Decorative Star">
        </div>

        <!-- Hidden admin button -->
        <button id="admin-reveal-button" style="display: none;">Admin Login</button>

        <!-- Secret admin script -->
        <script src="/assets/javascript/secret-admin.js"></script>

        <h1>Welcome to Star Media Review</h1>
        <p>Your source for media reviews — movies, TV shows, games, and more.</p>

        <?php
        require __DIR__ . '/includes/footer.php';
        break;
}
