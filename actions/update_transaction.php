<?php
/**
 * Update Transaction Action Handler
 * Personal Expense Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../transactions.php");
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', "Security verification failed. Please try again.");
    header("Location: ../transactions.php");
    exit();
}

$userId         = getCurrentUserId();
$txnId          = (int)($_POST['transaction_id'] ?? 0);
$amount         = (float)($_POST['amount'] ?? 0);
$categoryId     = (int)($_POST['category_id'] ?? 0);
$paymentMethod  = trim($_POST['payment_method'] ?? 'Cash');
$txnDate        = trim($_POST['transaction_date'] ?? date('Y-m-d'));
$description    = trim($_POST['description'] ?? '');

if ($txnId <= 0 || $amount <= 0 || $categoryId <= 0 || empty($txnDate)) {
    setFlash('error', "Invalid input values. Please review the transaction details.");
    header("Location: ../edit_transaction.php?id=" . $txnId);
    exit();
}

try {
    $pdo = getDBConnection();

    // Check ownership of transaction
    $checkStmt = $pdo->prepare("SELECT transaction_id, type FROM transactions WHERE transaction_id = ? AND user_id = ?");
    $checkStmt->execute([$txnId, $userId]);
    $existing = $checkStmt->fetch();

    if (!$existing) {
        setFlash('error', "Transaction not found or unauthorized access.");
        header("Location: ../transactions.php");
        exit();
    }

    // Update transaction
    $updateStmt = $pdo->prepare("UPDATE transactions 
                                 SET category_id = ?, amount = ?, payment_method = ?, transaction_date = ?, description = ? 
                                 WHERE transaction_id = ? AND user_id = ?");
    $updateStmt->execute([
        $categoryId,
        $amount,
        $paymentMethod,
        $txnDate,
        $description,
        $txnId,
        $userId
    ]);

    setFlash('success', "Transaction #{$txnId} updated successfully.");
    header("Location: ../transactions.php");
    exit();

} catch (PDOException $e) {
    error_log("Update transaction error: " . $e->getMessage());
    setFlash('error', "Unable to update transaction. Please try again.");
    header("Location: ../edit_transaction.php?id=" . $txnId);
    exit();
}
