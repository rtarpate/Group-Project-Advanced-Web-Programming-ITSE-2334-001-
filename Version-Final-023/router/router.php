<?php
// router.php — API router for Star Media Reviews

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/DatabaseConnector.php';

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit;
}

$action = $_GET['action'] ?? '';

try {

    switch ($action) {

        /* =====================================================
           SUBMIT REVIEW
        ===================================================== */
        case 'submitReview':

            $mediaId = (int)($_POST['media_id'] ?? 0);
            $rating  = (int)($_POST['rating'] ?? -1);
            $review  = trim($_POST['review'] ?? '');

            if ($mediaId <= 0 || $rating < 0 || $rating > 10) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid input'
                ]);
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO user_reviews (media_id, rating, review_text)
                VALUES (:media_id, :rating, :review_text)
            ");

            $stmt->execute([
                ':media_id'   => $mediaId,
                ':rating'     => $rating,
                ':review_text'=> $review
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Review submitted successfully!'
            ]);
            exit;


        /* =====================================================
           REQUEST NEW MEDIA
        ===================================================== */
        case 'requestMedia':

            $name = trim($_POST['media_name'] ?? '');
            $type = trim($_POST['media_type'] ?? '');
            $desc = trim($_POST['description'] ?? '');

            if ($name === '' || $type === '' || $desc === '') {
                echo json_encode([
                    'success' => false,
                    'message' => 'All required fields must be filled.'
                ]);
                exit;
            }

            $stmt = $pdo->prepare("
                INSERT INTO newmediarequest (media_name, media_type, media_description)
                VALUES (:name, :type, :desc)
            ");

            $stmt->execute([
                ':name' => $name,
                ':type' => $type,
                ':desc' => $desc
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Media request submitted successfully!'
            ]);
            exit;


        /* =====================================================
           GET MEDIA TYPES
        ===================================================== */
        case 'getMediaTypes':

            $stmt = $pdo->query("
                SELECT media_type_id, type_name
                FROM media_types
                ORDER BY type_name ASC
            ");

            echo json_encode([
                'success' => true,
                'types'   => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            exit;


        /* =====================================================
           INVALID ACTION
        ===================================================== */
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            exit;
    }

} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
    exit;
}
