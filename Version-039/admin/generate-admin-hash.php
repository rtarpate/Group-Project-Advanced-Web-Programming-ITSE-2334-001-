<?php
// Temporary script to generate a password hash for an admin password.
// DELETE THIS FILE AFTER YOU'RE DONE.

require_once __DIR__ . '/../includes/helper.php';

$plainPassword = 'Password001'; // <-- change this if you want a different password

$hash = hash_password($plainPassword);

echo "<p>Plain password: <strong>" . htmlspecialchars($plainPassword) . "</strong></p>";
echo "<p>Hashed password:</p>";
echo "<pre>" . htmlspecialchars($hash) . "</pre>";

?>