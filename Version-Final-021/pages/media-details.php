<?php
// pages/media-details.php
// NOTE: This file is included by index.php (header/footer already included).
// Do NOT output <html>, <head>, or include header/footer here.

require_once __DIR__ . '/../includes/DatabaseConnector.php';

$debug = isset($_GET['debug']) && $_GET['debug'] == '1';

function h($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

if ($debug) {
    echo "<pre style='padding:12px;border:1px solid #ccc;background:#fff;max-width:1100px;overflow:auto;'>";
    echo "DEBUG: pages/media-details.php loaded\n";
}

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    if ($debug) {
        echo "DEBUG: Database connection FAILED\n</pre>";
    }
    echo "<p style='padding:20px;'>Database connection failed.</p>";
    return;
}

$mediaId = isset($_GET['media_id']) ? (int)$_GET['media_id'] : 0;

if ($debug) {
    echo "DEBUG: media_id = {$mediaId}\n";
}

if ($mediaId <= 0) {
    if ($debug) echo "DEBUG: Invalid media_id\n</pre>";
    echo "<p style='padding:20px;'>Invalid media selected.</p>";
    return;
}

try {
    // ---- Media details (matches your DB schema) ----
    $sqlMedia = "
        SELECT
            m.media_id,
            m.title,
            m.release_date,
            m.director,
            m.image_path,
            mt.type_name     AS media_type,
            g.genre_name     AS genre_name,
            cr.rating_code   AS content_rating_code,
            cr.description   AS content_rating_desc
        FROM media m
        LEFT JOIN media_types mt      ON m.media_type_id = mt.media_type_id
        LEFT JOIN genres g            ON m.genre_id = g.genre_id
        LEFT JOIN content_ratings cr  ON m.content_rating_id = cr.rating_id
        WHERE m.media_id = :id
        LIMIT 1
    ";

    if ($debug) {
        echo "DEBUG: Media SQL prepared\n";
    }

    $stmt = $pdo->prepare($sqlMedia);
    $stmt->execute([':id' => $mediaId]);
    $media = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$media) {
        if ($debug) echo "DEBUG: No media row found\n</pre>";
        echo "<p style='padding:20px;'>Media not found.</p>";
        return;
    }

    // ---- Reviews (ALL reviews for this media) ----
    $sqlReviews = "
        SELECT review_text, rating, review_date
        FROM user_reviews
        WHERE media_id = :id
        ORDER BY review_date DESC
    ";
    $stmtR = $pdo->prepare($sqlReviews);
    $stmtR->execute([':id' => $mediaId]);
    $reviews = $stmtR->fetchAll(PDO::FETCH_ASSOC);

    // ---- Average rating ----
    $sqlAvg = "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM user_reviews WHERE media_id = :id";
    $stmtA = $pdo->prepare($sqlAvg);
    $stmtA->execute([':id' => $mediaId]);
    $avgRow = $stmtA->fetch(PDO::FETCH_ASSOC);

    $avgRating = isset($avgRow['avg_rating']) ? (float)$avgRow['avg_rating'] : 0.0;
    $totalReviews = isset($avgRow['total_reviews']) ? (int)$avgRow['total_reviews'] : 0;

    if ($debug) {
        echo "DEBUG: Media row loaded\n";
        echo "DEBUG: Reviews found = {$totalReviews}\n";
        echo "</pre>";
    }

    // Image handling (DB stores like "54.jpg")
    $imgFile = trim((string)($media['image_path'] ?? ''));
    if ($imgFile === '') $imgFile = 'no-image.jpg';
    $imgUrl  = "/assets/images/" . $imgFile;
    $fallbackUrl = "/assets/images/no-image.jpg";

} catch (Throwable $e) {
    if ($debug) {
        echo "<pre style='padding:12px;border:1px solid #c00;background:#fff;max-width:1100px;overflow:auto;'>";
        echo "DEBUG: Exception\n";
        echo h($e->getMessage()) . "\n";
        echo "</pre>";
    }
    echo "<p style='padding:20px;'>An error occurred loading this media.</p>";
    return;
}
?>

<div class="media-details-page">
    <h1 class="media-details-title">Media Details</h1>

    <div class="media-details-card">
        <div class="media-details-left">
            <img
                class="media-details-poster"
                src="<?= h($imgUrl) ?>"
                alt="<?= h($media['title']) ?>"
                onerror="this.onerror=null;this.src='<?= h($fallbackUrl) ?>';"
            />
        </div>

        <div class="media-details-right">
            <h2 class="media-details-name"><?= h($media['title']) ?></h2>

            <ul class="media-details-meta">
                <li><strong>Type:</strong> <?= h($media['media_type'] ?? 'N/A') ?></li>
                <li><strong>Genre:</strong> <?= h($media['genre_name'] ?? 'N/A') ?></li>
                <li>
                    <strong>Content Rating:</strong>
                    <?= h($media['content_rating_code'] ?? 'N/A') ?>
                    <?php if (!empty($media['content_rating_desc'])): ?>
                        <span class="muted">— <?= h($media['content_rating_desc']) ?></span>
                    <?php endif; ?>
                </li>
                <li><strong>Release Date:</strong> <?= h($media['release_date'] ?? 'N/A') ?></li>
                <li><strong>Director:</strong> <?= h($media['director'] ?? 'N/A') ?></li>
                <li><strong>Average Rating:</strong> <?= number_format($avgRating, 1) ?> / 10 <span class="muted">(<?= (int)$totalReviews ?> reviews)</span></li>
            </ul>

            <div class="media-details-actions">
                <a class="btn" href="/index.php?page=reviews">← Back to Reviews</a>
                <a class="btn btn-primary" href="/index.php?page=write-review">Write a Review</a>
            </div>
        </div>
    </div>

    <h2 class="reviews-heading">User Reviews</h2>

    <?php if (empty($reviews)): ?>
        <div class="review-empty">No reviews yet. Be the first to write one!</div>
    <?php else: ?>
        <div class="reviews-list">
            <?php foreach ($reviews as $r): ?>
                <div class="review-card">
                    <?php if (!empty($r['review_text'])): ?>
                        <div class="review-text"><?= nl2br(h($r['review_text'])) ?></div>
                    <?php else: ?>
                        <div class="review-text muted">(No written review)</div>
                    <?php endif; ?>

                    <div class="review-meta">
                        <span><strong>Rating:</strong> <?= (int)$r['rating'] ?>/10</span>
                        <span class="muted">— <?= h($r['review_date']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
