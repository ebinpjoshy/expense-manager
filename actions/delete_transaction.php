<?php
/**
 * Delete Transaction Action Handler
 * Personal Expense Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$userId = getCurrentUserId();
$txnId = (int)($_POST['transaction_id'] ?? $_GET['id'] ?? 0);

$prefix = (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/actions/') !== false) ? '../' : '';

// Determine redirect destination (default to transactions.php or safe referrer)
$redirectPage = 'transactions.php';
if (!empty($_SERVER['HTTP_REFERER'])) {
    $refererPath = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    $refererFile = basename($refererPath);
    if (in_array($refererFile, ['dashboard.php', 'transactions.php', 'reports.php'])) {
        $refererQuery = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
        $redirectPage = $refererFile . ($refererQuery ? '?' . $refererQuery : '');
    }
}

if ($txnId <= 0) {
    setFlash('error', "Invalid transaction ID.");
    header("Location: " . $prefix . $redirectPage);
    exit();
}

try {
    $pdo = getDBConnection();

    // Verify ownership and delete atomically
    $stmt = $pdo->prepare("DELETE FROM transactions WHERE transaction_id = ? AND user_id = ?");
    $stmt->execute([$txnId, $userId]);

    if ($stmt->rowCount() > 0) {
        setFlash('success', "Transaction #{$txnId} has been successfully deleted.");
    } else {
        setFlash('error', "Transaction not found or you are not authorized to delete it.");
    }

} catch (PDOException $e) {
    error_log("Delete transaction error: " . $e->getMessage());
    setFlash('error', "Failed to delete transaction due to database error.");
}

header("Location: " . $prefix . $redirectPage);
exit();
