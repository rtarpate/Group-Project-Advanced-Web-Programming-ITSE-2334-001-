<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'DatabaseConnector.php';

class StarStarMediaDatabase
{
    /*
     * INSERT REQUEST (request-new-media form)
     */
    public static function insertMediaRequest(string $mediaName, string $mediaType, string $mediaDescription): bool
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) {
            return false;
        }

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
            ErrorLogger::log("INSERT REQUEST ERROR: " . $e->getMessage());
            return false;
        }
    }


    /*
     * INSERT REVIEW (write-review form) → uses user_reviews table
     */
    public static function insertReview(int $mediaId, int $rating, ?string $reviewText): bool
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) {
            return false;
        }

        $sql = "INSERT INTO user_reviews (media_id, rating, review_text)
                VALUES (:media_id, :rating, :review_text)";

        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':media_id'   => $mediaId,
                ':rating'     => $rating,
                ':review_text'=> $reviewText,
            ]);
        } catch (PDOException $e) {
            ErrorLogger::log("INSERT REVIEW ERROR: " . $e->getMessage());
            return false;
        }
    }


    /*
     * GET MEDIA + RATINGS for the Reviews page
     * (this is basically your current get-reviews.php SQL :contentReference[oaicite:7]{index=7})
     */
    public static function getAllMediaWithRatings(): array
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) {
            return [];
        }

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
            ErrorLogger::log("GET MEDIA W/ RATINGS ERROR: " . $e->getMessage());
            return [];
        }
    }
}
