<?php
/**
 * Add Expense Action Handler
 * Personal Expense Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../add_expense.php");
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', "Security verification failed. Please try again.");
    header("Location: ../add_expense.php");
    exit();
}

$userId         = getCurrentUserId();
$amount         = (float)($_POST['amount'] ?? 0);
$categoryId     = (int)($_POST['category_id'] ?? 0);
$paymentMethod  = trim($_POST['payment_method'] ?? 'Cash');
$txnDate        = trim($_POST['transaction_date'] ?? date('Y-m-d'));
$description    = trim($_POST['description'] ?? '');

$errors = [];

if ($amount <= 0) {
    $errors[] = "Amount must be greater than 0.";
}

if ($categoryId <= 0) {
    $errors[] = "Please select a valid expense category.";
}

if (empty($txnDate) || !strtotime($txnDate)) {
    $errors[] = "Please enter a valid date.";
}

if (!empty($errors)) {
    setFlash('error', implode(' ', $errors));
    header("Location: ../add_expense.php");
    exit();
}

try {
    $pdo = getDBConnection();

    // Verify category exists and is of type 'expense'
    $catCheck = $pdo->prepare("SELECT category_name FROM categories WHERE category_id = ? AND type = 'expense'");
    $catCheck->execute([$categoryId]);
    $catRow = $catCheck->fetch();

    if (!$catRow) {
        setFlash('error', "Selected category is not a valid expense category.");
        header("Location: ../add_expense.php");
        exit();
    }

    $categoryName = $catRow['category_name'];

    // Insert the expense record
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, category_id, amount, type, payment_method, transaction_date, description) 
                           VALUES (?, ?, ?, 'expense', ?, ?, ?)");
    $stmt->execute([
        $userId,
        $categoryId,
        $amount,
        $paymentMethod,
        $txnDate,
        $description ?: 'Expense payment'
    ]);

    // Check if user has set a budget for this category for the transaction's month/year
    $txnMonth = (int)date('m', strtotime($txnDate));
    $txnYear  = (int)date('Y', strtotime($txnDate));

    $budgetStmt = $pdo->prepare("SELECT amount FROM budgets WHERE user_id = ? AND category_id = ? AND month = ? AND year = ?");
    $budgetStmt->execute([$userId, $categoryId, $txnMonth, $txnYear]);
    $budgetRow = $budgetStmt->fetch();

    $flashMsg = "Expense of ₹" . number_format($amount, 2) . " added successfully.";

    if ($budgetRow) {
        $budgetLimit = (float)$budgetRow['amount'];
        // Calculate total spent in this month for this category
        $spentStmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS total_spent 
                                    FROM transactions 
                                    WHERE user_id = ? AND category_id = ? AND type = 'expense' 
                                    AND MONTH(transaction_date) = ? AND YEAR(transaction_date) = ?");
        $spentStmt->execute([$userId, $categoryId, $txnMonth, $txnYear]);
        $totalSpent = (float)($spentStmt->fetch()['total_spent'] ?? 0);

        $percent = $budgetLimit > 0 ? round(($totalSpent / $budgetLimit) * 100, 1) : 0;

        if ($percent >= 100) {
            $over = $totalSpent - $budgetLimit;
            $flashMsg .= " ⚠️ ATTENTION: You have EXCEEDED your {$categoryName} budget by ₹" . number_format($over, 2) . " ({$percent}% spent)!";
        } elseif ($percent >= 80) {
            $rem = $budgetLimit - $totalSpent;
            $flashMsg .= " ⚠️ Warning: You have reached {$percent}% of your {$categoryName} budget (Only ₹" . number_format($rem, 2) . " remaining).";
        }
    }

    setFlash('success', $flashMsg);
    header("Location: ../transactions.php");
    exit();

} catch (PDOException $e) {
    error_log("Add expense error: " . $e->getMessage());
    setFlash('error', "Unable to record expense transaction. Please try again.");
    header("Location: ../add_expense.php");
    exit();
}
