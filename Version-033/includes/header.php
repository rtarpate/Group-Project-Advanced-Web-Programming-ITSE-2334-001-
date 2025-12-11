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
    <a href="/groupproject/Version-033/router/router.php?page=home">Home</a>
    <a href="/groupproject/Version-033/router/router.php?page=reviews">Reviews</a>
    <a href="/groupproject/Version-033/router/router.php?page=write-review">Write Review</a>
    <a href="/groupproject/Version-033/router/router.php?page=request-new-media">Request New Media</a>
</nav>

</header>

<main>
