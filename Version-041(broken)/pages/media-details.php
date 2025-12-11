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

        WHERE m.media_id = ?
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$mediaId]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch user reviews
    $stmt2 = $pdo->prepare("
        SELECT rating, review_text, review_date
        FROM user_reviews
        WHERE media_id = ?
        ORDER BY review_date DESC
    ");
    $stmt2->execute([$mediaId]);
    $reviews = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>
        <?php echo $media ? htmlspecialchars($media['title']) : "Media Details"; ?>
    </title>

    <link rel="stylesheet" href="/groupproject/Version-039/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-039/assets/css/media-details.css">
</head>

<body>

<?php include __DIR__ . '/../includes/header.php'; ?>

<main class="media-details-page">

<?php if (!$media): ?>
    <h2 class="page-title">Media Not Found</h2>
    <p>We couldn't locate this media item.</p>

<?php else: ?>

    <h2 class="page-title"><?php echo htmlspecialchars($media['title']); ?></h2>

    <div class="media-details-layout">

        <!-- IMAGE -->
        <div class="media-details-image">
            <img src="/groupproject/Version-039/assets/images/<?php echo htmlspecialchars($media['image_path']); ?>" 
             alt="<?php echo htmlspecialchars($media['title']); ?>">
        </div>

        <!-- DETAILS -->
        <div class="media-details-main">

            <p><strong>Type:</strong> <?php echo htmlspecialchars($media['media_type']); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($media['genre']); ?></p>
            <p><strong>Release Date:</strong> <?php echo htmlspecialchars($media['release_date']); ?></p>
            <p><strong>Director / Creator:</strong> <?php echo htmlspecialchars($media['director']); ?></p>

            <p><strong>Content Rating:</strong> 
                <?php echo htmlspecialchars($media['content_rating']); ?>
                – <?php echo htmlspecialchars($media['content_rating_desc']); ?>
            </p>

            <div class="media-details-rating-box">
                Average Rating: 
                <?php 
                    echo $media['average_rating'] !== null 
                        ? number_format($media['average_rating'], 1) . " / 4"
                        : "No reviews yet";
                ?>
                <br>
                <small>(<?php echo (int)$media['total_ratings']; ?> ratings)</small>
            </div>

            <a href="/Version-039/pages/reviews.php" class="btn-details">Back to Reviews</a>
        </div>

    </div>

    <!-- USER REVIEWS -->
    <section class="media-details-reviews">
        <h2>User Reviews</h2>

        <ul class="user-review-list">

        <?php if (empty($reviews)): ?>
            <p>No reviews yet.</p>

        <?php else: ?>
            <?php foreach ($reviews as $rev): ?>
                <li class="user-review-item">
                    <p><?php echo nl2br(htmlspecialchars($rev['review_text'])); ?></p>
                    <small>Rating: <?php echo htmlspecialchars($rev['rating']); ?>/4</small><br>
                    <small>Posted on: <?php echo htmlspecialchars($rev['review_date']); ?></small>
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
