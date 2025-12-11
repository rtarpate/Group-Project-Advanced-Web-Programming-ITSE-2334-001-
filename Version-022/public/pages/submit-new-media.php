<?php
$host = "localhost";
$dbname = "starstarmediareviewdatabase"; // ✅ match your phpMyAdmin DB name exactly
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $media_name = trim($_POST["media_name"] ?? '');
    $media_type = trim($_POST["media_type"] ?? '');
    $media_description = trim($_POST["media_description"] ?? '');

    if (!empty($media_name) && !empty($media_type) && !empty($media_description)) {
        try {
            $stmt = $conn->prepare("
                INSERT INTO newmediarequest (media_name, media_type, media_description)
                VALUES (:media_name, :media_type, :media_description)
            ");
            $stmt->bindParam(':media_name', $media_name);
            $stmt->bindParam(':media_type', $media_type);
            $stmt->bindParam(':media_description', $media_description);

            if ($stmt->execute()) {
                echo "Success";
            } else {
                echo "Insert failed.";
            }
        } catch (PDOException $e) {
            echo "Insert error: " . $e->getMessage();
        }
    } else {
        echo "Missing fields.";
    }
} else {
    echo "Not a POST request.";
}
?>
