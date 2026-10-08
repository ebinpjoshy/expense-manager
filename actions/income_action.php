<?php
/**
 * Add Income Action Handler
 * Personal Expense Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../add_income.php");
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', "Security verification failed. Please try again.");
    header("Location: ../add_income.php");
    exit();
}

$userId         = getCurrentUserId();
$amount         = (float)($_POST['amount'] ?? 0);
$categoryId     = (int)($_POST['category_id'] ?? 0);
$paymentMethod  = trim($_POST['payment_method'] ?? 'Bank');
$txnDate        = trim($_POST['transaction_date'] ?? date('Y-m-d'));
$description    = trim($_POST['description'] ?? '');

$errors = [];

if ($amount <= 0) {
    $errors[] = "Amount must be greater than 0.";
}

if ($categoryId <= 0) {
    $errors[] = "Please select a valid income category.";
}

if (empty($txnDate) || !strtotime($txnDate)) {
    $errors[] = "Please enter a valid date.";
}

if (!empty($errors)) {
    setFlash('error', implode(' ', $errors));
    header("Location: ../add_income.php");
    exit();
}

try {
    $pdo = getDBConnection();

    // Verify category exists and is of type 'income'
    $catCheck = $pdo->prepare("SELECT category_id FROM categories WHERE category_id = ? AND type = 'income'");
    $catCheck->execute([$categoryId]);
    if (!$catCheck->fetch()) {
        setFlash('error', "Selected category is not a valid income category.");
        header("Location: ../add_income.php");
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, category_id, amount, type, payment_method, transaction_date, description) 
                           VALUES (?, ?, ?, 'income', ?, ?, ?)");
    $stmt->execute([
        $userId,
        $categoryId,
        $amount,
        $paymentMethod,
        $txnDate,
        $description ?: 'Income credit'
    ]);

    setFlash('success', "Income of ₹" . number_format($amount, 2) . " added successfully.");
    header("Location: ../transactions.php");
    exit();

} catch (PDOException $e) {
    error_log("Add income error: " . $e->getMessage());
    setFlash('error', "Unable to record income transaction. Please try again.");
    header("Location: ../add_income.php");
    exit();
}
