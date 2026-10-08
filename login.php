<?php
/**
 * User Login View
 * ExpenseMgr — Personal Financial Intelligence System
 */
require_once __DIR__ . '/includes/auth.php';
requireGuest();

$pageTitle = "Sign In";
require_once __DIR__ . '/includes/header.php';
$csrfToken = generateCSRFToken();
?>

<div class="auth-card">
    <div class="auth-header">
        <div class="brand-badge">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
        </div>
        <h2>Welcome Back</h2>
        <p>Sign in to access your financial dashboard</p>
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

        <form action="actions/login_action.php" method="POST" id="loginForm" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-group">
                <label for="login_email" class="form-label">Email Address <span class="required">*</span></label>
                <input type="email" id="login_email" name="email" class="form-control" placeholder="you@example.com" required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="login_password" class="form-label">Password <span class="required">*</span></label>
                <input type="password" id="login_password" name="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="padding: 11px; margin-top: 10px;">
                Sign In
            </button>
        </form>

        <div class="demo-account-box" style="cursor: pointer;" onclick="document.getElementById('login_email').value='demo@example.com';document.getElementById('login_password').value='Password@123';" title="Click to autofill demo credentials">
            <span><strong>Demo Account:</strong> <code>demo@example.com</code></span>
            <span style="font-size: 11px; color: var(--primary); font-weight: 600;">Autofill</span>
        </div>
    </div>

    <div class="auth-footer">
        Don't have an account yet? <a href="register.php"><strong>Create an account</strong></a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
