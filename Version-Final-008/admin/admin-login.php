<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/admin-session.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

$error = "";

// Process form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {
        $error = "Please enter both username and password.";
    } else {

        $stmt = $pdo->prepare("
            SELECT admin_id, admin_name, admin_password
            FROM admins
            WHERE admin_name = :username
            LIMIT 1
        ");
        $stmt->execute([":username" => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {

            // Plaintext check (matches your DB)
            if ($password === $admin["admin_password"]) {

                $_SESSION["admin_logged_in"] = true;
                $_SESSION["admin_id"] = $admin["admin_id"];
                $_SESSION["admin_name"] = $admin["admin_name"];

                header("Location: /admin/admin-dashboard.php");
                exit;

            } else {
                $error = "Invalid password.";
            }

        } else {
            $error = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/assets/css/admin-style.css">
</head>

<body class="admin-login-body">

    <!-- HEADER BAR -->
    <div class="admin-login-header-bar">
        <h1 class="admin-login-header-title">ADMIN LOGIN</h1>
    </div>

    <!-- LOGIN BOX -->
    <div class="admin-login-wrapper">

        <?php if (!empty($error)): ?>
            <p class="admin-login-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="admin-login-box">

            <label for="username" class="admin-label">ADMIN NAME:</label>
            <input type="text" name="username" id="username" class="admin-input">

            <label for="password" class="admin-label">ADMIN PASSWORD:</label>
            <input type="password" name="password" id="password" class="admin-input">

            <button type="submit" class="admin-submit-btn">LOG IN</button>

        </form>

        <p class="admin-return-link">
            ← <a href="/index.php">Return to Website</a>
        </p>

    </div>

    <!-- FOOTER -->
    <div class="admin-login-footer">
        © 2025 Star Media Review — Admin Panel
    </div>

</body>
</html>
