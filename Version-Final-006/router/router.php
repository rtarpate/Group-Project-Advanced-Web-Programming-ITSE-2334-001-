<?php
// ============================================================
// router.php — JSON API for Star Media Review (InfinityFree)
// Location: /router.php (document root)
// Used by: reviews.js, form-write-review.js
// ============================================================

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/includes/DatabaseConnector.php';
require_once __DIR__ . '/includes/helper.php';

// ------------------------------------------------------------
// Get DB connection
// ------------------------------------------------------------
$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    echo json_encode([
        'success' => false,
        'error'   => 'Database connection failed.'
    ]);
    exit;
}

// ------------------------------------------------------------
// Determine requested action
// ------------------------------------------------------------
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ============================================================
// ACTION: getMedia
// Returns full media list + types/genres/content ratings
// and aggregate user review stats.
// ============================================================
if ($action === 'getMedia') {

    try {
        $sql = "
            SELECT
                m.media_id,
                m.title                         AS media_title,
                mt.type_name                    AS media_type,
                g.genre_name                    AS genre_name,
                cr.rating_code                  AS content_rating,
                m.image_path                    AS media_image,
                m.release_date,
                COALESCE(agg.avg_rating, 0)     AS average_rating,
                COALESCE(agg.review_count, 0)   AS review_count
            FROM media m
            LEFT JOIN media_types mt
                ON m.media_type_id = mt.media_type_id
            LEFT JOIN genres g
                ON m.genre_id = g.genre_id
            LEFT JOIN content_ratings cr
                ON m.content_rating_id = cr.content_rating_id
            LEFT JOIN (
                SELECT
                    media_id,
                    AVG(rating) AS avg_rating,
                    COUNT(*)    AS review_count
                FROM user_reviews
                GROUP BY media_id
            ) AS agg
                ON agg.media_id = m.media_id
            ORDER BY m.title ASC
        ";

        $stmt  = $pdo->query($sql);
        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'media'   => $media
        ]);
    } catch (Exception $e) {
        error_log('router.php getMedia error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error'   => 'Failed to load media list.'
        ]);
    }
    exit;
}

// ============================================================
// ACTION: getMediaTitles
// Used for the write-review dropdown
// ============================================================
if ($action === 'getMediaTitles') {

    try {
        $sql = "
            SELECT
                m.media_id,
                m.title AS media_title
            FROM media m
            ORDER BY m.title ASC
        ";
        $stmt   = $pdo->query($sql);
        $titles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'media'   => $titles
        ]);
    } catch (Exception $e) {
        error_log('router.php getMediaTitles error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error'   => 'Failed to load media titles.'
        ]);
    }
    exit;
}

// ============================================================
// ACTION: submitReview
// Accepts POST: media_id, rating, review (optional)
// ============================================================
if ($action === 'submitReview') {

    $rawMediaId = $_POST['media_id'] ?? '';
    $rawRating  = $_POST['rating']   ?? '';
    $rawReview  = $_POST['review']   ?? '';

    $mediaId = (int) $rawMediaId;
    $rating  = is_numeric($rawRating) ? (float) $rawRating : null;
    $review  = clean_input($rawReview);

    if ($mediaId <= 0 || $rating === null) {
        echo json_encode([
            'success' => false,
            'error'   => 'Media and rating are required.'
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

    try {
        $sql = "
            INSERT INTO user_reviews (media_id, rating, review_text, review_date)
            VALUES (:media_id, :rating, :review_text, NOW())
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':media_id'    => $mediaId,
            ':rating'      => $rating,
            ':review_text' => $review
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully.'
        ]);
    } catch (Exception $e) {
        error_log('router.php submitReview error: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'error'   => 'Failed to submit review.'
        ]);
    }
    exit;
}

// ============================================================
// DEFAULT ERROR — Unknown or missing action
// ============================================================
echo json_encode([
    'success' => false,
    'error'   => 'Unknown or missing action.'
]);
exit;
