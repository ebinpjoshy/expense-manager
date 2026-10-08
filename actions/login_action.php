<?php
/**
 * User Login Action Handler
 * Personal Expense Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

// Verify CSRF
if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', "Invalid session or CSRF token mismatch.");
    header("Location: ../login.php");
    exit();
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    setFlash('error', "Invalid email or password.");
    header("Location: ../login.php");
    exit();
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT user_id, name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);

        $_SESSION['user_id']    = (int)$user['user_id'];
        $_SESSION['user_name']  = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        setFlash('success', "Welcome back, " . htmlspecialchars($user['name']) . "!");
        header("Location: ../dashboard.php");
        exit();
    } else {
        // Uniform message to prevent user enumeration
        setFlash('error', "Invalid email or password.");
        header("Location: ../login.php");
        exit();
    }

} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    setFlash('error', "A database error occurred during login. Please try again.");
    header("Location: ../login.php");
    exit();
}
