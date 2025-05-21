<?php
session_start();

// Clear all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Delete all cookies
foreach ($_COOKIE as $key => $value) {
    setcookie($key, '', time() - 3600, '/'); // Expire 1 hour ago
}

// Clear cookies from current request
$_COOKIE = [];

header("Location: ../pages/aast/uniUserLogin.php");
exit();
?>