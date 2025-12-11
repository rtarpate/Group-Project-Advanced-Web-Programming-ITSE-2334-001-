<?php
require_once __DIR__ . '/includes/header.php';

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'reviews':
        require __DIR__ . '/pages/reviews.php';
        break;

    case 'write-review':
        require __DIR__ . '/pages/write-review.php';
        break;

    case 'request-new-media':
        require __DIR__ . '/pages/request-new-media.php';
        break;

    case 'media-details':
        require __DIR__ . '/pages/media-details.php';
        break;

    default:
        echo "<h2 style='text-align:center;margin-top:40px;'>Welcome to Star Media Reviews!</h2>";
        break;
}

require_once __DIR__ . '/includes/footer.php';
