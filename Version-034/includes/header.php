<?php
// header.php — Shared site header + navigation (PUBLIC SITE ONLY)

$base = dirname($_SERVER['SCRIPT_NAME']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Star Media Review</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/../assets/css/style.css">
</head>
<body>

<header>
    <nav>
        <ul class="main-nav">
            <li><a href="<?php echo $base; ?>/../public/index.php">Home</a></li>
            <li><a href="<?php echo $base; ?>/../pages/reviews.php">Reviews</a></li>
            <li><a href="<?php echo $base; ?>/../pages/write-review.php">Write a Review</a></li>
            <li><a href="<?php echo $base; ?>/../pages/request-new-media.php">Request New Media</a></li>
        </ul>
    </nav>
</header>

<main>
