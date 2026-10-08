<?php
/**
 * User Registration View
 * ExpenseMgr — Personal Financial Intelligence System
 */
require_once __DIR__ . '/includes/auth.php';
requireGuest();

$pageTitle = "Create Account";
require_once __DIR__ . '/includes/header.php';
$csrfToken = generateCSRFToken();
?>

<div class="auth-card" style="max-width: 480px;">
    <div class="auth-header">
        <div class="brand-badge">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
        </div>
        <h2>Create Account</h2>
        <p>Set up your private personal financial tracker</p>
    </div>

    <div class="auth-body">
        <?php
        $flashSuccess = getFlash('success');
        $flashError   = getFlash('error');
        ?>
        <?php if ($flashSuccess): ?>
            <div class="alert alert-success">
                <svg class="alert-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span><?= htmlspecialchars($flashSuccess) ?></span>
            </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="alert alert-danger">
                <svg class="alert-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span><?= htmlspecialchars($flashError) ?></span>
            </div>
        <?php endif; ?>

        <form action="actions/register_action.php" method="POST" id="registerForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-group">
                <label for="reg_name" class="form-label">Full Name <span class="required">*</span></label>
                <input type="text" id="reg_name" name="name" class="form-control" placeholder="e.g. Alex Morgan" required>
            </div>

            <div class="form-group">
                <label for="reg_email" class="form-label">Email Address <span class="required">*</span></label>
                <input type="email" id="reg_email" name="email" class="form-control" placeholder="alex@example.com" required>
            </div>

            <div class="form-group">
                <label for="reg_phone" class="form-label">Mobile Number (10 digits) <span class="required">*</span></label>
                <input type="tel" id="reg_phone" name="phone" maxlength="10" class="form-control" placeholder="9876543210" required>
            </div>

            <div class="form-group">
                <label for="reg_password" class="form-label">Password <span class="required">*</span></label>
                <input type="password" id="reg_password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                <p class="form-hint">Requires uppercase, lowercase, number, and special character.</p>
            </div>

            <div class="form-group">
                <label for="reg_confirm_password" class="form-label">Confirm Password <span class="required">*</span></label>
                <input type="password" id="reg_confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 11px; margin-top: 10px;">
                Complete Registration
            </button>
        </form>
    </div>

    <div class="auth-footer">
        Already have an account? <a href="login.php"><strong>Sign in here</strong></a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
