<?php
// ============================================================
// header.php — Shared site layout header
// Used by ALL public pages loaded through index.php?page=...
// ============================================================

$pageTitle = $pageTitle ?? 'Star Media Review';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle); ?></title>

    <!-- Global Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <?php if (!empty($extraCss) && is_array($extraCss)): ?>
        <?php foreach ($extraCss as $cssPath): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($cssPath) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

<header>
    <nav>
        <ul class="main-nav">
            <li><a href="/index.php">Home</a></li>
            <li><a href="/index.php?page=reviews">Reviews</a></li>
            <li><a href="/index.php?page=write-review">Write a Review</a></li>
            <li><a href="/index.php?page=request-new-media">Request New Media</a></li>
        </ul>
    </nav>
</header>

<main>
