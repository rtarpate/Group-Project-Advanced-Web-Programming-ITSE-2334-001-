<?php
// generate-admin-hash.php
// Utility page: generate a password_hash for admin accounts

require_once __DIR__ . '/../includes/helper.php';

$result = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plain = $_POST['plain_password'] ?? '';
    if ($plain !== '') {
        $hash   = password_hash($plain, PASSWORD_DEFAULT);
        $result = $hash;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Generate Admin Password Hash</title>
    <link rel="stylesheet" href="/assets/css/admin-style.css">
</head>
<body class="admin-body">

<div class="admin-login-container">
    <h1>Generate Admin Password Hash</h1>

    <form method="POST" class="admin-login-form">
        <label for="plain_password">Plaintext Password</label>
        <input type="text" id="plain_password" name="plain_password" required>

        <button type="submit">Generate Hash</button>
    </form>

    <?php if ($result): ?>
        <div class="admin-hash-result">
            <h2>Result</h2>
            <p><strong>Hash:</strong></p>
            <pre><?= htmlspecialchars($result); ?></pre>
            <p>Copy this hash into the <code>password_hash</code> column of your <code>admin</code> table.</p>
        </div>
    <?php endif; ?>

    <p class="admin-login-return">
        <a href="/index.php">Return to Website</a>
    </p>
</div>

</body>
</html>
