<?php
// Load session handler
require_once __DIR__ . '/admin-session.php';

// Load required includes (Version-036)
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

// If user is already logged in, redirect to admin dashboard
if (is_admin_logged_in()) {
    header('Location: admin-dashboard.php');
    exit;
}

$error = '';

// ------------------------------------------------------
// HANDLE FORM SUBMISSION
// ------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = clean_string('username', INPUT_POST);
    $password = clean_string('password', INPUT_POST);

    if ($username === '' || $password === '') {
        $error = "Please enter a username and password.";
    } else {
        try {
            $pdo = DatabaseConnector::getConnection();
            if (!$pdo) {
                throw new Exception("Database connection failed.");
            }

            // 1) Look up admin by username
            $stmt = $pdo->prepare("SELECT admin_id, username, password FROM admin WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2) If found, verify the hashed password
            if ($admin && verify_password_hash($password, $admin['password'])) {

                // Successful login – store session info
                $_SESSION['admin_id']   = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['username'];

                // Redirect to dashboard
                header('Location: admin-dashboard.php');
                exit;

            } else {
                // Either username not found or password mismatch
                $error = "Invalid username or password.";
            }

        } catch (Exception $e) {
            log_error("Error in admin-login.php: " . $e->getMessage());
            $error = "An error occurred during login. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Star Media Review</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>

<main class="admin-login-main">
    <section class="admin-login-container">
        <h1>Admin Login</h1>

        <?php if ($error): ?>
            <p class="admin-error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="post" class="admin-login-form">
            <label for="username">Username</label>
            <input
                type="text"
                name="username"
                id="username"
                required
                autocomplete="username"
            >

            <label for="password">Password</label>
            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="current-password"
            >

            <button type="submit" class="admin-btn">Log In</button>
        </form>

        <p class="return-link">
            <a href="../public/index.php"
                    style="text-decoration: none; color: #F4D47C; border-bottom: 1px solid transparent;"
                    onmouseover="this.style.borderBottomColor='#D4AF37';"
                    onmouseout="this.style.borderBottomColor='transparent';">
                &larr; Return to website
            </a>
        </p>
    </section>
</main>

</body>
</html>
