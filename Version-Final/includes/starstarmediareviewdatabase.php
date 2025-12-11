<?php
// Legacy compatibility file
// Most admin/public pages now use DatabaseConnector.php

require_once __DIR__ . '/DatabaseConnector.php';

$pdo = DatabaseConnector::getConnection();

if (!$pdo) {
    die("Database connection failed (starstarmediareviewdatabase.php).");
}

return $pdo;
