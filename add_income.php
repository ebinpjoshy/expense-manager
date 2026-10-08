<?php
/**
 * Add Income Transaction View
 * ExpenseMgr — Personal Financial Intelligence System
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$pageTitle = "Record Income";
require_once __DIR__ . '/includes/header.php';

$incomeCategories = getCategories('income');
$csrfToken = generateCSRFToken();
?>

<div class="card" style="max-width: 660px; margin: 0 auto;">
    <div class="card-header">
        <div>
            <h2 class="card-title">Record Incoming Funds</h2>
            <p class="card-subtitle">Add earnings, recurring salary, client invoices, or dividends</p>
        </div>
        <span class="badge badge-income">+ Credit</span>
    </div>

    <div class="card-body">
        <form action="actions/income_action.php" method="POST" id="transactionForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="amount" class="form-label">Income Amount (₹) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="form-control" placeholder="e.g. 25000.00" required>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Income Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($incomeCategories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>">
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="payment_method" class="form-label">Deposit Channel <span class="required">*</span></label>
                    <select id="payment_method" name="payment_method" class="form-control" required>
                        <option value="Bank" selected>Bank Account (Direct / IMPS)</option>
                        <option value="Cash">Cash</option>
                        <option value="GPay">GPay (UPI)</option>
                        <option value="PhonePe">PhonePe (UPI)</option>
                        <option value="Card">Debit / Credit Card</option>
                        <option value="Other">Other Mode</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="transaction_date" class="form-label">Credit Date <span class="required">*</span></label>
                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Reference Note / Description</label>
                <input type="text" id="description" name="description" class="form-control" placeholder="e.g. Monthly Retainer, Milestone Bonus">
                <p class="form-hint">Brief note for ledger reference.</p>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-success" style="padding: 10px 22px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Save Income
                </button>
                <a href="dashboard.php" class="btn btn-secondary" style="padding: 10px 18px;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
