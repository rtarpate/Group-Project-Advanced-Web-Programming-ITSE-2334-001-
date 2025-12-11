<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/starstarmediareviewdatabase.php';

header("Content-Type: application/json");

// ------------------------------------------------------
// CREATE DATABASE CONNECTION
// ------------------------------------------------------
$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    echo json_encode([
        "success" => false,
        "error" => "Database connection failed."
    ]);
    exit;
}

// ------------------------------------------------------
// ACTIVATE REQUEST HANDLER
// ------------------------------------------------------
$action = $_GET['action'] ?? null;


// ------------------------------------------------------
// ROUTES
// ------------------------------------------------------
switch ($action) {


/* ======================================================
   GET MEDIA (Reviews Page)
   ====================================================== */
case "getMedia":
    try {
        $sql = "
            SELECT 
                m.media_id,
                m.title AS media_title,
                m.image_path AS media_image,
                mt.type_name AS media_type,
                g.genre_name AS genre_name,
                cr.rating_code AS content_rating,
                cr.description AS content_rating_desc,
                AVG(r.rating) AS average_rating,
                COUNT(r.review_id) AS review_count
            FROM media m

            LEFT JOIN media_types mt 
                ON m.media_type_id = mt.media_type_id

            LEFT JOIN genres g 
                ON m.genre_id = g.genre_id

            LEFT JOIN content_ratings cr
                ON m.content_rating_id = cr.rating_id

            LEFT JOIN user_reviews r 
                ON m.media_id = r.media_id

            GROUP BY m.media_id
            ORDER BY m.title ASC;
        ";

        $stmt = $pdo->query($sql);
        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "media" => $media]);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
    exit;




/* ======================================================
   GET MEDIA TITLES (Write a Review dropdown)
   ====================================================== */
case "getMediaTitles":
    try {
        $stmt = $pdo->query("
            SELECT media_id, title
            FROM media
            ORDER BY title ASC
        ");

        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "media" => $media]);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }
    exit;



/* ======================================================
   SUBMIT REVIEW
   ====================================================== */
case "submitReview":

    $mediaId    = $_POST["media_id"] ?? null;
    $rating     = $_POST["rating"] ?? null;
    $reviewText = $_POST["review_text"] ?? null;

    if (!$mediaId || $rating === null) {
        echo json_encode(["success" => false, "error" => "Missing required fields."]);
        exit;
    }

    try {
        $sql = "INSERT INTO user_reviews (media_id, rating, review_text)
                VALUES (?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mediaId, $rating, $reviewText]);

        echo json_encode(["success" => true]);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    exit;




/* ======================================================
   REQUEST NEW MEDIA
   ====================================================== */
case "requestNewMedia":

    $name = $_POST['media_name'] ?? null;
    $type = $_POST['media_type'] ?? null;
    $desc = $_POST['media_description'] ?? null;

    if (!$name || !$type) {
        echo json_encode(["success" => false, "error" => "Missing required fields."]);
        exit;
    }

    try {
        $sql = "INSERT INTO newmediarequest (media_name, media_type, media_description)
                VALUES (?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $type, $desc]);

        echo json_encode(["success" => true]);

    } catch (Exception $e) {
        echo json_encode(["success" => false, "error" => $e->getMessage()]);
    }

    exit;




/* ======================================================
   DEFAULT / FALLBACK
   ====================================================== */
default:
    echo json_encode([
        "success" => false,
        "error" => "Invalid or missing action."
    ]);
    exit;
}
