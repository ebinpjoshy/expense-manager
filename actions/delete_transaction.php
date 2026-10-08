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

if ($txnId <= 0) {
    setFlash('error', "Invalid transaction ID.");
    header("Location: ../transactions.php");
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

header("Location: ../transactions.php");
exit();
