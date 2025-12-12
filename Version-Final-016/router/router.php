<?php


header('Content-Type: application/json');
ini_set('display_errors', 0);

require_once __DIR__ . '/../includes/DatabaseConnector.php';

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

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

                COALESCE(AVG(ur.rating), 0) AS average_rating,
                COUNT(ur.review_id)         AS review_count

            FROM media m
            LEFT JOIN media_types mt      ON m.media_type_id = mt.media_type_id
            LEFT JOIN genres g            ON m.genre_id      = g.genre_id
            LEFT JOIN content_ratings cr  ON m.content_rating_id = cr.content_rating_id
            LEFT JOIN user_reviews ur     ON m.media_id      = ur.media_id

            GROUP BY m.media_id
            ORDER BY m.title ASC
        ";

        $stmt = $pdo->query($sql);
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
    // ACTION: getMediaTypes
    // Used by dropdowns (reviews filter + request-new-media)
    // ========================================================
    if ($action === 'getMediaTypes') {

        $sql = "SELECT media_type_id, type_name FROM media_types ORDER BY type_name ASC";
        $stmt = $pdo->query($sql);
        $types = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'types'   => $types
        ]);
        exit;
    }

    // ========================================================
    // ACTION: requestMedia
    // Inserts a new row into newmediarequest
    // ========================================================
    if ($action === 'requestMedia') {

        $mediaName = trim($_POST['media_name'] ?? '');
        $mediaType = trim($_POST['media_type'] ?? '');
        $desc      = trim($_POST['description'] ?? '');

        if ($mediaName === '' || $mediaType === '' || $desc === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Please fill out all required fields.'
            ]);
            exit;
        }

        $sql = "INSERT INTO newmediarequest (media_name, media_type, media_description) VALUES (:name, :type, :desc)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $mediaName,
            ':type' => $mediaType,
            ':desc' => $desc
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Request submitted successfully.'
        ]);
        exit;
    }

    // ===============================================
    // ACTION: submitReview
    // ===============================================
    if ($action === 'submitReview') {

        $mediaId = (int)($_POST['media_id'] ?? 0);
        $rating  = (int)($_POST['rating'] ?? -1);
        $review  = trim($_POST['review'] ?? '');

        if ($mediaId <= 0 || $rating < 0 || $rating > 10) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid media or rating.'
            ]);
            exit;
        }

        $sql = "INSERT INTO user_reviews (media_id, rating, review_text) VALUES (:media_id, :rating, :review_text)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':media_id'    => $mediaId,
            ':rating'      => $rating,
            ':review_text' => ($review === '' ? null : $review)
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Review submitted successfully.'
        ]);
        exit;
    }

    // ========================================================
    // Unknown action
    // ========================================================
    echo json_encode([
        'success' => false,
        'message' => 'Unknown action.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error.',
        'error'   => $e->getMessage()
    ]);
    exit;
}
