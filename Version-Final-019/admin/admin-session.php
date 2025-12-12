<?php
// admin-session.php
// Start session and provide helper to enforce admin login

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Require that an admin is logged in, otherwise redirect to login page.
 */
function require_admin(): void
{
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: /admin/admin-login.php");
        exit;
    }
}
