<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';

$pdo = DatabaseConnector::getConnection();
if (!$pdo) die("Database connection failed.");

$mediaId = isset($_GET['media_id']) ? (int)$_GET['media_id'] : 0;

$media   = null;
$reviews = [];

if ($mediaId > 0) {

    $sql = "
        SELECT 
            m.media_id,
            m.title,
            m.release_date,
            m.director,
            m.image_path,
            mt.type_name AS media_type,
            g.genre_name AS genre_name,
            cr.rating_code AS content_rating,
            cr.description AS content_rating_desc,
            mr.average_rating,
            mr.total_ratings
        FROM media m
        LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
        LEFT JOIN genres g ON m.genre_id = g.genre_id
        LEFT JOIN content_ratings cr ON m.content_rating_id = cr.rating_id
        LEFT JOIN media_ratings mr ON m.media_id = mr.media_id
        WHERE m.media_id = :id
        LIMIT 1
    ";


    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $mediaId]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);

    // Reviews
    $stmt = $pdo->prepare("
        SELECT rating, review_text, review_date
        FROM user_reviews
        WHERE media_id = :id
        ORDER BY review_date DESC
    ");
    $stmt->execute([':id' => $mediaId]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include __DIR__ . '/../includes/header.php';
?>

<h1 class="page-title">Media Details</h1>

<?php if (!$media): ?>

    <p>Media not found.</p>

<?php else: ?>

<div class="media-details-container">

    <div class="media-image-block">
        <img src="/assets/images/<?= htmlspecialchars($media['image_path']); ?>" alt="">
    </div>

    <div class="media-info-block">
        <h2><?= htmlspecialchars($media['title']); ?></h2>

        <p><strong>Type:</strong> <?= htmlspecialchars($media['media_type']); ?></p>
        <p><strong>Genre:</strong> <?= htmlspecialchars($media['genre_name'] ?? 'N/A'); ?></p>

        <p>
            <strong>Content Rating:</strong> 
            <?= htmlspecialchars($media['content_rating'] ?? 'N/A'); ?>
            <?php if (!empty($media['content_rating_desc'])): ?>
                (<?= htmlspecialchars($media['content_rating_desc']); ?>)
            <?php endif; ?>
        </p>

        <?php if ($media['average_rating'] !== null): ?>
        <p>
            <strong>Average Rating:</strong>
            <?= htmlspecialchars($media['average_rating']); ?>/10
            (<?= htmlspecialchars($media['total_ratings']); ?> ratings)
        </p>
        <?php endif; ?>

        <p><strong>Release Date:</strong> <?= htmlspecialchars($media['release_date']); ?></p>
        <p><strong>Director:</strong> <?= htmlspecialchars($media['director']); ?></p>

    </div>
</div>

<section class="user-reviews">
    <h3>User Reviews</h3>

    <?php if (empty($reviews)): ?>
        <p>No reviews yet.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($reviews as $rev): ?>
                <li>
                    <p><?= nl2br(htmlspecialchars($rev['review_text'])); ?></p>
                    <small>Rating: <?= $rev['rating']; ?>/10</small><br>
                    <small><?= $rev['review_date']; ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</section>

<?php endif; ?>
