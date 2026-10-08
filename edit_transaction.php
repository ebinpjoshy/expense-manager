<?php
/**
 * Edit Transaction View
 * ExpenseMgr — Personal Financial Intelligence System
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$userId = getCurrentUserId();
$txnId  = (int)($_GET['id'] ?? 0);

if ($txnId <= 0) {
    setFlash('error', "Invalid transaction specified.");
    header("Location: transactions.php");
    exit();
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE transaction_id = ? AND user_id = ?");
$stmt->execute([$txnId, $userId]);
$txn = $stmt->fetch();

if (!$txn) {
    setFlash('error', "Transaction not found or you are not authorized to edit it.");
    header("Location: transactions.php");
    exit();
}

$categories = getCategories($txn['type']);
$csrfToken = generateCSRFToken();
$pageTitle = "Edit Transaction #" . $txnId;
require_once __DIR__ . '/includes/header.php';
?>

<div class="card" style="max-width: 660px; margin: 0 auto;">
    <div class="card-header">
        <div>
            <h2 class="card-title">Modify <?= ucfirst($txn['type']) ?> Record</h2>
            <p class="card-subtitle">Updating record #<?= $txnId ?> recorded on <?= formatDate($txn['transaction_date']) ?></p>
        </div>
        <span class="badge badge-<?= $txn['type'] ?>"><?= strtoupper($txn['type']) ?></span>
    </div>

    <div class="card-body">
        <form action="actions/update_transaction.php" method="POST" id="transactionForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="transaction_id" value="<?= $txn['transaction_id'] ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label for="amount" class="form-label">Amount (₹) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0.01" id="amount" name="amount" class="form-control" 
                           value="<?= htmlspecialchars($txn['amount']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= $txn['category_id'] == $cat['category_id'] ? 'selected' : '' ?>>
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
                        <?php
                        $methods = ['Cash', 'GPay', 'PhonePe', 'Bank', 'Card', 'Other'];
                        foreach ($methods as $m):
                        ?>
                            <option value="<?= $m ?>" <?= $txn['payment_method'] === $m ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="transaction_date" class="form-label">Date <span class="required">*</span></label>
                    <input type="date" id="transaction_date" name="transaction_date" class="form-control" 
                           value="<?= htmlspecialchars($txn['transaction_date']) ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Memo / Description</label>
                <input type="text" id="description" name="description" class="form-control" 
                       value="<?= htmlspecialchars($txn['description'] ?? '') ?>">
            </div>

            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px;">
                    Update Record
                </button>
                <a href="transactions.php" class="btn btn-secondary" style="padding: 10px 18px;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
