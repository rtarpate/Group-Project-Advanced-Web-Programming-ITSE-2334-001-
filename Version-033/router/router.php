<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/starstarmediareviewdatabase.php'; 
// IMPORTANT: lowercase filename matches your real file

header("Content-Type: application/json");

// ======================================================================
// Normalize Media Type Names → Option A standard list
// ======================================================================
function normalizeTypeName(string $type): string {

    // clean input
    $clean = strtolower(trim($type));
    $clean = str_replace("_", " ", $clean);
    $clean = preg_replace('/\s+/', ' ', $clean);

    // standardized buckets
    $map = [
        'movie'         => ['movie', 'film', 'movies'],
        'tv show'       => ['tv', 'tv show', 'television', 'tv series', 'series', 'show'],
        'video game'    => ['video game', 'game', 'games'],
        'comic book'    => ['comic', 'comic book', 'comics'],
        'manga'         => ['manga'],
        'novel'         => ['novel', 'book', 'light novel'],
        'web novel'     => ['web novel', 'online novel'],
        'web series'    => ['web series', 'web show', 'online series'],
        'audio book'    => ['audiobook', 'audio book'],
    ];

    foreach ($map as $standard => $aliases) {
        if (in_array($clean, $aliases, true)) {
            return $standard;
        }
    }

    // fallback: return cleaned, title-cased
    return ucwords($clean);
}

// ======================================================================
// MediaRepository - all DB operations for media + reviews
// ======================================================================
class MediaRepository
{
    public static function getAllMedia(): array
    {
        $pdo = DatabaseConnector::getConnection();
        if (!$pdo) {
            return [];
        }

        $sql = "
            SELECT 
                m.media_id,
                m.media_title,
                m.media_type,
                m.media_description,
                m.media_image,
                COALESCE(ar.average_rating, 0) AS average_rating,
                COALESCE(ar.review_count, 0) AS review_count
            FROM media m
            LEFT JOIN average_reviews ar ON m.media_id = ar.media_id
            ORDER BY m.media_title ASC
        ";

        try {
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // normalize media_type
            foreach ($rows as &$row) {
                $row['media_type'] = normalizeTypeName($row['media_type'] ?? '');
            }

            return $rows;

        } catch (PDOException $e) {
            ErrorLogger::log("GET ALL MEDIA ERROR: " . $e->getMessage());
            return [];
        }
    }

    public static function getMediaDetails(int $mediaId): ?array
    {
        $pdo = DatabaseConnector::getConnection();
        if (!$pdo) {
            return null;
        }

        $sql = "
            SELECT 
                m.media_id,
                m.media_title,
                m.media_type,
                m.media_description,
                m.media_image,
                COALESCE(ar.average_rating, 0) AS average_rating,
                COALESCE(ar.review_count, 0) AS review_count
            FROM media m
            LEFT JOIN average_reviews ar ON m.media_id = ar.media_id
            WHERE m.media_id = :id
            LIMIT 1
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $mediaId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            $row['media_type'] = normalizeTypeName($row['media_type'] ?? '');
            return $row;

        } catch (PDOException $e) {
            ErrorLogger::log("GET MEDIA DETAILS ERROR: " . $e->getMessage());
            return null;
        }
    }

    public static function getMediaReviews(int $mediaId): array
    {
        $pdo = DatabaseConnector::getConnection();
        if (!$pdo) {
            return [];
        }

        $sql = "
            SELECT 
                review_id,
                rating,
                review_text,
                created_at
            FROM user_reviews
            WHERE media_id = :id
            ORDER BY created_at DESC
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $mediaId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            ErrorLogger::log("GET MEDIA REVIEWS ERROR: " . $e->getMessage());
            return [];
        }
    }

    public static function submitNewMedia(string $title, string $type, string $description): bool
    {
        $pdo = DatabaseConnector::getConnection();
        if (!$pdo) {
            return false;
        }

        $sql = "
            INSERT INTO newmediarequest (media_name, media_type, media_description, request_date)
            VALUES (:name, :type, :description, NOW())
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name'        => $title,
                ':type'        => $type,
                ':description' => $description,
            ]);

            return true;

        } catch (PDOException $e) {
            ErrorLogger::log("SUBMIT NEW MEDIA ERROR: " . $e->getMessage());
            return false;
        }
    }

    public static function recalcMediaRatings(int $mediaId): bool
    {
        $pdo = DatabaseConnector::getConnection();
        if (!$pdo) {
            return false;
        }

        try {
            // Recalculate average + count
            $sql = "
                SELECT 
                    AVG(rating) AS avg_rating,
                    COUNT(*)   AS review_count
                FROM user_reviews
                WHERE media_id = :id
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $mediaId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $avg   = $row['avg_rating'] ?? 0;
            $count = $row['review_count'] ?? 0;

            // Upsert into average_reviews
            $upsert = "
                INSERT INTO average_reviews (media_id, average_rating, review_count)
                VALUES (:id, :avg, :cnt)
                ON DUPLICATE KEY UPDATE
                    average_rating = VALUES(average_rating),
                    review_count   = VALUES(review_count)
            ";
            $upStmt = $pdo->prepare($upsert);
            $upStmt->execute([
                ':id'  => $mediaId,
                ':avg' => $avg,
                ':cnt' => $count,
            ]);

            return true;

        } catch (PDOException $e) {
            ErrorLogger::log("RECALC RATINGS ERROR: " . $e->getMessage());
            return false;
        }
    }

    public static function submitReview(int $mediaId, int $rating, string $reviewText = ''): bool
    {
        $pdo = DatabaseConnector::getConnection();
        if (!$pdo) {
            return false;
        }

        $sql = "
            INSERT INTO user_reviews (media_id, rating, review_text, created_at)
            VALUES (:media_id, :rating, :review_text, NOW())
        ";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':media_id'    => $mediaId,
                ':rating'      => $rating,
                ':review_text' => $reviewText,
            ]);

            // Recalculate ratings
            return self::recalcMediaRatings($mediaId);

        } catch (PDOException $e) {
            ErrorLogger::log("INSERT REVIEW ERROR: " . $e->getMessage());
            return false;
        }
    }
}

// ======================================================================
// ROUTER: handle AJAX actions
// ======================================================================
$action = $_GET['action'] ?? '';

switch ($action) {

    case 'getMedia':
        $media = MediaRepository::getAllMedia();
        echo json_encode([
            "success" => true,
            "media"   => $media
        ]);
        exit;

    case 'getMediaDetails':
        $mediaId = isset($_GET['media_id']) ? (int)$_GET['media_id'] : 0;

        if ($mediaId <= 0) {
            echo json_encode([
                "success" => false,
                "error"   => "Invalid media ID."
            ]);
            exit;
        }

        $details = MediaRepository::getMediaDetails($mediaId);

        if (!$details) {
            echo json_encode([
                "success" => false,
                "error"   => "Media not found."
            ]);
            exit;
        }

        echo json_encode([
            "success" => true,
            "media"   => $details
        ]);
        exit;

    case 'getMediaReviews':
        $mediaId = isset($_GET['media_id']) ? (int)$_GET['media_id'] : 0;

        if ($mediaId <= 0) {
            echo json_encode([
                "success" => false,
                "error"   => "Invalid media ID."
            ]);
            exit;
        }

        $reviews = MediaRepository::getMediaReviews($mediaId);

        echo json_encode([
            "success" => true,
            "reviews" => $reviews
        ]);
        exit;

    case 'submitNewMedia':
        $title       = trim($_POST['media_name'] ?? '');
        $type        = trim($_POST['media_type'] ?? '');
        $description = trim($_POST['media_description'] ?? '');

        if ($title === '' || $type === '' || $description === '') {
            echo json_encode([
                "success" => false,
                "error"   => "All fields are required."
            ]);
            exit;
        }

        $ok = MediaRepository::submitNewMedia($title, $type, $description);

        echo json_encode([
            "success" => $ok,
            "error"   => $ok ? null : "Failed to submit request."
        ]);
        exit;

    case 'submitReview':
        $mediaId    = isset($_POST['media_id']) ? (int)$_POST['media_id'] : 0;
        $rating     = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $reviewText = trim($_POST['review'] ?? '');

        if ($mediaId <= 0 || $rating < 0 || $rating > 10) {
            echo json_encode([
                "success" => false,
                "error"   => "Invalid media or rating."
            ]);
            exit;
        }

        $ok = MediaRepository::submitReview($mediaId, $rating, $reviewText);

        echo json_encode([
            "success" => $ok,
            "error"   => $ok ? null : "Failed to submit review."
        ]);
        exit;

    default:
        echo json_encode([
            "success" => false,
            "error"   => "Invalid action."
        ]);
        exit;
}
?>
