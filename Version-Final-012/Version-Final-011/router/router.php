<?php


header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);


// ============================================================
// router.php — JSON API for Star Media Review
// Location: /router/router.php
// Used by: reviews.js, form-write-review.js
// ============================================================

header('Content-Type: application/json; charset=utf-8');

// ------------------------------------------------------------
// Load DB + helper functions
// ------------------------------------------------------------
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    echo json_encode([
        'success' => false,
        'error'   => 'Database connection failed.'
    ]);
    exit;
}

// Get action from query or POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    // ========================================================
    // ACTION: getMedia
    // Used by reviews.js to build the media cards grid
    // ========================================================
    if ($action === 'getMedia') {

        $sql = "
            SELECT 
                m.media_id,
                m.title              AS media_title,
                m.release_date,
                m.director,
                m.image_path         AS media_image,

                mt.type_name         AS media_type,
                g.genre_name         AS genre_name,
                cr.rating_code       AS content_rating,

                COALESCE(AVG(ur.rating), 0)      AS average_rating,
                COUNT(ur.review_id)              AS review_count

            FROM media m
            LEFT JOIN media_types mt 
                   ON m.media_type_id = mt.media_type_id
            LEFT JOIN genres g 
                   ON m.genre_id = g.genre_id
            LEFT JOIN content_ratings cr 
                   ON m.content_rating_id = cr.rating_id
            LEFT JOIN user_reviews ur
                   ON m.media_id = ur.media_id

            GROUP BY 
                m.media_id,
                m.title,
                m.release_date,
                m.director,
                m.image_path,
                mt.type_name,
                g.genre_name,
                cr.rating_code

            ORDER BY m.title ASC
        ";

        $stmt  = $pdo->query($sql);
        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'media'   => $media
        ]);
        exit;
    }

    // ========================================================
    // ACTION: getMediaTitles
    // Used by write-review form dropdown
    // ========================================================
    if ($action === 'getMediaTitles') {

        $sql = "SELECT media_id, title FROM media ORDER BY title ASC";
        $stmt = $pdo->query($sql);
        $titles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'titles'  => $titles
        ]);
        exit;
    }

    // ========================================================
    // ACTION: submitReview
    // Used by form-write-review.js (POST only)
    // ========================================================
    if ($action === 'submitReview') {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'error'   => 'Invalid request method.'
            ]);
            exit;
        }

        // Clean and validate input
        $mediaId = isset($_POST['media_id']) ? (int) $_POST['media_id'] : 0;
        $rating  = isset($_POST['rating'])   ? (int) $_POST['rating']   : -1;
        $reviewTextRaw = $_POST['review'] ?? '';
        $reviewText    = clean_input($reviewTextRaw);

        if ($mediaId <= 0) {
            echo json_encode([
                'success' => false,
                'error'   => 'Please select a valid media title.'
            ]);
            exit;
        }

        if ($rating < 0 || $rating > 10) {
            echo json_encode([
                'success' => false,
                'error'   => 'Rating must be between 0 and 10.'
            ]);
            exit;
        }

        // Ensure the media exists
        $checkStmt = $pdo->prepare("SELECT media_id FROM media WHERE media_id = ?");
        $checkStmt->execute([$mediaId]);
        if (!$checkStmt->fetchColumn()) {
            echo json_encode([
                'success' => false,
                'error'   => 'Selected media no longer exists.'
            ]);
            exit;
        }

        // Insert the new review
        $insertStmt = $pdo->prepare("
            INSERT INTO user_reviews (media_id, rating, review_text)
            VALUES (:media_id, :rating, :review_text)
        ");

        $insertStmt->execute([
            ':media_id'    => $mediaId,
            ':rating'      => $rating,
            ':review_text' => $reviewText  // can be empty string if optional
        ]);

        // You asked not to change the database structure.
        // We therefore *do not* touch or rely on any extra tables here.
        // Averages are computed live in getMedia() using user_reviews.

        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully.'
        ]);
        exit;
    }

    // --------------------------------------------------------
    // Unknown / missing action
    // --------------------------------------------------------
    echo json_encode([
        'success' => false,
        'error'   => 'Unknown or missing action.'
    ]);
    exit;

} catch (Exception $e) {

    // Generic JSON error handler so the frontend can show a message
    error_log("router.php error ({$action}): " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'error'   => 'A server error occurred while processing your request.'
    ]);
    exit;
}
