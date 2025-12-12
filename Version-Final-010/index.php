<?php
// index.php — Front controller for public pages

$page = $_GET['page'] ?? 'home';

// Page title + page-specific CSS
$pageTitle = 'Star Media Review';
$extraCss  = [];

switch ($page) {
    case 'reviews':
        $pageTitle = 'Media Reviews';
        break;

    case 'write-review':
        $pageTitle = 'Write a Review';
        $extraCss[] = '/assets/css/write-review.css';
        break;

    case 'request-new-media':
        $pageTitle = 'Request New Media';
        $extraCss[] = '/assets/css/RequestMedia.css';
        break;

    case 'media-details':
        $pageTitle = 'Media Details';
        $extraCss[] = '/assets/css/media-details.css';
        break;

    default:
        $pageTitle = 'Home';
        break;
}

require_once __DIR__ . '/includes/header.php';

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
