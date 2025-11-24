<?php
require_once __DIR__ . '/includes/DatabaseConnector.php';
require_once __DIR__ . '/includes/starstarmediareviewdatabase.php'; 
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

    switch ($clean) {

        case "movie":
        case "movies":
            return "Movie";

        case "tv show":
        case "tv shows":
        case "tv series":
        case "television show":
        case "television series":
        case "series":
            return "TV Show";

        case "video game":
        case "video games":
        case "game":
        case "games":
            return "Video Game";

        case "comic book":
        case "comic books":
        case "comic":
        case "comics":
            return "Comic Book";

        case "manga":
            return "Manga";

        case "novel":
        case "novels":
            return "Novel";

        case "web novel":
        case "web novels":
        case "webnovel":
            return "Web Novel";

        case "web series":
        case "webseries":
        case "web serie":
            return "Web Series";

        case "audio book":
        case "audio books":
        case "audiobook":
        case "audiobooks":
            return "Audio Book";

        default:
            return ucwords($clean);
    }
}

// ======================================================================
// Attempt DB Connection
// ======================================================================
try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    echo json_encode([
        "success" => false,
        "error"   => "Database connection failed."
    ]);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {

    // ------------------------------------------------------------------
    // GET ALL MEDIA (used by reviews.html)
    // ------------------------------------------------------------------
    case 'getMedia':

        try {
            $media = StarStarMediaReviewDatabase::getAllMediaWithRatings($pdo);

            // Normalize all media types before sending JSON
            foreach ($media as &$item) {
                if (isset($item['media_type'])) {
                    $item['media_type'] = normalizeTypeName($item['media_type']);
                }
            }
            unset($item);

            echo json_encode([
                "success" => true,
                "media"   => $media
            ]);
            exit;

        } catch (Throwable $e) {
            echo json_encode([
                "success" => false,
                "error"   => "Query failed: " . $e->getMessage()
            ]);
            exit;
        }

    // ------------------------------------------------------------------
    // Default: invalid action
    // ------------------------------------------------------------------
    default:
        echo json_encode([
            "success" => false,
            "error"   => "Invalid action."
        ]);
        exit;
}

?>
