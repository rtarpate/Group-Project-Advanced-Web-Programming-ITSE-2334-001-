<?php
require_once __DIR__ . '/admin-session.php';
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../../assets/logs/ErrorLogger.php';

if (is_admin_logged_in()) {
    header('Location: admin-dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean('username', 'string', INPUT_POST);
    $password = clean('password', 'string', INPUT_POST);

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $db = DatabaseConnector::getConnection();
        if (!$db) {
            $error = 'Database connection error.';
        } else {
            try {
                $sql = "SELECT * FROM admins WHERE admin_name = :name LIMIT 1";
                $stmt = $db->prepare($sql);
                $stmt->execute([':name' => $username]);
                $admin = $stmt->fetch();

                if ($admin && $admin['admin_password'] === $password) {
                    // For production, use password_hash & password_verify instead!
                    $_SESSION['admin_id']   = $admin['admin_id'];
                    $_SESSION['admin_name'] = $admin['admin_name'];

                    header('Location: admin-dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }

            } catch (PDOException $e) {
                ErrorLogger::log("ADMIN LOGIN ERROR: " . $e->getMessage());
                $error = 'Internal error. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Star Media Reviews</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<header>
    <h1>Star Media Reviews - Admin Login</h1>
</header>

<div class="container">
    <h2>Admin Login</h2>

    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" action="admin-login.php">
        <label for="username">Admin Name:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Admin Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Log In</button>
    </form>
</div>

</body>
</html>
