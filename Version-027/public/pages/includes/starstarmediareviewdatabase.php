<?php

require_once __DIR__ . '/DatabaseConnector.php';

class StarStarMediaDatabase
{
    // ===========================================
    // INSERT NEW MEDIA REQUEST
    // ===========================================
    public static function insertMediaRequest(string $mediaName, string $mediaType, string $mediaDescription): bool
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return false;

        $sql = "INSERT INTO newmediarequest (media_name, media_type, media_description)
                VALUES (:name, :type, :description)";

        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':name'        => $mediaName,
                ':type'        => $mediaType,
                ':description' => $mediaDescription,
            ]);
        } catch (PDOException $e) {
            ErrorLogger::log("INSERT NEW MEDIA REQUEST ERROR: " . $e->getMessage());
            return false;
        }
    }


    // ===========================================
    // INSERT USER REVIEW + UPDATE MEDIA RATINGS
    // ===========================================
    public static function insertReview(int $mediaId, int $rating, ?string $reviewText): bool
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return false;

        try {
            // STEP 1 — INSERT USER REVIEW
            $sql = "INSERT INTO user_reviews (media_id, rating, review_text)
                    VALUES (:media_id, :rating, :review_text)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':media_id'    => $mediaId,
                ':rating'      => $rating,
                ':review_text' => $reviewText,
            ]);

            // STEP 2 — GET NEW AVG + COUNT
            $ratingSql = "
                SELECT 
                    AVG(rating) AS avg_rating,
                    COUNT(*) AS rating_count
                FROM user_reviews
                WHERE media_id = :media_id
            ";
            $stmt2 = $db->prepare($ratingSql);
            $stmt2->execute([':media_id' => $mediaId]);

            $result = $stmt2->fetch();
            if (!$result) {
                return false;
            }

            $newAvg   = round($result['avg_rating'], 2);
            $newCount = (int) $result['rating_count'];

            // STEP 3 — UPDATE media_ratings TABLE
            // Upsert logic: insert if missing, update if exists
            $updateSql = "
                INSERT INTO media_ratings (media_id, average_rating, total_ratings)
                VALUES (:media_id, :average_rating, :total_ratings)
                ON DUPLICATE KEY UPDATE
                    average_rating = VALUES(average_rating),
                    total_ratings  = VALUES(total_ratings)
            ";

            $stmt3 = $db->prepare($updateSql);
            return $stmt3->execute([
                ':media_id'       => $mediaId,
                ':average_rating' => $newAvg,
                ':total_ratings'  => $newCount,
            ]);

        } catch (PDOException $e) {
            ErrorLogger::log("INSERT REVIEW ERROR: " . $e->getMessage());
            return false;
        }
    }


    // ===========================================
    // GET ALL MEDIA + RATINGS FOR REVIEWS PAGE
    // ===========================================
    public static function getAllMediaWithRatings(): array
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return [];

        $sql = "
            SELECT 
                m.media_id,
                m.title,
                m.image_path,
                m.genre,
                m.content_rating,
                m.release_date,
                mt.type_name AS media_type,
                IFNULL(r.average_rating, 0) AS average_rating,
                IFNULL(r.total_ratings, 0) AS total_ratings
            FROM media m
            LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
            LEFT JOIN media_ratings r ON m.media_id = r.media_id
            ORDER BY m.title ASC;
        ";

        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorLogger::log("GET MEDIA WITH RATINGS ERROR: " . $e->getMessage());
            return [];
        }
    }
}

    // ===========================================
    // GET ONE MEDIA'S FULL DETAILS
    // ===========================================
    public static function getMediaDetails(int $mediaId): ?array
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return null;

        $sql = "
            SELECT 
                m.*, 
                mt.type_name AS media_type,
                IFNULL(r.average_rating, 0) AS average_rating,
                IFNULL(r.total_ratings, 0) AS total_ratings
            FROM media m
            LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
            LEFT JOIN media_ratings r ON m.media_id = r.media_id
            WHERE m.media_id = :id
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $mediaId]);
        return $stmt->fetch() ?: null;
    }


    // ===========================================
    // GET ALL USER REVIEWS FOR ONE MEDIA
    // ===========================================
    public static function getMediaReviews(int $mediaId): array
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return [];

        $sql = "
            SELECT rating, review_text, review_date
            FROM user_reviews
            WHERE media_id = :id
            ORDER BY review_date DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $mediaId]);
        return $stmt->fetchAll();
    }
