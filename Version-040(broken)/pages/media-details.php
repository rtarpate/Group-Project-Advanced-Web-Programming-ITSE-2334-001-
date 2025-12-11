<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

$mediaId = isset($_GET['media_id']) ? (int) $_GET['media_id'] : 0;

$media = null;
$reviews = [];

if ($mediaId > 0) {

    // Correct SQL for Version-039 database
    $sql = "
        SELECT 
            m.media_id,
            m.title,
            m.release_date,
            m.director,
            m.image_path,

            mt.type_name AS media_type,
            g.genre_name AS genre,
            cr.rating_code AS content_rating,
            cr.description AS content_rating_desc,

            mr.average_rating,
            mr.total_ratings

        FROM media m
        LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
        LEFT JOIN genres g ON m.genre_id = g.genre_id
        LEFT JOIN content_ratings cr ON m.content_rating_id = cr.rating_id
        LEFT JOIN media_ratings mr ON m.media_id = mr.media_id

        WHERE m.media_id = :media_id
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':media_id' => $mediaId]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch user reviews for this media
    $reviewsSql = "
        SELECT 
            r.review_id,
            r.rating,
            r.review_text,
            r.created_at
        FROM user_reviews r
        WHERE r.media_id = :media_id
        ORDER BY r.created_at DESC
    ";

    $stmtReviews = $pdo->prepare($reviewsSql);
    $stmtReviews->execute([':media_id' => $mediaId]);
    $reviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Media Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/media-details.css">
</head>
<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="media-details-page">

<?php if (!$media): ?>

    <p>Media not found.</p>

<?php else: ?>

    <div class="media-details-layout">

        <div class="media-details-poster">
            <img src="../assets/images/<?php echo htmlspecialchars($media['image_path']); ?>"
                 alt="<?php echo htmlspecialchars($media['title']); ?>">
        </div>

        <div class="media-details-info">
            <h1><?php echo htmlspecialchars($media['title']); ?></h1>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($media['media_type']); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($media['genre']); ?></p>
            <p><strong>Release Date:</strong> <?php echo htmlspecialchars($media['release_date']); ?></p>
            <p><strong>Director:</strong> <?php echo htmlspecialchars($media['director']); ?></p>
            <p><strong>Content Rating:</strong> 
                <?php echo htmlspecialchars($media['content_rating']); ?>
                (<?php echo htmlspecialchars($media['content_rating_desc']); ?>)
            </p>
            <p><strong>Average Rating:</strong> 
                <?php echo $media['average_rating'] !== null ? htmlspecialchars($media['average_rating']) : 'No ratings yet'; ?>
            </p>
            <p><strong>Total Ratings:</strong> 
                <?php echo $media['total_ratings'] !== null ? htmlspecialchars($media['total_ratings']) : 0; ?>
            </p>

            <a href="reviews.php" class="btn-details">Back to Reviews</a>
        </div>

    </div>

    <!-- USER REVIEWS -->
    <section class="media-details-reviews">
        <h2>User Reviews</h2>

        <ul class="user-review-list">

        <?php if (empty($reviews)): ?>
            <p>No reviews yet for this media.</p>
        <?php else: ?>
            <?php foreach ($reviews as $r): ?>
                <li class="user-review-item">
                    <p><strong>Rating:</strong> <?php echo htmlspecialchars($r['rating']); ?> / 10</p>
                    <p><?php echo nl2br(htmlspecialchars($r['review_text'])); ?></p>
                    <small>Posted: <?php echo htmlspecialchars($r['created_at']); ?></small>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>

        </ul>
    </section>

<?php endif; ?>

</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
