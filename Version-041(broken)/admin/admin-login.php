<?php
// admin-login.php
// Admin login page and authentication

require_once __DIR__ . '/admin-session.php';
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

// If already logged in, go straight to the dashboard
if (is_admin_logged_in()) {
    header('Location: admin-dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ------------------------------------------------------
    // 1. Clean input
    // ------------------------------------------------------
    $username = clean_form_input('username', 'string', INPUT_POST);
    $password = clean_form_input('password', 'string', INPUT_POST);

    // Basic validation
    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // --------------------------------------------------
        // 2. Get DB connection
        // --------------------------------------------------
        $pdo = DatabaseConnector::getConnection();

        if (!$pdo) {
            // Connection failed; log and show generic error
            ErrorLogger::log('ADMIN LOGIN: Database connection failed.');
            $error = 'An unexpected error occurred. Please try again later.';
        } else {
            try {
                // --------------------------------------------------
                // 3. Look up admin by username
                //    ASSUMES TABLE:
                //      admin(admin_id, username, password_hash)
                // --------------------------------------------------
                $sql = "
                    SELECT admin_id, username, password_hash
                    FROM admin
                    WHERE username = :username
                    LIMIT 1
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':username' => $username]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($admin && verify_password_hash($password, $admin['password_hash'])) {
                    // --------------------------------------------------
                    // 4. Success: set session and redirect
                    // --------------------------------------------------
                    $_SESSION['admin_id']   = $admin['admin_id'];
                    $_SESSION['admin_name'] = $admin['username'];

                    header('Location: admin-dashboard.php');
                    exit;
                } else {
                    // Invalid credentials
                    $error = 'Invalid username or password.';
                }
            } catch (PDOException $e) {
                ErrorLogger::log('ADMIN LOGIN QUERY ERROR: ' . $e->getMessage());
                $error = 'An unexpected error occurred. Please try again later.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Star Media Review</title>

    <!-- NOTE:
         Update "/groupproject/Version-041" below if your local URL is different.
         Using absolute paths so this works from any folder.
    -->
    <link rel="stylesheet" href="/groupproject/Version-041/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-041/assets/css/admin-style.css">
</head>
<body class="admin-body">
    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <h1 class="admin-title">Admin Login</h1>

            <?php if (!empty($error)): ?>
                <div class="admin-error-message">
                    <?php echo escape_output($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="admin-login.php" class="admin-login-form" novalidate>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        required
                        value="<?php echo escape_output($username ?? ''); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Log In
                    </button>
                    <a
                        href="/groupproject/Version-041/index.php"
                        class="btn btn-secondary"
                    >
                        &larr; Return to Website
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
