<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . "DatabaseConnector.php";

class StarStarMediaDatabase {

    /*
     * INSERT REQUEST (request-new-media form)
     */
    public static function insertMediaRequest($mediaName, $mediaType, $mediaDescription) {
        $db = DatabaseConnector::getConnection();
        if (!$db) return false;

        $sql = "INSERT INTO newmediarequest (media_name, media_type, media_description)
                VALUES (:name, :type, :description)";

        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':name'        => $mediaName,
                ':type'        => $mediaType,
                ':description' => $mediaDescription
            ]);
        } catch (PDOException $e) {
            ErrorLogger::log("INSERT REQUEST ERROR: " . $e->getMessage());
            return false;
        }
    }



    /*
     * INSERT REVIEW (write-review form)
     */
    public static function insertReview($mediaTitle, $rating, $reviewText) {
        $db = DatabaseConnector::getConnection();
        if (!$db) return false;

        $sql = "INSERT INTO reviews (media_title, rating, review_text)
                VALUES (:title, :rating, :review)";

        try {
            $stmt = $db->prepare($sql);
            return $stmt->execute([
                ':title'  => $mediaTitle,
                ':rating' => $rating,
                ':review' => $reviewText
            ]);
        } catch (PDOException $e) {
            ErrorLogger::log("INSERT REVIEW ERROR: " . $e->getMessage());
            return false;
        }
    }



    /*
     * GET ALL REVIEWS (reviews.html)
     */
    public static function getAllReviews() {
        $db = DatabaseConnector::getConnection();
        if (!$db) return [];

        try {
            $stmt = $db->query("SELECT * FROM reviews ORDER BY review_id DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            ErrorLogger::log("GET ALL REVIEWS ERROR: " . $e->getMessage());
            return [];
        }
    }
}
