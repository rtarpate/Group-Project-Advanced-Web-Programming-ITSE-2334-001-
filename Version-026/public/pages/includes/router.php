<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/includes/helper.php';
require_once __DIR__ . '/includes/StarStarMediaDatabase.php';
require_once __DIR__
    . '/..'
    . '/assets/logs/ErrorLogger.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {

        // ============================
        // 1) Reviews page – get media
        // ============================
        case 'getMedia':
            $data = StarStarMediaDatabase::getAllMediaWithRatings();

            echo json_encode([
                'status' => 'success',
                'data'   => $data,
            ]);
            break;


        // =====================================
        // 2) Request New Media form submission
        // =====================================
        case 'submitNewMedia':
            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Method not allowed',
                ]);
                break;
            }

            $name = clean('media_name', 'string', INPUT_POST);
            $type = clean('media_type', 'string', INPUT_POST);
            $desc = clean('media_description', 'string', INPUT_POST);

            if ($name === '' || $type === '' || $desc === '') {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'All fields are required.',
                ]);
                break;
            }

            $ok = StarStarMediaDatabase::insertMediaRequest($name, $type, $desc);

            echo json_encode([
                'status'  => $ok ? 'success' : 'error',
                'message' => $ok ? 'Request submitted successfully.' : 'Failed to submit request.',
            ]);
            break;


        // ===============================
        // 3) Write Review form submission
        // ===============================
        case 'submitReview':
            if ($method !== 'POST') {
                http_response_code(405);
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Method not allowed',
                ]);
                break;
            }

            $mediaId    = (int) clean('media_id', 'int', INPUT_POST);
            $rating     = (int) clean('rating', 'int', INPUT_POST);
            $reviewText = clean('review_text', 'string', INPUT_POST);

            // rating 0–10 as per your latest change
            if ($mediaId <= 0 || $rating < 0 || $rating > 10) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Please select a title and enter a rating between 0 and 10.',
                ]);
                break;
            }

            $ok = StarStarMediaDatabase::insertReview($mediaId, $rating, $reviewText ?: null);

            echo json_encode([
                'status'  => $ok ? 'success' : 'error',
                'message' => $ok ? 'Review submitted successfully.' : 'Failed to submit review.',
            ]);
            break;


        // =======================
        // Unknown or missing action
        // =======================
        default:
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => 'Unknown or missing action.',
            ]);
            break;
    }

} catch (Throwable $e) {
    ErrorLogger::log("ROUTER ERROR: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Internal server error.',
    ]);
}
