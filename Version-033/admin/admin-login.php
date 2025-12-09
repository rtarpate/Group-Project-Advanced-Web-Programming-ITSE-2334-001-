<?php
require_once __DIR__ . '/admin-session.php';
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

if (is_admin_logged_in()) {
    header('Location: admin-dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean('username', 'string', INPUT_POST);
    $password = clean('password', 'string', INPUT_POST);

    if (!$username || !$password) {
        $error = 'Please enter both username and password.';
    } else {
        $pdo = DatabaseConnector::getConnection();
        if (!$pdo) {
            $error = 'Failed to connect to the database.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    SELECT admin_id, username, password_hash
                    FROM admin
                    WHERE username = :username
                    LIMIT 1
                ");
                $stmt->execute([':username' => $username]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($admin && password_verify($password, $admin['password_hash'])) {
                    admin_login($admin['admin_id'], $admin['username']);
                    header('Location: admin-dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            } catch (Throwable $e) {
                ErrorLogger::log("ADMIN LOGIN ERROR: " . $e->getMessage());
                $error = 'An error occurred during login.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/admin-style.css">
</head>
<body class="admin-body">
<div class="admin-login-wrapper">
    <h1>Admin Login</h1>

    <?php if ($error): ?>
        <div class="admin-message error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" class="admin-form admin-login-form">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit" class="admin-btn">Login</button>
    </form>
</div>
</body>
</html>
