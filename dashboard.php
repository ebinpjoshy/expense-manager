<?php
/**
 * Main Application Dashboard
 * ExpenseMgr — Personal Financial Intelligence System
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$userId = getCurrentUserId();
$pageTitle = "Financial Overview";

// -------------------------------------------------------------
// Period Selection & Date Range Resolution
// -------------------------------------------------------------
$period = $_GET['period'] ?? 'this_month';
$customStart = $_GET['start_date'] ?? null;
$customEnd = $_GET['end_date'] ?? null;

$range = resolveDateRange($period, $customStart, $customEnd);
$startDate = $range['start_date'];
$endDate   = $range['end_date'];
$periodLabel = $range['label'];

// -------------------------------------------------------------
// Fetch Analytics Data for Selected Period
// -------------------------------------------------------------
$totalIncome   = getTotalIncome($userId, $startDate, $endDate);
$totalExpenses = getTotalExpenses($userId, $startDate, $endDate);
$balance       = $totalIncome - $totalExpenses;
$txnCount      = getTransactionCount($userId, $startDate, $endDate);

// Category-wise Breakdown
$categoryData = getCategoryExpenses($userId, $startDate, $endDate);

// Payment Method Breakdown
$paymentData = getPaymentMethodExpenses($userId, $startDate, $endDate);

// Weekly Daily Breakdown (Monday to Sunday for current week)
$mondayDate = ($period === 'last_week') ? date('Y-m-d', strtotime('monday last week')) : date('Y-m-d', strtotime('monday this week'));
$weeklyDailyData = getWeeklyDailyExpenses($userId, $mondayDate);

// Monthly Breakdown (Weeks 1 to 5)
$activeMonth = (int)date('m', strtotime($startDate));
$activeYear  = (int)date('Y', strtotime($startDate));
$monthlyWeeklyData = getMonthlyWeeklyBreakdown($userId, $activeMonth, $activeYear);

// Recent Transactions
$recentTransactions = getRecentTransactions($userId, 6);

// Budget alerts check for current month
$currentMonthBudgets = getBudgetStatus($userId, (int)date('n'), (int)date('Y'));
$budgetAlerts = array_filter($currentMonthBudgets, fn($b) => $b['status'] !== 'normal');

// Prepare chart datasets for JavaScript
$catLabels = array_column($categoryData, 'category_name');
$catValues = array_map(fn($v) => (float)$v['total_amount'], $categoryData);

$weekLabels = array_column($weeklyDailyData, 'day');
$weekValues = array_column($weeklyDailyData, 'total');

$monthLabels = array_column($monthlyWeeklyData, 'week');
$monthValues = array_column($monthlyWeeklyData, 'total');

$payLabels = array_column($paymentData, 'payment_method');
$payValues = array_map(fn($v) => (float)$v['total_amount'], $paymentData);

require_once __DIR__ . '/includes/header.php';
?>

<!-- Active Budget Warning Banner if any category exceeds 80% -->
<?php if (!empty($budgetAlerts)): ?>
    <?php foreach ($budgetAlerts as $alert): ?>
        <div class="alert alert-<?= $alert['status_class'] ?>">
            <svg class="alert-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
            <div>
                <strong><?= htmlspecialchars($alert['category_name']) ?> Budget Alert:</strong>
                <?= htmlspecialchars($alert['message']) ?>
                <a href="budget.php" style="margin-left: 8px; text-decoration: underline; font-weight: 600;">Manage Budgets &rarr;</a>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Period Selector Bar -->
<div class="period-selector-card">
    <div class="period-header">
        <div style="display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--text-muted);">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Timeline:</span>
            <strong style="font-size: 13px; color: var(--text-main);"><?= htmlspecialchars($periodLabel) ?></strong>
        </div>

        <div class="period-pills">
            <a href="dashboard.php?period=this_week" class="period-btn <?= $period === 'this_week' ? 'active' : '' ?>">This Week</a>
            <a href="dashboard.php?period=last_week" class="period-btn <?= $period === 'last_week' ? 'active' : '' ?>">Last Week</a>
            <a href="dashboard.php?period=this_month" class="period-btn <?= $period === 'this_month' ? 'active' : '' ?>">This Month</a>
            <a href="dashboard.php?period=last_month" class="period-btn <?= $period === 'last_month' ? 'active' : '' ?>">Last Month</a>
            <a href="dashboard.php?period=this_year" class="period-btn <?= $period === 'this_year' ? 'active' : '' ?>">This Year</a>
            <button type="button" class="period-btn <?= $period === 'custom' ? 'active' : '' ?>" onclick="toggleCustomDateRange(true)">Custom</button>
        </div>
    </div>

    <!-- Custom Date Range Form -->
    <form action="dashboard.php" method="GET" class="custom-date-box" id="customDateRangeBox" style="display: <?= $period === 'custom' ? 'flex' : 'none' ?>;">
        <input type="hidden" name="period" value="custom">
        <label for="custom_start_date" style="font-size: 12px; font-weight: 600; color: var(--text-muted);">From:</label>
        <input type="date" id="custom_start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="form-control" style="width: auto;">

        <label for="custom_end_date" style="font-size: 12px; font-weight: 600; color: var(--text-muted);">To:</label>
        <input type="date" id="custom_end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="form-control" style="width: auto;">

        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="toggleCustomDateRange(false)">Cancel</button>
    </form>
</div>

<!-- Four Stat Summary Cards -->
<div class="stats-grid">
    <div class="stat-card income">
        <div class="stat-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <line x1="12" y1="19" x2="12" y2="5"></line>
                <polyline points="5 12 12 5 19 12"></polyline>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Earnings</div>
            <div class="stat-value"><?= formatCurrency($totalIncome) ?></div>
            <div class="stat-help">Credits in period</div>
        </div>
    </div>

    <div class="stat-card expense">
        <div class="stat-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <polyline points="19 12 12 19 5 12"></polyline>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Total Expenditures</div>
            <div class="stat-value"><?= formatCurrency($totalExpenses) ?></div>
            <div class="stat-help">Spending in period</div>
        </div>
    </div>

    <div class="stat-card balance">
        <div class="stat-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Remaining Balance</div>
            <div class="stat-value" style="color: <?= $balance >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                <?= formatCurrency($balance) ?>
            </div>
            <div class="stat-help">Net cash reserve</div>
        </div>
    </div>

    <div class="stat-card count">
        <div class="stat-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Transactions</div>
            <div class="stat-value"><?= number_format($txnCount) ?></div>
            <div class="stat-help">Logged entries</div>
        </div>
    </div>
</div>

<!-- Charts & Analysis Grid -->
<div class="charts-grid">
    <!-- Category-wise Expense Analysis -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Category Spending Share</h2>
                <p class="card-subtitle">Distribution across active expense groups</p>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($categoryData)): ?>
                <div class="chart-container">
                    <canvas id="categoryExpenseChart"></canvas>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-state-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <h3>No Expenses Recorded</h3>
                    <p>No expenditures were recorded for the selected timeline.</p>
                    <a href="add_expense.php" class="btn btn-sm btn-primary">+ Add New Expense</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Period Specific Chart -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">
                    <?= ($period === 'this_week' || $period === 'last_week') ? 'Daily Weekly Velocity' : 'Weekly Monthly Spending Velocity' ?>
                </h2>
                <p class="card-subtitle">
                    <?= ($period === 'this_week' || $period === 'last_week') ? 'Day-by-day distribution (Monday through Sunday)' : 'Week 1 through Week 5 cash outflow' ?>
                </p>
            </div>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <?php if ($period === 'this_week' || $period === 'last_week'): ?>
                    <canvas id="weeklyExpenseChart"></canvas>
                <?php else: ?>
                    <canvas id="monthlyExpenseChart"></canvas>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Category Expense Detailed Table -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Category Breakdown</h2>
            <span class="badge badge-income"><?= count($categoryData) ?> Categories</span>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Count</th>
                            <th>Amount</th>
                            <th>Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categoryData)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding: 25px; color: var(--text-light);">
                                    No category data available for the chosen date range.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($categoryData as $cat): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($cat['category_name']) ?></strong></td>
                                    <td><?= (int)$cat['txn_count'] ?></td>
                                    <td class="amount-expense"><?= formatCurrency($cat['total_amount']) ?></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="flex: 1; background: #f1f5f9; height: 6px; border-radius: 999px;">
                                                <div style="width: <?= min(100, $cat['percentage']) ?>%; background: var(--danger); height: 100%; border-radius: 999px;"></div>
                                            </div>
                                            <span style="font-size: 11px; font-weight: 700; min-width: 36px;"><?= $cat['percentage'] ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment Method Breakdown -->
    <div class="card">
        <div class="card-header">
            <div>
                <h2 class="card-title">Payment Channel Analysis</h2>
                <p class="card-subtitle">Volume across Cash, UPI (GPay/PhonePe), Card &amp; Bank</p>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($paymentData)): ?>
                <div class="chart-container" style="min-height: 240px;">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-state-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                    <p>No payment channel data available.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Transactions Table Section -->
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Recent Transactions</h2>
            <p class="card-subtitle">Real-time ledger of incoming and outgoing flows</p>
        </div>
        <a href="transactions.php" class="btn btn-sm btn-secondary">Full Ledger &rarr;</a>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Memo / Note</th>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentTransactions)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 30px; color: var(--text-light);">
                                No recent transactions recorded yet. <a href="add_expense.php">Record your first expense</a>.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentTransactions as $txn): ?>
                            <tr>
                                <td><?= formatDate($txn['transaction_date']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $txn['type'] ?>">
                                        <?= strtoupper($txn['type']) ?>
                                    </span>
                                </td>
                                <td><strong><?= htmlspecialchars($txn['category_name']) ?></strong></td>
                                <td><?= htmlspecialchars($txn['description'] ?: '-') ?></td>
                                <td><span class="badge badge-method"><?= htmlspecialchars($txn['payment_method']) ?></span></td>
                                <td class="amount-<?= $txn['type'] ?>">
                                    <?= ($txn['type'] === 'income' ? '+' : '-') . formatCurrency($txn['amount']) ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="edit_transaction.php?id=<?= $txn['transaction_id'] ?>" class="btn btn-sm btn-secondary" style="padding: 3px 8px;">Edit</a>
                                    <a href="delete_transaction.php?id=<?= $txn['transaction_id'] ?>" 
                                       onclick="return confirmDelete(event, 'Delete transaction #<?= $txn['transaction_id'] ?>?');" 
                                       class="btn btn-sm btn-danger" style="padding: 3px 8px;">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Supply datasets to JS charts initializers -->
<script>
    window.categoryChartData = {
        labels: <?= json_encode($catLabels) ?>,
        values: <?= json_encode($catValues) ?>
    };

    window.weeklyChartData = {
        labels: <?= json_encode($weekLabels) ?>,
        values: <?= json_encode($weekValues) ?>
    };

    window.monthlyChartData = {
        labels: <?= json_encode($monthLabels) ?>,
        values: <?= json_encode($monthValues) ?>
    };

    window.paymentChartData = {
        labels: <?= json_encode($payLabels) ?>,
        values: <?= json_encode($payValues) ?>
    };
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
