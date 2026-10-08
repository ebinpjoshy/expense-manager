<?php
/**
 * Budget Management Action Handler
 * Personal Expense Management System
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../budget.php");
    exit();
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
    setFlash('error', "Security verification failed. Please try again.");
    header("Location: ../budget.php");
    exit();
}

$userId = getCurrentUserId();
$action = $_POST['action'] ?? 'set';

try {
    $pdo = getDBConnection();

    if ($action === 'delete') {
        $budgetId = (int)($_POST['budget_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM budgets WHERE budget_id = ? AND user_id = ?");
        $stmt->execute([$budgetId, $userId]);
        setFlash('success', "Budget limit removed successfully.");
        header("Location: ../budget.php");
        exit();
    }

    // Set / Update budget
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $amount     = (float)($_POST['amount'] ?? 0);
    $month      = (int)($_POST['month'] ?? date('n'));
    $year       = (int)($_POST['year'] ?? date('Y'));

    if ($categoryId <= 0 || $amount <= 0 || $month < 1 || $month > 12 || $year < 2000) {
        setFlash('error', "Please provide a valid category, positive amount, month, and year.");
        header("Location: ../budget.php");
        exit();
    }

    // Use INSERT ... ON DUPLICATE KEY UPDATE to prevent duplicates
    $sql = "INSERT INTO budgets (user_id, category_id, amount, month, year) 
            VALUES (?, ?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE amount = VALUES(amount)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $categoryId, $amount, $month, $year]);

    setFlash('success', "Budget of ₹" . number_format($amount, 2) . " successfully saved for " . date('F Y', mktime(0, 0, 0, $month, 10, $year)) . ".");
    header("Location: ../budget.php?month=" . $month . "&year=" . $year);
    exit();

} catch (PDOException $e) {
    error_log("Budget action error: " . $e->getMessage());
    setFlash('error', "Unable to update budget limit. Database error.");
    header("Location: ../budget.php");
    exit();
}
