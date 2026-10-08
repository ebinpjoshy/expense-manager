<?php
/**
 * Add Expense Transaction View
 * ExpenseMgr — Personal Financial Intelligence System
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$pageTitle = "Record Expense";
require_once __DIR__ . '/includes/header.php';

$expenseCategories = getCategories('expense');
$csrfToken = generateCSRFToken();
?>

<div class="card" style="max-width: 660px; margin: 0 auto;">
    <div class="card-header">
        <div>
            <h2 class="card-title">Record Expenditure</h2>
            <p class="card-subtitle">Track daily personal outflow, utilities, bills, and lifestyle spending</p>
        </div>
        <span class="badge badge-expense">- Debit</span>
    </div>

    <div class="card-body">
        <form action="actions/expense_action.php" method="POST" id="transactionForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="amount" class="form-label">Expense Amount (₹) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="form-control" placeholder="e.g. 650.00" required>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Expense Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($expenseCategories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>">
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="payment_method" class="form-label">Payment Channel <span class="required">*</span></label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="Cash">Cash</option>
                        <option value="GPay" selected>GPay (Google Pay UPI)</option>
                        <option value="PhonePe">PhonePe (UPI)</option>
                        <option value="Bank">Bank Transfer / NetBanking</option>
                        <option value="Card">Debit / Credit Card</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="transaction_date" class="form-label">Expense Date <span class="required">*</span></label>
                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description / Memo</label>
                <input type="text" id="description" name="description" class="form-control" placeholder="e.g. Grocery restock, Fuel refuel, Dining out">
                <p class="form-hint">Provides search context in statement records.</p>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-danger" style="padding: 10px 22px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Add Expense
                </button>
                <a href="dashboard.php" class="btn btn-secondary" style="padding: 10px 18px;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
