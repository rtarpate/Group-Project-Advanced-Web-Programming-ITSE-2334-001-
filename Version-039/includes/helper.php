<?php
/**
 * ------------------------------------------------------------
 * helper.php  (Version-035)
 * Global validation, sanitization, and safety functions
 * ------------------------------------------------------------
 * These functions are used across the entire project to:
 * - Clean user input
 * - Prevent XSS
 * - Validate required fields
 * - Validate strings & lengths
 * - Safely escape output
 * ------------------------------------------------------------
 */


/* ------------------------------------------------------------
   BASIC INPUT CLEANING (used for displaying inputs safely)
   ------------------------------------------------------------ */
function clean_input($data) {
    if (!isset($data)) return "";

    // Remove surrounding spaces
    $data = trim($data);

    // Remove backslashes
    $data = stripslashes($data);

    // Convert special characters to safe HTML entities
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}


/* ------------------------------------------------------------
   BASIC SANITIZATION (more aggressive than clean_input)
   Removes harmful characters entirely instead of encoding them
   ------------------------------------------------------------ */
function sanitize_input($data) {
    if (!isset($data)) return "";

    $data = trim($data);

    // Strip possible script tags or malicious content
    $data = strip_tags($data);

    // Remove remaining special characters
    $data = filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);

    return $data;
}


/* ------------------------------------------------------------
   REQUIRED FIELD VALIDATION
   Returns true if string contains value, false if empty
   ------------------------------------------------------------ */
function validate_required($data) {
    return isset($data) && trim($data) !== "";
}


/* ------------------------------------------------------------
   VALIDATE LENGTH
   Ensures input is within a character range
   Example: validate_length($name, 3, 50)
   ------------------------------------------------------------ */
function validate_length($data, $min, $max) {
    if (!isset($data)) return false;

    $length = strlen(trim($data));
    return ($length >= $min && $length <= $max);
}


/* ------------------------------------------------------------
   ESCAPE OUTPUT FOR SAFE HTML DISPLAY
   Use this when printing database values into HTML
   ------------------------------------------------------------ */
function escape_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}


/* ------------------------------------------------------------
   OPTIONAL: SAFE SQL STRING CLEANING
   Use this BEFORE sending text to database ONLY IF NOT using PDO bindings.
   NOTE: Your project uses PDO prepared statements, so this is not required.
   Keeping it for flexibility in other scripts.
   ------------------------------------------------------------ */
function safe_sql_string($data) {
    if (!isset($data)) return "";
    return addslashes(trim($data));
}


/**
 * Legacy function kept for backward compatibility.
 * Maps old clean() usage to the new clean_input() method.
 */
function clean($data) {
    return clean_input($data);
}

/* ------------------------------------------------------------
   NEW FORM INPUT CLEANER (safe for login, forms, etc.)
   This reads real values from $_POST or $_GET properly.
   ------------------------------------------------------------ */
function clean_form_input($key, $type = 'string', $method = INPUT_POST)
{
    // Retrieve raw value from form
    $value = filter_input($method, $key, FILTER_UNSAFE_RAW);

    if ($value === null || $value === false) {
        return '';
    }

    // Trim whitespace
    $value = trim($value);

    // Sanitize based on type
    switch ($type) {
        case 'int':
            return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
        case 'email':
            return filter_var($value, FILTER_SANITIZE_EMAIL);
        case 'string':
        default:
            return filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
}

/* ------------------------------------------------------------
   PASSWORD HASHING HELPERS (for admin accounts)
   ------------------------------------------------------------ */

/**
 * Hash a plain-text password using PHP's built-in password_hash().
 */
function hash_password(string $plainPassword): string
{
    return password_hash($plainPassword, PASSWORD_DEFAULT);
}

/**
 * Verify a plain-text password against a hashed password.
 */
function verify_password_hash(string $plainPassword, string $storedHash): bool
{
    if (empty($storedHash)) {
        return false;
    }
    return password_verify($plainPassword, $storedHash);
}

?>
