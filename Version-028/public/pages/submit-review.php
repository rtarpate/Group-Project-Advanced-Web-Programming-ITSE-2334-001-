<?php

require_once __DIR__ . "/helper.php";
require_once __DIR__ . "/starstarmediareviewdatabase.php";

header('Content-Type: application/json');

try {
    $mediaTitle = cleanInput($_POST['media_title'] ?? '');
    $rating     = cleanInput($_POST['rating'] ?? '');
    $reviewText = cleanInput($_POST['review_text'] ?? '');

    if ($rating === '' || $rating < 0 || $rating > 10) {
        echo json_encode(["status" => "error", "message" => "Rating must be between 0 and 10"]);
        exit;
    }

    $success = StarStarMediaDatabase::insertReview($mediaTitle, $rating, $reviewText);

    if ($success) {
        echo json_encode(["status" => "success", "message" => "Review submitted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "DB insert failed"]);
    }

} catch (Exception $e) {
    ErrorLogger::log("SUBMIT REVIEW ERROR: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Unexpected server error"]);
}
