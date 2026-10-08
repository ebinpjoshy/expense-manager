<?php
/**
 * Core Helper and Financial Analysis Functions
 * Personal Expense Management System
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Format a numeric amount to Indian Rupee representation.
 *
 * @param float|int|string $amount
 * @return string
 */
function formatCurrency($amount): string {
    return '₹' . number_format((float)$amount, 2);
}

/**
 * Format a MySQL date (YYYY-MM-DD) into readable format (DD/MM/YYYY).
 *
 * @param string $dateStr
 * @return string
 */
function formatDate(string $dateStr): string {
    if (empty($dateStr)) return '-';
    $time = strtotime($dateStr);
    return $time ? date('d/m/Y', $time) : $dateStr;
}

/**
 * Resolve start and end dates based on selected period.
 *
 * @param string $period
 * @param string|null $customStart
 * @param string|null $customEnd
 * @return array{start_date: string, end_date: string, label: string}
 */
function resolveDateRange(string $period = 'this_month', ?string $customStart = null, ?string $customEnd = null): array {
    $today = date('Y-m-d');

    switch ($period) {
        case 'this_week':
            // Monday to Sunday of the current week
            $start = date('Y-m-d', strtotime('monday this week'));
            $end = date('Y-m-d', strtotime('sunday this week'));
            $label = "This Week (" . date('d M', strtotime($start)) . " - " . date('d M', strtotime($end)) . ")";
            break;

        case 'last_week':
            // Monday to Sunday of the previous week
            $start = date('Y-m-d', strtotime('monday last week'));
            $end = date('Y-m-d', strtotime('sunday last week'));
            $label = "Last Week (" . date('d M', strtotime($start)) . " - " . date('d M', strtotime($end)) . ")";
            break;

        case 'last_month':
            $start = date('Y-m-01', strtotime('first day of last month'));
            $end = date('Y-m-t', strtotime('last day of last month'));
            $label = "Last Month (" . date('F Y', strtotime($start)) . ")";
            break;

        case 'this_year':
            $start = date('Y-01-01');
            $end = date('Y-12-31');
            $label = "This Year (" . date('Y') . ")";
            break;

        case 'custom':
            $start = !empty($customStart) ? $customStart : date('Y-m-01');
            $end = !empty($customEnd) ? $customEnd : $today;
            $label = "Custom Range (" . formatDate($start) . " to " . formatDate($end) . ")";
            break;

        case 'this_month':
        default:
            $start = date('Y-m-01');
            $end = date('Y-m-t');
            $label = "This Month (" . date('F Y') . ")";
            $period = 'this_month';
            break;
    }

    return [
        'start_date' => $start,
        'end_date'   => $end,
        'period'     => $period,
        'label'      => $label,
    ];
}

/**
 * Fetch user details by ID.
 *
 * @param int $userId
 * @return array|null
 */
function getUserById(int $userId): ?array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT user_id, name, email, phone, created_at FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    return $user ?: null;
}

/**
 * Calculate total income for a user within a date range.
 *
 * @param int $userId
 * @param string|null $startDate
 * @param string|null $endDate
 * @return float
 */
function getTotalIncome(int $userId, ?string $startDate = null, ?string $endDate = null): float {
    $pdo = getDBConnection();
    $sql = "SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'income'";
    $params = [$userId];

    if ($startDate && $endDate) {
        $sql .= " AND transaction_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return (float)($row['total'] ?? 0);
}

/**
 * Calculate total expenses for a user within a date range.
 *
 * @param int $userId
 * @param string|null $startDate
 * @param string|null $endDate
 * @return float
 */
function getTotalExpenses(int $userId, ?string $startDate = null, ?string $endDate = null): float {
    $pdo = getDBConnection();
    $sql = "SELECT COALESCE(SUM(amount), 0) AS total FROM transactions WHERE user_id = ? AND type = 'expense'";
    $params = [$userId];

    if ($startDate && $endDate) {
        $sql .= " AND transaction_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return (float)($row['total'] ?? 0);
}

/**
 * Calculate remaining balance (Income - Expense).
 *
 * @param int $userId
 * @param string|null $startDate
 * @param string|null $endDate
 * @return float
 */
function getBalance(int $userId, ?string $startDate = null, ?string $endDate = null): float {
    return getTotalIncome($userId, $startDate, $endDate) - getTotalExpenses($userId, $startDate, $endDate);
}

/**
 * Get total number of transactions in period.
 *
 * @param int $userId
 * @param string|null $startDate
 * @param string|null $endDate
 * @return int
 */
function getTransactionCount(int $userId, ?string $startDate = null, ?string $endDate = null): int {
    $pdo = getDBConnection();
    $sql = "SELECT COUNT(*) AS total_count FROM transactions WHERE user_id = ?";
    $params = [$userId];

    if ($startDate && $endDate) {
        $sql .= " AND transaction_date BETWEEN ? AND ?";
        $params[] = $startDate;
        $params[] = $endDate;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return (int)($row['total_count'] ?? 0);
}

/**
 * Get category-wise expense breakdown with amounts and percentages.
 *
 * @param int $userId
 * @param string $startDate
 * @param string $endDate
 * @return array
 */
function getCategoryExpenses(int $userId, string $startDate, string $endDate): array {
    $pdo = getDBConnection();
    $sql = "SELECT c.category_id, c.category_name, COALESCE(SUM(t.amount), 0) AS total_amount, COUNT(t.transaction_id) as txn_count
            FROM transactions t
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.user_id = ? AND t.type = 'expense' AND t.transaction_date BETWEEN ? AND ?
            GROUP BY c.category_id, c.category_name
            ORDER BY total_amount DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $startDate, $endDate]);
    $results = $stmt->fetchAll();

    $grandTotal = array_sum(array_column($results, 'total_amount'));

    foreach ($results as &$item) {
        $item['percentage'] = $grandTotal > 0 ? round(($item['total_amount'] / $grandTotal) * 100, 1) : 0;
    }
    unset($item);

    return $results;
}

/**
 * Get payment method breakdown for expenses in the period.
 *
 * @param int $userId
 * @param string $startDate
 * @param string $endDate
 * @return array
 */
function getPaymentMethodExpenses(int $userId, string $startDate, string $endDate): array {
    $pdo = getDBConnection();
    $sql = "SELECT payment_method, COALESCE(SUM(amount), 0) AS total_amount, COUNT(transaction_id) as txn_count
            FROM transactions
            WHERE user_id = ? AND type = 'expense' AND transaction_date BETWEEN ? AND ?
            GROUP BY payment_method
            ORDER BY total_amount DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $startDate, $endDate]);
    $results = $stmt->fetchAll();

    $grandTotal = array_sum(array_column($results, 'total_amount'));

    foreach ($results as &$item) {
        $item['percentage'] = $grandTotal > 0 ? round(($item['total_amount'] / $grandTotal) * 100, 1) : 0;
    }
    unset($item);

    return $results;
}

/**
 * Get recent transactions for a user.
 *
 * @param int $userId
 * @param int $limit
 * @return array
 */
function getRecentTransactions(int $userId, int $limit = 5): array {
    $pdo = getDBConnection();
    $sql = "SELECT t.*, c.category_name
            FROM transactions t
            JOIN categories c ON t.category_id = c.category_id
            WHERE t.user_id = ?
            ORDER BY t.transaction_date DESC, t.transaction_id DESC
            LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $userId, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Get day-by-day weekly expenses (Monday to Sunday) for the given week.
 *
 * @param int $userId
 * @param string $mondayDate
 * @return array
 */
function getWeeklyDailyExpenses(int $userId, string $mondayDate): array {
    $pdo = getDBConnection();
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $chartData = [];

    for ($i = 0; $i < 7; $i++) {
        $currentDate = date('Y-m-d', strtotime("$mondayDate +$i days"));
        $dayName = $days[$i];

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS daily_total 
                               FROM transactions 
                               WHERE user_id = ? AND type = 'expense' AND transaction_date = ?");
        $stmt->execute([$userId, $currentDate]);
        $row = $stmt->fetch();
        $total = (float)($row['daily_total'] ?? 0);

        $chartData[] = [
            'day'   => $dayName,
            'date'  => $currentDate,
            'short' => date('d M', strtotime($currentDate)),
            'total' => $total,
        ];
    }

    return $chartData;
}

/**
 * Get monthly week-by-week breakdown (Week 1 to Week 5) for current or selected month.
 *
 * @param int $userId
 * @param int $month
 * @param int $year
 * @return array
 */
function getMonthlyWeeklyBreakdown(int $userId, int $month, int $year): array {
    $pdo = getDBConnection();
    $daysInMonth = (int)date('t', strtotime("$year-$month-01"));

    // Standard monthly grouping: Days 1-7 (W1), 8-14 (W2), 15-21 (W3), 22-28 (W4), 29+ (W5)
    $weeks = [
        'Week 1' => ['start' => 1, 'end' => 7],
        'Week 2' => ['start' => 8, 'end' => 14],
        'Week 3' => ['start' => 15, 'end' => 21],
        'Week 4' => ['start' => 22, 'end' => 28],
    ];
    if ($daysInMonth > 28) {
        $weeks['Week 5'] = ['start' => 29, 'end' => $daysInMonth];
    }

    $result = [];
    foreach ($weeks as $weekName => $range) {
        $startDate = sprintf('%04d-%02d-%02d', $year, $month, $range['start']);
        $endDate   = sprintf('%04d-%02d-%02d', $year, $month, $range['end']);

        $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS total 
                               FROM transactions 
                               WHERE user_id = ? AND type = 'expense' AND transaction_date BETWEEN ? AND ?");
        $stmt->execute([$userId, $startDate, $endDate]);
        $row = $stmt->fetch();

        $result[] = [
            'week'       => $weekName,
            'range'      => "Day {$range['start']}-{$range['end']}",
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'total'      => (float)($row['total'] ?? 0),
        ];
    }

    return $result;
}

/**
 * Get monthly trend for the past 6 or 12 months for reports and charts.
 *
 * @param int $userId
 * @param int $year
 * @return array
 */
function getMonthlyTrend(int $userId, int $year): array {
    $pdo = getDBConnection();
    $monthsData = [];

    for ($m = 1; $m <= 12; $m++) {
        $monthName = date('M', mktime(0, 0, 0, $m, 10));
        $startDate = sprintf('%04d-%02d-01', $year, $m);
        $endDate   = date('Y-m-t', strtotime($startDate));

        // Income
        $stmtInc = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions 
                                  WHERE user_id = ? AND type = 'income' AND transaction_date BETWEEN ? AND ?");
        $stmtInc->execute([$userId, $startDate, $endDate]);
        $inc = (float)($stmtInc->fetch()['total'] ?? 0);

        // Expense
        $stmtExp = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM transactions 
                                  WHERE user_id = ? AND type = 'expense' AND transaction_date BETWEEN ? AND ?");
        $stmtExp->execute([$userId, $startDate, $endDate]);
        $exp = (float)($stmtExp->fetch()['total'] ?? 0);

        $monthsData[] = [
            'month_num'  => $m,
            'month_name' => $monthName,
            'income'     => $inc,
            'expense'    => $exp,
            'savings'    => $inc - $exp,
        ];
    }

    return $monthsData;
}

/**
 * Get budget comparison and status for a given month and year.
 * Compares Budget vs Actual Expenses with warning thresholds.
 *
 * @param int $userId
 * @param int $month
 * @param int $year
 * @return array
 */
function getBudgetStatus(int $userId, int $month, int $year): array {
    $pdo = getDBConnection();
    $startDate = sprintf('%04d-%02d-01', $year, $month);
    $endDate   = date('Y-m-t', strtotime($startDate));

    // Fetch all budgets set by user for this month
    $sql = "SELECT b.budget_id, b.category_id, b.amount AS budget_amount, b.month, b.year, c.category_name
            FROM budgets b
            JOIN categories c ON b.category_id = c.category_id
            WHERE b.user_id = ? AND b.month = ? AND b.year = ?
            ORDER BY c.category_name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $month, $year]);
    $budgets = $stmt->fetchAll();

    $report = [];
    foreach ($budgets as $b) {
        $catId = (int)$b['category_id'];
        $budgetAmount = (float)$b['budget_amount'];

        // Get actual spending in this category for this month
        $stmtExp = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) AS spent 
                                  FROM transactions 
                                  WHERE user_id = ? AND category_id = ? AND type = 'expense' 
                                  AND transaction_date BETWEEN ? AND ?");
        $stmtExp->execute([$userId, $catId, $startDate, $endDate]);
        $spent = (float)($stmtExp->fetch()['spent'] ?? 0);

        $remaining = $budgetAmount - $spent;
        $percentage = $budgetAmount > 0 ? round(($spent / $budgetAmount) * 100, 1) : 0;

        // Status determinations
        if ($percentage >= 100) {
            $status = 'exceeded';
            $statusClass = 'danger';
            $message = "Budget exceeded! You are over budget by " . formatCurrency(abs($remaining)) . ".";
        } elseif ($percentage >= 80) {
            $status = 'warning';
            $statusClass = 'warning';
            $message = "Warning: You are close to your " . htmlspecialchars($b['category_name']) . " budget ({$percentage}% spent).";
        } else {
            $status = 'normal';
            $statusClass = 'success';
            $message = "On track. " . formatCurrency($remaining) . " remaining.";
        }

        $report[] = [
            'budget_id'     => $b['budget_id'],
            'category_id'   => $catId,
            'category_name' => $b['category_name'],
            'budget_amount' => $budgetAmount,
            'spent_amount'  => $spent,
            'remaining'     => $remaining,
            'percentage'    => $percentage,
            'status'        => $status,
            'status_class'  => $statusClass,
            'message'       => $message,
        ];
    }

    return $report;
}

/**
 * Get all categories by type ('income', 'expense', or null for all).
 *
 * @param string|null $type
 * @return array
 */
function getCategories(?string $type = null): array {
    $pdo = getDBConnection();
    if ($type) {
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE type = ? ORDER BY category_name ASC");
        $stmt->execute([$type]);
    } else {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY type ASC, category_name ASC");
    }
    return $stmt->fetchAll();
}
