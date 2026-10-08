<?php
/**
 * User Logout Action
 * Personal Expense Management System
 */

require_once __DIR__ . '/includes/auth.php';

// Unset all session variables
$_SESSION = [];

// Delete the session cookie if present
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session on server
session_destroy();

// Start fresh session just to deliver logout flash message
session_start();
setFlash('success', "You have been logged out securely.");

header("Location: login.php");
exit();
