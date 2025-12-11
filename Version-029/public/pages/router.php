<?php
// ----------------------------------------------------------
// router.php  (AJAX Router for public-facing JS)
// ----------------------------------------------------------

header('Content-Type: application/json');

require_once __DIR__ . '/includes/DatabaseConnector.php';
require_once __DIR__ . '/includes/starstarmediareviewdatabase.php';
require_once __DIR__ . '/includes/helper.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    ErrorLogger::log("Router - DB Connection Failed: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error'   => 'Database connection failed.'
    ]);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {

        // --------------------------------------
        // GET ALL MEDIA (for reviews.html, etc.)
        // --------------------------------------
        case 'getMedia':
            $media = StarStarMediaDatabase::getAllMediaWithRatings($pdo);
            echo json_encode([
                'success' => true,
                'media'   => $media
            ]);
            break;

        // --------------------------------------
        // GET MEDIA DETAILS (for media-details.html)
        // --------------------------------------
        case 'getMediaDetails':
            $mediaId = isset($_GET['media_id']) ? (int)$_GET['media_id'] : 0;
            if ($mediaId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Invalid media ID.']);
                break;
            }

            $details = StarStarMediaDatabase::getMediaDetails($pdo, $mediaId);
            if (!$details) {
                echo json_encode(['success' => false, 'error' => 'Media not found.']);
                break;
            }

            echo json_encode([
                'success' => true,
                'media'   => $details
            ]);
            break;

        // --------------------------------------
        // GET MEDIA REVIEWS (for media-details.html)
        // --------------------------------------
        case 'getMediaReviews':
            $mediaId = isset($_GET['media_id']) ? (int)$_GET['media_id'] : 0;
            if ($mediaId <= 0) {
                echo json_encode(['success' => false, 'error' => 'Invalid media ID.']);
                break;
            }

            $reviews = StarStarMediaDatabase::getMediaReviews($pdo, $mediaId);
            echo json_encode([
                'success' => true,
                'reviews' => $reviews
            ]);
            break;

        // --------------------------------------
        // SUBMIT REVIEW (from write-review.html)
        // --------------------------------------
        case 'submitReview':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
                break;
            }

            $mediaId = isset($_POST['media_id']) ? (int)$_POST['media_id'] : 0;
            $rating  = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
            $review  = clean($_POST['review'] ?? '');

            if ($mediaId <= 0 || $rating < 0 || $rating > 10) {
                echo json_encode(['success' => false, 'error' => 'Invalid media or rating value.']);
                break;
            }

            StarStarMediaDatabase::submitReview($pdo, $mediaId, $rating, $review);
            echo json_encode(['success' => true]);
            break;

        // --------------------------------------
        // SUBMIT NEW MEDIA REQUEST
        // (from request-new-media.html)
// --------------------------------------
        case 'submitNewMedia':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
                break;
            }

            $name        = clean($_POST['media_name'] ?? '');
            $type        = clean($_POST['media_type'] ?? '');
            $description = clean($_POST['media_description'] ?? '');

            if ($name === '' || $type === '') {
                echo json_encode(['success' => false, 'error' => 'Media name and type are required.']);
                break;
            }

            $sql = "
                INSERT INTO newmediarequest (media_name, media_type, media_description)
                VALUES (:name, :type, :description)
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name'        => $name,
                ':type'        => $type,
                ':description' => $description !== '' ? $description : null
            ]);

            echo json_encode(['success' => true]);
            break;

        // --------------------------------------
        // DEFAULT / UNKNOWN ACTION
        // --------------------------------------
        default:
            echo json_encode([
                'success' => false,
                'error'   => 'Unknown action.'
            ]);
            break;
    }

} catch (Throwable $e) {
    ErrorLogger::log("Router - Action '{$action}' Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error'   => 'An unexpected error occurred.'
    ]);
    exit;
}
