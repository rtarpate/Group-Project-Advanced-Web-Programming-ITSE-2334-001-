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
           GET ALL MEDIA (Reviews Page)
        ===================================================== */
        case 'getMedia':

            $sql = "
                SELECT
                    m.media_id,
                    m.title              AS media_title,
                    m.image_path         AS media_image,
                    mt.type_name         AS media_type,
                    g.genre_name         AS genre_name,
                    cr.rating_code       AS content_rating,
                    COALESCE(mr.average_rating, 0) AS average_rating
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

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            echo json_encode([
                'success' => true,
                'media'   => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
            exit;


        /* =====================================================
           GET MEDIA TYPES (Filters + Forms)
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
           UNKNOWN ACTION
        ===================================================== */
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
            exit;
    }

} catch (Throwable $e) {

    // Never expose raw SQL errors publicly
    echo json_encode([
        'success' => false,
        'message' => 'Server error',
        'error'   => $e->getMessage()
    ]);
    exit;
}
