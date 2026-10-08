<?php
/**
 * Financial Reports and Long-Term Trends
 * ExpenseMgr — Personal Financial Intelligence System
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$userId = getCurrentUserId();
$pageTitle = "Financial Analytics & Reports";

// Period selector
$period      = $_GET['period'] ?? 'this_month';
$customStart = $_GET['start_date'] ?? null;
$customEnd   = $_GET['end_date'] ?? null;

$range = resolveDateRange($period, $customStart, $customEnd);
$startDate   = $range['start_date'];
$endDate     = $range['end_date'];
$periodLabel = $range['label'];

// Fetch Analytics Data
$totalIncome   = getTotalIncome($userId, $startDate, $endDate);
$totalExpenses = getTotalExpenses($userId, $startDate, $endDate);
$balance       = $totalIncome - $totalExpenses;
$savingsRate   = $totalIncome > 0 ? round(($balance / $totalIncome) * 100, 1) : 0;

$categoryData  = getCategoryExpenses($userId, $startDate, $endDate);
$paymentData   = getPaymentMethodExpenses($userId, $startDate, $endDate);

// Monthly trend comparison for current year (Jan - Dec)
$currentYear = (int)date('Y', strtotime($startDate));
$monthlyTrend = getMonthlyTrend($userId, $currentYear);

// Identify Top Spending Category
$topCategory = !empty($categoryData) ? $categoryData[0] : null;

// Prepare data for Chart.js
$catLabels = array_column($categoryData, 'category_name');
$catValues = array_map(fn($v) => (float)$v['total_amount'], $categoryData);

$payLabels = array_column($paymentData, 'payment_method');
$payValues = array_map(fn($v) => (float)$v['total_amount'], $paymentData);

$trendLabels   = array_column($monthlyTrend, 'month_name');
$trendIncomes  = array_column($monthlyTrend, 'income');
$trendExpenses = array_column($monthlyTrend, 'expense');

require_once __DIR__ . '/includes/header.php';
?>

<!-- Period Selector -->
<div class="period-selector-card">
    <div class="period-header">
        <div style="display: flex; align-items: center; gap: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--text-muted);">
                <line x1="18" y1="20" x2="18" y2="10"></line>
                <line x1="12" y1="20" x2="12" y2="4"></line>
                <line x1="6" y1="20" x2="6" y2="14"></line>
            </svg>
            <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Analysis Scope:</span>
            <strong style="font-size: 13px; color: var(--text-main);"><?= htmlspecialchars($periodLabel) ?></strong>
        </div>

        <div style="display: flex; gap: 8px; align-items: center;">
            <div class="period-pills">
                <a href="reports.php?period=this_week" class="period-btn <?= $period === 'this_week' ? 'active' : '' ?>">Weekly</a>
                <a href="reports.php?period=this_month" class="period-btn <?= $period === 'this_month' ? 'active' : '' ?>">Monthly</a>
                <a href="reports.php?period=this_year" class="period-btn <?= $period === 'this_year' ? 'active' : '' ?>">Yearly</a>
                <button type="button" class="period-btn <?= $period === 'custom' ? 'active' : '' ?>" onclick="toggleCustomDateRange(true)">Custom</button>
            </div>
            <button onclick="window.print()" class="btn btn-sm btn-secondary" title="Print statement">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Print Report
            </button>
        </div>
    </div>

    <!-- Custom Date Range Form -->
    <form action="reports.php" method="GET" class="custom-date-box" id="customDateRangeBox" style="display: <?= $period === 'custom' ? 'flex' : 'none' ?>;">
        <input type="hidden" name="period" value="custom">
        <label for="custom_start_date" style="font-size: 12px; font-weight: 600; color: var(--text-muted);">From:</label>
        <input type="date" id="custom_start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="form-control" style="width: auto;">

        <label for="custom_end_date" style="font-size: 12px; font-weight: 600; color: var(--text-muted);">To:</label>
        <input type="date" id="custom_end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="form-control" style="width: auto;">

        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
        <button type="button" class="btn btn-sm btn-secondary" onclick="toggleCustomDateRange(false)">Cancel</button>
    </form>
</div>

<!-- Highlight & Metric Summary Cards -->
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
            <div class="stat-help">Inflow in active period</div>
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
            <div class="stat-label">Total Expenses</div>
            <div class="stat-value"><?= formatCurrency($totalExpenses) ?></div>
            <div class="stat-help">Outflow in active period</div>
        </div>
    </div>

    <div class="stat-card balance">
        <div class="stat-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Net Savings</div>
            <div class="stat-value" style="color: <?= $balance >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                <?= formatCurrency($balance) ?>
            </div>
            <div class="stat-help">Savings Rate: <?= $savingsRate ?>%</div>
        </div>
    </div>

    <div class="stat-card count">
        <div class="stat-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Primary Outflow Category</div>
            <div class="stat-value" style="font-size: 18px;">
                <?= $topCategory ? htmlspecialchars($topCategory['category_name']) : 'None' ?>
            </div>
            <div class="stat-help">
                <?= $topCategory ? formatCurrency($topCategory['total_amount']) . " (" . $topCategory['percentage'] . "%)" : 'No expenditures' ?>
            </div>
        </div>
    </div>
</div>

<!-- 12-Month Annual Trend Comparison -->
<div class="card chart-card-full">
    <div class="card-header">
        <div>
            <h2 class="card-title">Annual Cash Flow Distribution (<?= $currentYear ?>)</h2>
            <p class="card-subtitle">Month-by-month comparative analysis of income versus expense</p>
        </div>
    </div>
    <div class="card-body">
        <div class="chart-container" style="min-height: 320px;">
            <canvas id="monthlyComparisonChart"></canvas>
        </div>
    </div>
</div>

<!-- Detailed Analysis Charts & Tables -->
<div class="charts-grid">
    <!-- Category Doughnut -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Category Outflow Volume</h2>
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
                    <p>No category expenses to display.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Mode Doughnut -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Payment Channel Share</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($paymentData)): ?>
                <div class="chart-container">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg class="empty-state-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                    <p>No payment channel records found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Comprehensive Breakdown Tables -->
<div class="charts-grid">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Category Outflow Breakdown</h2>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Transactions</th>
                            <th>Amount</th>
                            <th>Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categoryData)): ?>
                            <tr><td colspan="4" style="text-align:center; padding: 20px; color:var(--text-light);">No records</td></tr>
                        <?php else: ?>
                            <?php foreach ($categoryData as $c): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($c['category_name']) ?></strong></td>
                                    <td><?= $c['txn_count'] ?></td>
                                    <td class="amount-expense"><?= formatCurrency($c['total_amount']) ?></td>
                                    <td><?= $c['percentage'] ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Payment Channel Summary</h2>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Channel</th>
                            <th>Transactions</th>
                            <th>Amount</th>
                            <th>Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($paymentData)): ?>
                            <tr><td colspan="4" style="text-align:center; padding: 20px; color:var(--text-light);">No records</td></tr>
                        <?php else: ?>
                            <?php foreach ($paymentData as $p): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($p['payment_method']) ?></strong></td>
                                    <td><?= $p['txn_count'] ?></td>
                                    <td class="amount-expense"><?= formatCurrency($p['total_amount']) ?></td>
                                    <td><?= $p['percentage'] ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Supply chart data to JS initializers -->
<script>
    window.categoryChartData = {
        labels: <?= json_encode($catLabels) ?>,
        values: <?= json_encode($catValues) ?>
    };

    window.paymentChartData = {
        labels: <?= json_encode($payLabels) ?>,
        values: <?= json_encode($payValues) ?>
    };

    window.monthlyTrendData = {
        labels: <?= json_encode($trendLabels) ?>,
        incomes: <?= json_encode($trendIncomes) ?>,
        expenses: <?= json_encode($trendExpenses) ?>
    };
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
