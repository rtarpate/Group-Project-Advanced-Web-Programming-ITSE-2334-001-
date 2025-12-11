<?php
// ============================================================
// router.php — JSON API for Star Media Review
// Used by reviews.js, form-write-review.js, and future features
// ============================================================

header("Content-Type: application/json");

// Load DB + helper functions
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    echo json_encode([
        "success" => false,
        "error"   => "Database connection failed."
    ]);
    exit;
}

$action = $_GET['action'] ?? null;

// ============================================================
// ACTION: getMedia
// Returns a full list of media for reviews.js
// Includes:
// - media_title
// - media_type
// - genre_name
// - content_rating
// - average_rating
// - review_count
// - release_date
// - media_image
// ============================================================

if ($action === "getMedia") {

    try {
        $sql = "
            SELECT 
                m.media_id,
                m.title AS media_title,
                m.release_date,
                m.image_path AS media_image,

                mt.type_name AS media_type,
                g.genre_name,
                cr.rating_code AS content_rating,

                COALESCE(mr.average_rating, 0) AS average_rating,
                COALESCE(mr.total_ratings, 0) AS review_count

            FROM media m
            LEFT JOIN media_types mt 
                ON m.media_type_id = mt.media_type_id
            LEFT JOIN genres g 
                ON m.genre_id = g.genre_id
            LEFT JOIN content_ratings cr 
                ON m.content_rating_id = cr.rating_id
            LEFT JOIN media_ratings mr 
                ON m.media_id = mr.media_id

            ORDER BY m.title ASC
        ";

        $stmt = $pdo->query($sql);
        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "media"   => $media
        ]);
    } catch (Exception $e) {

        echo json_encode([
            "success" => false,
            "error"   => $e->getMessage()
        ]);
    }
    exit;
}



// ============================================================
// ACTION: submitReview
// Called by form-write-review.js when the user submits a review
// ============================================================

if ($action === "submitReview") {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "success" => false,
            "error"   => "POST required."
        ]);
        exit;
    }

    $media_id    = clean_input($_POST['media_id'] ?? '');
    $rating      = clean_input($_POST['rating'] ?? '');
    $review_text = clean_input($_POST['review_text'] ?? '');

    if ($media_id === "" || $rating === "") {
        echo json_encode([
            "success" => false,
            "error"   => "Media and rating are required."
        ]);
        exit;
    }

    try {
        // Insert user review
        $sql = "
            INSERT INTO user_reviews (media_id, rating, review_text, review_date)
            VALUES (:media_id, :rating, :review_text, NOW())
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ":media_id"    => $media_id,
            ":rating"      => $rating,
            ":review_text" => $review_text
        ]);

        // Recalculate aggregated rating
        $sqlAvg = "
            REPLACE INTO media_ratings (media_id, average_rating, total_ratings)
            SELECT 
                media_id,
                AVG(rating) AS avg_rating,
                COUNT(*)    AS total_reviews
            FROM user_reviews
            WHERE media_id = :media_id
        ";

        $stmtAvg = $pdo->prepare($sqlAvg);
        $stmtAvg->execute([":media_id" => $media_id]);

        echo json_encode(["success" => true]);
    }
    catch (Exception $e) {

        echo json_encode([
            "success" => false,
            "error"   => $e->getMessage()
        ]);
    }

    exit;
}



// ============================================================
// OPTIONAL FUTURE ENDPOINTS
// (Kept for expansion without breaking structure)
// ============================================================

if ($action === "getMediaDetails") {
    echo json_encode(["success" => false, "error" => "Not implemented."]);
    exit;
}

if ($action === "getMediaReviews") {
    echo json_encode(["success" => false, "error" => "Not implemented."]);
    exit;
}



// ============================================================
// DEFAULT ERROR — Unknown Action
// ============================================================

echo json_encode([
    "success" => false,
    "error"   => "Unknown or missing action."
]);
exit;
?>
