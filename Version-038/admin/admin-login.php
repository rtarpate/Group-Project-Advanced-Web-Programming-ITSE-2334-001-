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

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Safely read form fields
    $username = clean_form_input('username', 'string', INPUT_POST);
    $password = clean_form_input('password', 'string', INPUT_POST);

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';

    } else {
        $pdo = DatabaseConnector::getConnection();

        if (!$pdo) {
            $error = 'Unable to connect to the database. Please try again later.';
        } else {
            try {
                // 1) Fetch the admin by username ONLY
                $sql = "
                    SELECT admin_id, username, password
                    FROM admin
                    WHERE username = :username
                    LIMIT 1
                ";
                $stmt = $pdo->prepare($sql);
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
                    $error = 'Invalid username or password.';
                }

            } catch (PDOException $e) {
                ErrorLogger::log("ADMIN LOGIN ERROR: " . $e->getMessage());
                $error = 'An unexpected error occurred during login. Please try again.';
            }
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Safely read form input (using updated helper.php)
    $username = clean_form_input('username', 'string', INPUT_POST);
    $password = clean_form_input('password', 'string', INPUT_POST);

    // Ensure both fields are provided
    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {

        // Get DB connection
        $db = DatabaseConnector::getConnection();

        if (!$db) {
            $error = 'Database connection error.';
        } else {
            try {
                // Prepare and execute lookup
                $sql = "SELECT * FROM admins WHERE admin_name = :name LIMIT 1";
                $stmt = $db->prepare($sql);
                $stmt->execute([':name' => $username]);
                $admin = $stmt->fetch();

                // Compare plaintext passwords (matches your SQL file)
                if ($admin && $admin['admin_password'] === $password) {

                    // Store admin session info
                    $_SESSION['admin_id']   = $admin['admin_id'];
                    $_SESSION['admin_name'] = $admin['admin_name'];

                    // Redirect to dashboard
                    header('Location: admin-dashboard.php');
                    exit;

                } else {
                    $error = 'Invalid username or password.';
                }

            } catch (PDOException $e) {
                // Log detailed error
                ErrorLogger::log("ADMIN LOGIN ERROR: " . $e->getMessage());

                // Show generic message to user
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

    <!-- Correct Version-036 stylesheet paths -->
    <link rel="stylesheet" href="/groupproject/Version-036/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-036/assets/css/admin-style.css">
</head>

<body class="admin-body">
<div class="admin-wrapper">

    <header class="admin-header">
        <div class="admin-header-inner">
            <h1>Admin Login</h1>
        </div>
    </header>

    <main class="admin-main">
        <section class="admin-panel">

            <!-- Error message -->
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="post" action="admin-login.php" class="admin-form">

                <label for="username">Admin Name:</label>
                <input type="text" name="username" id="username" required>

                <label for="password">Admin Password:</label>
                <input type="password" name="password" id="password" required>

                <button type="submit" class="admin-btn">Log In</button>
            </form>

            <p class="return-link">
                <a href="/groupproject/Version-036/public/index.php"
                    style="text-decoration: none; color: #F4D47C; border-bottom: 1px solid transparent;"
                    onmouseover="this.style.borderBottomColor='#D4AF37';"
                    onmouseout="this.style.borderBottomColor='transparent';">
                    &larr; Return to Website
                </a>

            </p>

        </section>
    </main>

    <footer class="admin-footer">
        <p>&copy; <?php echo date('Y'); ?> Star Media Review — Admin Panel</p>
    </footer>

</div>
</body>
</html>
