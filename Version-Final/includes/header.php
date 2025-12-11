<?php
// ============================================================
// header.php — Shared site layout header
// Used by ALL public pages loaded through index.php?page=...
// ============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Star Media Review</title>

    <!-- Global Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">

    <!-- Page-Specific Public Styles -->
    <link rel="stylesheet" href="/assets/css/media-details.css">
    <link rel="stylesheet" href="/assets/css/write-review.css">
    <link rel="stylesheet" href="/assets/css/RequestMedia.css">

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
