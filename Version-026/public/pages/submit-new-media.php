<?php

require_once __DIR__ . "/helper.php";
require_once __DIR__ . "/starstarmediareviewdatabase.php";

header('Content-Type: application/json');

try {
    // Clean input
    $mediaName        = cleanInput($_POST['media_name'] ?? '');
    $mediaType        = cleanInput($_POST['media_type'] ?? '');
    $mediaDescription = cleanInput($_POST['media_description'] ?? '');

    if (!$mediaName || !$mediaType) {
        ErrorLogger::log("MISSING REQUIRED FIELDS IN submit-new-media.php");
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit;
    }

    // Insert into DB
    $success = StarStarMediaDatabase::insertMediaRequest($mediaName, $mediaType, $mediaDescription);

    if ($success) {
        echo json_encode(["status" => "success", "message" => "Request submitted"]);
    } else {
        echo json_encode(["status" => "error", "message" => "DB insert failed"]);
    }

} catch (Exception $e) {
    ErrorLogger::log("UNEXPECTED ERROR SUBMIT REQUEST: " . $e->getMessage());
    echo json_encode(["status" => "error", "message" => "Unexpected server error"]);
}
