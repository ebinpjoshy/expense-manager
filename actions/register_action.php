<?php
/**
 * User Registration Action Handler
 * Personal Expense Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../register.php");
    exit();
}

// Verify CSRF
if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', "Invalid session or CSRF token mismatch.");
    header("Location: ../register.php");
    exit();
}

$name            = trim($_POST['name'] ?? '');
$email           = trim($_POST['email'] ?? '');
$phone           = trim($_POST['phone'] ?? '');
$password        = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Server-side validation
$errors = [];

if (mb_strlen($name) < 2) {
    $errors[] = "Full name must be at least 2 characters long.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please provide a valid email address.";
}

$cleanedPhone = preg_replace('/\D/', '', $phone);
if (strlen($cleanedPhone) !== 10) {
    $errors[] = "Phone number must be exactly 10 digits.";
}

if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters.";
} elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/', $password)) {
    $errors[] = "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
}

if ($password !== $confirmPassword) {
    $errors[] = "Password and Confirm Password do not match.";
}

if (!empty($errors)) {
    setFlash('error', implode(' ', $errors));
    header("Location: ../register.php");
    exit();
}

try {
    $pdo = getDBConnection();

    // Check if email already exists
    $checkStmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        setFlash('error', "An account with this email already exists. Please login instead.");
        header("Location: ../register.php");
        exit();
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert user record
    $insertStmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $insertStmt->execute([$name, $email, $cleanedPhone, $hashedPassword]);

    setFlash('success', "Registration successful. Please login.");
    header("Location: ../login.php");
    exit();

} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    setFlash('error', "A database error occurred during registration. Please try again.");
    header("Location: ../register.php");
    exit();
}
