<?php
/**
 * Monthly Budget Management System
 * ExpenseMgr — Personal Financial Intelligence System
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$userId = getCurrentUserId();
$pageTitle = "Budget Intelligence";

// Selected Month and Year (Default to current)
$selectedMonth = (int)($_GET['month'] ?? date('n'));
$selectedYear  = (int)($_GET['year'] ?? date('Y'));

if ($selectedMonth < 1 || $selectedMonth > 12) $selectedMonth = (int)date('n');
if ($selectedYear < 2020 || $selectedYear > 2035) $selectedYear = (int)date('Y');

$budgetStatusList = getBudgetStatus($userId, $selectedMonth, $selectedYear);
$expenseCategories = getCategories('expense');
$csrfToken = generateCSRFToken();

// Calculate aggregate budget stats
$totalBudgeted = array_sum(array_column($budgetStatusList, 'budget_amount'));
$totalSpentOnBudgets = array_sum(array_column($budgetStatusList, 'spent_amount'));
$overallRemaining = $totalBudgeted - $totalSpentOnBudgets;
$overallPercent = $totalBudgeted > 0 ? round(($totalSpentOnBudgets / $totalBudgeted) * 100, 1) : 0;

require_once __DIR__ . '/includes/header.php';
?>

<!-- Month/Year Navigation Filter -->
<div class="period-selector-card">
    <form action="budget.php" method="GET" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--text-muted);">
                <circle cx="12" cy="12" r="10"></circle>
                <circle cx="12" cy="12" r="6"></circle>
                <circle cx="12" cy="12" r="2"></circle>
            </svg>
            <label for="budget_month" style="font-weight: 600; font-size: 13px; color: var(--text-muted);">Budget Cycle:</label>
            <select name="month" id="budget_month" class="form-control" style="width: auto; padding: 6px 12px;" onchange="this.form.submit()">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= $m === $selectedMonth ? 'selected' : '' ?>>
                        <?= date('F', mktime(0, 0, 0, $m, 10)) ?>
                    </option>
                <?php endfor; ?>
            </select>

            <select name="year" id="budget_year" class="form-control" style="width: auto; padding: 6px 12px;" onchange="this.form.submit()">
                <?php for ($y = date('Y') - 2; $y <= date('Y') + 2; $y++): ?>
                    <option value="<?= $y ?>" <?= $y === $selectedYear ? 'selected' : '' ?>>
                        <?= $y ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <div>
            <span style="font-size: 12px; color: var(--text-muted);">
                Target Period: <strong><?= date('F Y', mktime(0, 0, 0, $selectedMonth, 10, $selectedYear)) ?></strong>
            </span>
        </div>
    </form>
</div>

<!-- Overall Budget Overview Cards -->
<div class="stats-grid-3">
    <div class="stat-card balance">
        <div class="stat-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Allocated Budget</div>
            <div class="stat-value"><?= formatCurrency($totalBudgeted) ?></div>
            <div class="stat-help"><?= count($budgetStatusList) ?> active limits</div>
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
            <div class="stat-label">Actual Spent</div>
            <div class="stat-value"><?= formatCurrency($totalSpentOnBudgets) ?></div>
            <div class="stat-help"><?= $overallPercent ?>% consumed</div>
        </div>
    </div>

    <div class="stat-card income">
        <div class="stat-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Safe Spending Buffer</div>
            <div class="stat-value" style="color: <?= $overallRemaining >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                <?= formatCurrency($overallRemaining) ?>
            </div>
            <div class="stat-help"><?= $overallRemaining >= 0 ? 'Within safety margins' : 'Over budget threshold' ?></div>
        </div>
    </div>
</div>

<div class="charts-grid">
    <!-- Left Column: Add / Update Budget Target Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Set Monthly Category Ceiling</h2>
        </div>
        <div class="card-body">
            <form action="actions/budget_action.php" method="POST" id="budgetForm" novalidate>
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="set">
                <input type="hidden" name="month" value="<?= $selectedMonth ?>">
                <input type="hidden" name="year" value="<?= $selectedYear ?>">

                <div class="form-group">
                    <label for="budget_category" class="form-label">Expense Category <span class="required">*</span></label>
                    <select id="budget_category" name="category_id" class="form-control" required>
                        <option value="">-- Choose Category --</option>
                        <?php foreach ($expenseCategories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>">
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="budget_amount" class="form-label">Monthly Limit (₹) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="1" id="budget_amount" name="amount" class="form-control" placeholder="e.g. 5000.00" required>
                    <p class="form-hint">Maximum spending target for <?= date('F Y', mktime(0, 0, 0, $selectedMonth, 10, $selectedYear)) ?>.</p>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 11px; margin-top: 10px;">
                    Save Budget Limit
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: Current Category Budgets Status & Warnings -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Category Spending Health</h2>
            <span class="badge badge-income"><?= count($budgetStatusList) ?> Active</span>
        </div>
        <div class="card-body">
            <?php if (empty($budgetStatusList)): ?>
                <div class="empty-state">
                    <svg class="empty-state-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="12" r="10"></circle>
                        <circle cx="12" cy="12" r="6"></circle>
                        <circle cx="12" cy="12" r="2"></circle>
                    </svg>
                    <h3>No Budgets Set For This Cycle</h3>
                    <p>Assign category budgets to monitor spending velocities and trigger automated alerts.</p>
                </div>
            <?php else: ?>
                <?php foreach ($budgetStatusList as $item): ?>
                    <div class="budget-card-item">
                        <div class="budget-item-header">
                            <div>
                                <span class="budget-item-title"><?= htmlspecialchars($item['category_name']) ?></span>
                                <?php if ($item['status'] === 'exceeded'): ?>
                                    <span class="badge badge-danger" style="margin-left: 8px;">Exceeded</span>
                                <?php elseif ($item['status'] === 'warning'): ?>
                                    <span class="badge badge-warning" style="margin-left: 8px;">Threshold (80%+)</span>
                                <?php else: ?>
                                    <span class="badge badge-success" style="margin-left: 8px;">On Track</span>
                                <?php endif; ?>
                            </div>

                            <form action="actions/budget_action.php" method="POST" onsubmit="return confirmDelete(event, 'Remove budget for <?= htmlspecialchars($item['category_name']) ?>?');" style="margin: 0;">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="budget_id" value="<?= $item['budget_id'] ?>">
                                <button type="submit" class="btn btn-sm btn-secondary" style="padding: 2px 7px; font-size: 11px;" title="Remove this budget">✕</button>
                            </form>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill progress-<?= $item['status_class'] ?>" 
                                 style="width: <?= min(100, $item['percentage']) ?>%;"></div>
                        </div>

                        <div class="budget-item-metrics">
                            <span>Spent: <strong><?= formatCurrency($item['spent_amount']) ?></strong> of <?= formatCurrency($item['budget_amount']) ?></span>
                            <span><strong><?= $item['percentage'] ?>%</strong></span>
                        </div>

                        <div style="font-size: 11px; margin-top: 5px; color: <?= $item['status'] === 'exceeded' ? 'var(--danger)' : ($item['status'] === 'warning' ? 'var(--warning)' : 'var(--success)') ?>; font-weight: 600;">
                            <?= htmlspecialchars($item['message']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
