<?php
/**
 * Authentication and Session Management
 * Personal Expense Management System
 */

// Start session if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if a user is currently logged in.
 *
 * @return bool
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Enforce that the user must be authenticated.
 * If not logged in, redirects to login.php.
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        $_SESSION['flash_error'] = "Please log in to access this page.";
        header("Location: login.php");
        exit();
    }
}

/**
 * Enforce that the user must be a guest (not logged in).
 * If logged in, redirects to dashboard.php.
 */
function requireGuest(): void {
    if (isLoggedIn()) {
        header("Location: dashboard.php");
        exit();
    }
}

/**
 * Get current logged in user ID.
 *
 * @return int|null
 */
function getCurrentUserId(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Get current logged in user display name.
 *
 * @return string
 */
function getCurrentUserName(): string {
    return htmlspecialchars($_SESSION['user_name'] ?? 'User');
}

/**
 * Generate CSRF token and store it in session.
 *
 * @return string
 */
function generateCSRFToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token.
 *
 * @param string|null $token
 * @return bool
 */
function verifyCSRFToken(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set a flash message for display on the next page.
 *
 * @param string $type 'success' or 'error'
 * @param string $message
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash_' . $type] = $message;
}

/**
 * Get and clear a flash message.
 *
 * @param string $type 'success' or 'error'
 * @return string|null
 */
function getFlash(string $type): ?string {
    $key = 'flash_' . $type;
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}
