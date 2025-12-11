<?php
// media-details.php
// Display full information for a single media item



require_once __DIR__ . '/../includes/DatabaseConnector.php';

// ------------------------------------------------------
// Get DB connection
// ------------------------------------------------------
$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

// ------------------------------------------------------
// Get and validate media_id from query string
// (reviews.js links as: media-details.php?media_id=123)
// ------------------------------------------------------
$mediaId = isset($_GET['media_id']) ? (int) $_GET['media_id'] : 0;

$media    = null;
$reviews  = [];

if ($mediaId > 0) {

    // --------------------------------------------------
    // Fetch main media details + type + genre + rating
    // --------------------------------------------------
    $sql = "
        SELECT 
            m.media_id,
            m.title,
            m.release_date,
            m.director,
            m.image_path,
            mt.type_name       AS media_type,
            g.genre_name       AS genre_name,
            cr.rating_code     AS content_rating,
            cr.description     AS content_rating_desc,
            AVG(r.rating)      AS average_rating,
            COUNT(r.review_id) AS review_count
        FROM media m
        LEFT JOIN media_types mt 
            ON m.media_type_id = mt.media_type_id
        LEFT JOIN genres g 
            ON m.genre_id = g.genre_id
        LEFT JOIN content_ratings cr
            ON m.content_rating_id = cr.rating_id
        LEFT JOIN user_reviews r 
            ON m.media_id = r.media_id
        WHERE m.media_id = :id
        GROUP BY m.media_id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $mediaId]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);

    // --------------------------------------------------
    // If media exists, fetch all user reviews for it
    // --------------------------------------------------
    if ($media) {
        $sqlReviews = "
            SELECT 
                rating,
                review_text,
                review_date
            FROM user_reviews
            WHERE media_id = :id
            ORDER BY review_date DESC
        ";

        $stmtReviews = $pdo->prepare($sqlReviews);
        $stmtReviews->execute([':id' => $mediaId]);
        $reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="page-section media-details-page">
    <?php if (!$media): ?>
        <h1 class="page-title">Media Not Found</h1>
        <p>We couldn't find that media item. It may have been removed or the link may be incorrect.</p>
        <p><a href="reviews.php" class="btn-details">Back to Reviews</a></p>

    <?php else: ?>
        <?php
            // Image: use DB value or fall back to 0.jpg
            $imgFile = !empty($media['image_path']) ? $media['image_path'] : '0.jpg';
            $imgSrc  = "../assets/images/" . $imgFile;

            // Release date formatting
            $releaseText = !empty($media['release_date'])
                ? date("F j, Y", strtotime($media['release_date']))
                : "Unknown";

            // Average rating text
            $avg = isset($media['average_rating']) ? (float)$media['average_rating'] : 0;
            $avgText = $media['review_count'] > 0
                ? number_format($avg, 2) . " / 10 ({$media['review_count']} review" . ($media['review_count'] == 1 ? "" : "s") . ")"
                : "No user ratings yet";
        ?>

        <h1 class="page-title"><?php echo htmlspecialchars($media['title']); ?></h1>

        <div class="media-details-layout">
            <div class="media-details-image">
                <img src="<?php echo htmlspecialchars($imgSrc); ?>"
                     alt="<?php echo htmlspecialchars($media['title']); ?>">
            </div>

            <div class="media-details-main">
                <p><strong>Type:</strong>
                    <?php echo htmlspecialchars($media['media_type'] ?? "Unknown"); ?>
                </p>

                <p><strong>Genre:</strong>
                    <?php echo htmlspecialchars($media['genre_name'] ?? "N/A"); ?>
                </p>

                <p><strong>Content Rating:</strong>
                    <?php
                        $code = $media['content_rating'] ?? "N/A";
                        $desc = $media['content_rating_desc'] ?? "";
                        echo htmlspecialchars($code);
                        if (!empty($desc)) {
                            echo " — " . htmlspecialchars($desc);
                        }
                    ?>
                </p>

                <p><strong>Release Date:</strong>
                    <?php echo htmlspecialchars($releaseText); ?>
                </p>

                <p><strong>Director / Author:</strong>
                    <?php echo htmlspecialchars($media['director']); ?>
                </p>

                <p class="media-details-rating">
                    <strong>Average Rating:</strong> <?php echo htmlspecialchars($avgText); ?>
                </p>

                <p>
                    <a href="write-review.php?media_id=<?php echo (int)$media['media_id']; ?>"
                       class="btn-details">
                        Write a Review for this Media
                    </a>
                </p>
            </div>
        </div>

        <section class="media-details-reviews">
            <h2>User Reviews</h2>

            <?php if (empty($reviews)): ?>
                <p>No user reviews yet. Be the first to review this media!</p>
            <?php else: ?>
                <ul class="user-review-list">
                    <?php foreach ($reviews as $r): ?>
                        <li class="user-review-item">
                            <p><strong>Rating:</strong>
                                <?php echo (int)$r['rating']; ?> / 10
                            </p>

                            <?php if (!empty($r['review_text'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($r['review_text'])); ?></p>
                            <?php else: ?>
                                <p><em>No review text provided.</em></p>
                            <?php endif; ?>

                            <small>
                                Posted on:
                                <?php
                                    $dt = !empty($r['review_date'])
                                        ? date("F j, Y g:i A", strtotime($r['review_date']))
                                        : "";
                                    echo htmlspecialchars($dt);
                                ?>
                            </small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
