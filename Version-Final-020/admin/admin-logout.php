<?php
// admin-logout.php

require_once __DIR__ . '/admin-session.php';

$_SESSION = [];
session_unset();
session_destroy();

header("Location: /index.php");
exit;
