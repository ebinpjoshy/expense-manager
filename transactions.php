<?php
/**
 * Transactions History and Filtering
 * ExpenseMgr — Personal Financial Intelligence System
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$userId = getCurrentUserId();
$pageTitle = "Transactions Ledger";

// -------------------------------------------------------------
// Filters
// -------------------------------------------------------------
$typeFilter      = trim($_GET['type'] ?? '');
$categoryFilter  = (int)($_GET['category'] ?? 0);
$methodFilter    = trim($_GET['method'] ?? '');
$startDate       = trim($_GET['start_date'] ?? '');
$endDate         = trim($_GET['end_date'] ?? '');
$searchKeyword   = trim($_GET['search'] ?? '');

// Build Prepared Query
$pdo = getDBConnection();
$sql = "SELECT t.*, c.category_name 
        FROM transactions t
        JOIN categories c ON t.category_id = c.category_id
        WHERE t.user_id = ?";
$params = [$userId];

if (!empty($typeFilter) && in_array($typeFilter, ['income', 'expense'])) {
    $sql .= " AND t.type = ?";
    $params[] = $typeFilter;
}

if ($categoryFilter > 0) {
    $sql .= " AND t.category_id = ?";
    $params[] = $categoryFilter;
}

if (!empty($methodFilter)) {
    $sql .= " AND t.payment_method = ?";
    $params[] = $methodFilter;
}

if (!empty($startDate)) {
    $sql .= " AND t.transaction_date >= ?";
    $params[] = $startDate;
}

if (!empty($endDate)) {
    $sql .= " AND t.transaction_date <= ?";
    $params[] = $endDate;
}

if (!empty($searchKeyword)) {
    $sql .= " AND (t.description LIKE ? OR c.category_name LIKE ?)";
    $params[] = "%{$searchKeyword}%";
    $params[] = "%{$searchKeyword}%";
}

$sql .= " ORDER BY t.transaction_date DESC, t.transaction_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Calculate totals for currently filtered records
$filteredIncome = 0;
$filteredExpense = 0;
foreach ($transactions as $t) {
    if ($t['type'] === 'income') {
        $filteredIncome += (float)$t['amount'];
    } else {
        $filteredExpense += (float)$t['amount'];
    }
}
$filteredNet = $filteredIncome - $filteredExpense;

// Get all categories for filter dropdown
$allCategories = getCategories();

require_once __DIR__ . '/includes/header.php';
?>

<!-- Filter and Search Box -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Filter &amp; Search Ledger</h2>
        <a href="transactions.php" class="btn btn-sm btn-secondary">Reset Filters</a>
    </div>
    <div class="card-body">
        <form action="transactions.php" method="GET" class="form-grid">
            <div class="form-group">
                <label for="filter_search" class="form-label">Search Query</label>
                <input type="text" id="filter_search" name="search" value="<?= htmlspecialchars($searchKeyword) ?>" class="form-control" placeholder="Search memo, note, or category...">
            </div>

            <div class="form-group">
                <label for="filter_type" class="form-label">Flow Type</label>
                <select id="filter_type" name="type" class="form-control">
                    <option value="">All Flows</option>
                    <option value="income" <?= $typeFilter === 'income' ? 'selected' : '' ?>>Income Credits</option>
                    <option value="expense" <?= $typeFilter === 'expense' ? 'selected' : '' ?>>Expense Debits</option>
                </select>
            </div>

            <div class="form-group">
                <label for="filter_category" class="form-label">Category</label>
                <select id="filter_category" name="category" class="form-control">
                    <option value="0">All Categories</option>
                    <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>" <?= $categoryFilter == $cat['category_id'] ? 'selected' : '' ?>>
                            [<?= ucfirst($cat['type']) ?>] <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="filter_method" class="form-label">Payment Channel</label>
                <select id="filter_method" name="method" class="form-control">
                    <option value="">All Channels</option>
                    <option value="Cash" <?= $methodFilter === 'Cash' ? 'selected' : '' ?>>Cash</option>
                    <option value="GPay" <?= $methodFilter === 'GPay' ? 'selected' : '' ?>>GPay (UPI)</option>
                    <option value="PhonePe" <?= $methodFilter === 'PhonePe' ? 'selected' : '' ?>>PhonePe (UPI)</option>
                    <option value="Bank" <?= $methodFilter === 'Bank' ? 'selected' : '' ?>>Bank Transfer</option>
                    <option value="Card" <?= $methodFilter === 'Card' ? 'selected' : '' ?>>Card</option>
                    <option value="Other" <?= $methodFilter === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="filter_start" class="form-label">From Date</label>
                <input type="date" id="filter_start" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="form-control">
            </div>

            <div class="form-group">
                <label for="filter_end" class="form-label">To Date</label>
                <input type="date" id="filter_end" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="form-control">
            </div>

            <div style="grid-column: 1 / -1; display: flex; gap: 8px; margin-top: 4px;">
                <button type="submit" class="btn btn-primary" style="padding: 9px 20px;">Filter Ledger</button>
                <a href="transactions.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Filter Summary Strip -->
<div class="stats-grid-3">
    <div class="stat-card income" style="padding: 14px 18px;">
        <div class="stat-info">
            <div class="stat-label">Filtered Credits</div>
            <div class="stat-value" style="font-size: 19px;"><?= formatCurrency($filteredIncome) ?></div>
        </div>
    </div>
    <div class="stat-card expense" style="padding: 14px 18px;">
        <div class="stat-info">
            <div class="stat-label">Filtered Debits</div>
            <div class="stat-value" style="font-size: 19px;"><?= formatCurrency($filteredExpense) ?></div>
        </div>
    </div>
    <div class="stat-card balance" style="padding: 14px 18px;">
        <div class="stat-info">
            <div class="stat-label">Filtered Net</div>
            <div class="stat-value" style="font-size: 19px; color: <?= $filteredNet >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                <?= formatCurrency($filteredNet) ?>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Records Table -->
<div class="card">
    <div class="card-header">
        <div>
            <h2 class="card-title">Transaction Ledger</h2>
            <p class="card-subtitle">Showing <?= count($transactions) ?> matching transactions</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <a href="add_income.php" class="btn btn-sm btn-outline-success">+ Income</a>
            <a href="add_expense.php" class="btn btn-sm btn-outline-danger">+ Expense</a>
        </div>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Payment Mode</th>
                        <th>Amount</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-light);">
                                No records matched your filter criteria. Try resetting the filters.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $txn): ?>
                            <tr>
                                <td><?= formatDate($txn['transaction_date']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $txn['type'] ?>">
                                        <?= strtoupper($txn['type']) ?>
                                    </span>
                                </td>
                                <td><strong><?= htmlspecialchars($txn['category_name']) ?></strong></td>
                                <td><?= htmlspecialchars($txn['description'] ?: '-') ?></td>
                                <td>
                                    <span class="badge badge-method"><?= htmlspecialchars($txn['payment_method']) ?></span>
                                </td>
                                <td class="amount-<?= $txn['type'] ?>">
                                    <?= ($txn['type'] === 'income' ? '+' : '-') . formatCurrency($txn['amount']) ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="edit_transaction.php?id=<?= $txn['transaction_id'] ?>" class="btn btn-sm btn-secondary" style="padding: 3px 8px;">
                                        Edit
                                    </a>
                                    <a href="delete_transaction.php?id=<?= $txn['transaction_id'] ?>" 
                                       onclick="return confirmDelete(event, 'Delete transaction #<?= $txn['transaction_id'] ?>?');" 
                                       class="btn btn-sm btn-danger" style="padding: 3px 8px;">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
