<?php

require_once __DIR__ . '/DatabaseConnector.php';

class StarStarMediaReviewDatabase 
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
            // Insert into user_reviews
            $sql = "INSERT INTO user_reviews (media_id, rating, review_text)
                    VALUES (:media_id, :rating, :review_text)";
            $stmt = $db->prepare($sql);
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

    // ===========================================
    // RECALC MEDIA_RATINGS FROM USER_REVIEWS
    // ===========================================
    public static function recalcMediaRatings(int $mediaId): bool
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return false;

        try {
            $ratingSql = "
                SELECT 
                    AVG(rating) AS avg_rating,
                    COUNT(*)    AS rating_count
                FROM user_reviews
                WHERE media_id = :media_id
            ";
            $stmt2 = $db->prepare($ratingSql);
            $stmt2->execute([':media_id' => $mediaId]);
            $result = $stmt2->fetch();

            $ratingCount = (int)($result['rating_count'] ?? 0);

            if ($ratingCount > 0) {
                $newAvg = round($result['avg_rating'], 2);

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
                    ':total_ratings'  => $ratingCount,
                ]);
            } else {
                // No ratings left – remove row from media_ratings
                $delSql = "DELETE FROM media_ratings WHERE media_id = :id";
                $stmtDel = $db->prepare($delSql);
                $stmtDel->execute([':id' => $mediaId]);
                return true;
            }

        } catch (PDOException $e) {
            ErrorLogger::log("RECALC MEDIA RATINGS ERROR: " . $e->getMessage());
            return false;
        }
    }

    // ===========================================
    // GET ALL MEDIA + RATINGS FOR REVIEWS PAGE
    // ===========================================
    public static function getAllMediaWithRatings(PDO $pdo) {
        $sql = "
            SELECT 
                m.media_id,
                m.title,
                m.image_path,
                g.genre_name AS genre,
                cr.rating_code AS content_rating,
                mt.type_name AS media_type,
                m.release_date,
                m.director,
                IFNULL(r.average_rating, 0) AS average_rating,
                IFNULL(r.total_ratings, 0) AS total_ratings
            FROM media m
            LEFT JOIN genres g ON m.genre_id = g.genre_id
            LEFT JOIN content_ratings cr ON m.content_rating_id = cr.rating_id
            LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
            LEFT JOIN media_ratings r ON m.media_id = r.media_id
            ORDER BY m.title ASC
        ";

        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ===========================================
    // GET ONE MEDIA'S FULL DETAILS
    // ===========================================
    public static function getMediaDetails(PDO $pdo, int $media_id) {
        $sql = "
            SELECT
                m.media_id,
                m.title,
                m.release_date,
                m.director,
                g.genre_name AS genre,
                cr.rating_code AS content_rating,
                mt.type_name AS media_type,
                m.image_path,
                IFNULL(r.average_rating, 0) AS average_rating,
                IFNULL(r.total_ratings, 0) AS total_ratings
            FROM media m
            LEFT JOIN genres g ON m.genre_id = g.genre_id
            LEFT JOIN content_ratings cr ON m.content_rating_id = cr.rating_id
            LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
            LEFT JOIN media_ratings r ON m.media_id = r.media_id
            WHERE m.media_id = :media_id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':media_id' => $media_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // ===========================================
    // GET ALL USER REVIEWS FOR ONE MEDIA
    // ===========================================
    public static function getMediaReviews(PDO $pdo, int $media_id) {
        $sql = "
            SELECT
                review_id,
                media_id,
                rating,
                review_text,
                created_at
            FROM user_reviews
            WHERE media_id = :media_id
            ORDER BY created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':media_id' => $media_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // ===========================================
    // ADMIN: GET ALL NEW MEDIA REQUESTS
    // ===========================================
    public static function getAllNewMediaRequests(): array
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return [];

        $sql = "SELECT * FROM newmediarequest ORDER BY request_date DESC";

        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorLogger::log("GET NEW MEDIA REQUESTS ERROR: " . $e->getMessage());
            return [];
        }
    }

    // ===========================================
    // ADMIN: DELETE NEW MEDIA REQUEST
    // ===========================================
    public static function deleteNewMediaRequest(int $requestId): bool
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return false;

        $sql = "DELETE FROM newmediarequest WHERE request_id = :id";

        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute([':id' => $requestId]);
        } catch (PDOException $e) {
            ErrorLogger::log("DELETE NEW MEDIA REQUEST ERROR: " . $e->getMessage());
            return false;
        }
    }

    // ===========================================
    // ADMIN: GET ALL USER REVIEWS (WITH MEDIA TITLE)
    // ===========================================
    public static function getAllUserReviewsWithMedia(): array
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return [];

        $sql = "
            SELECT 
                ur.review_id,
                ur.media_id,
                ur.rating,
                ur.review_text,
                ur.review_date,
                m.title
            FROM user_reviews ur
            INNER JOIN media m ON ur.media_id = m.media_id
            ORDER BY ur.review_date DESC
        ";

        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorLogger::log("GET USER REVIEWS ERROR: " . $e->getMessage());
            return [];
        }
    }

    // ===========================================
    // ADMIN: DELETE USER REVIEW + RECALC RATINGS
    // ===========================================
    public static function deleteUserReview(int $reviewId): bool
    {
        $db = DatabaseConnector::getConnection();
        if (!$db) return false;

        try {
            // Find which media this review belongs to
            $sql = "SELECT media_id FROM user_reviews WHERE review_id = :id LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([':id' => $reviewId]);
            $row  = $stmt->fetch();

            if (!$row) {
                return false;
            }

            $mediaId = (int)$row['media_id'];

            // Delete the review
            $delSql = "DELETE FROM user_reviews WHERE review_id = :id";
            $stmtDel = $db->prepare($delSql);
            $stmtDel->execute([':id' => $reviewId]);

            // Recalculate ratings
            return self::recalcMediaRatings($mediaId);

        } catch (PDOException $e) {
            ErrorLogger::log("DELETE USER REVIEW ERROR: " . $e->getMessage());
            return false;
        }
    }

    public static function submitReview(PDO $pdo, int $media_id, int $rating, string $review_text = null) {

        // 1. Insert new review
        $sql = "
            INSERT INTO user_reviews (media_id, rating, review_text)
            VALUES (:media_id, :rating, :review_text)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':media_id'    => $media_id,
            ':rating'      => $rating,
            ':review_text' => $review_text !== "" ? $review_text : null
        ]);

        // 2. Recalculate average rating
        $avgSQL = "
            SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews
            FROM user_reviews
            WHERE media_id = :media_id
        ";

        $stmt = $pdo->prepare($avgSQL);
        $stmt->execute([':media_id' => $media_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $newAvg   = round($row['avg_rating'], 2);
        $newCount = (int)$row['total_reviews'];

        // 3. Update media_ratings
        $updateSQL = "
            UPDATE media_ratings
            SET average_rating = :avg, total_ratings = :cnt
            WHERE media_id = :media_id
        ";

        $stmt = $pdo->prepare(updateSQL);
        $stmt->execute([
            ':avg'       => $newAvg,
            ':cnt'       => $newCount,
            ':media_id'  => $media_id
        ]);

        return true;
    }

}
