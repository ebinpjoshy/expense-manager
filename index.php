<?php
/**
 * Project Home / Landing Page
 * ExpenseMgr — Personal Financial Intelligence System
 */
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit();
}

$pageTitle = "Personal Expense & Financial Manager";
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-card" style="max-width: 580px; margin: 40px auto; text-align: center;">
    <div class="auth-header">
        <div class="brand-badge">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="1" x2="12" y2="23"></line>
                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
        </div>
        <h2>ExpenseMgr</h2>
        <p>Intelligent Financial Tracking &amp; Cash Flow Analysis</p>
    </div>
    
    <div class="auth-body" style="padding: 32px 28px;">
        <h3 style="font-size: 18px; color: var(--text-main); margin-bottom: 10px; font-weight: 700;">
            Master Your Daily Expenses &amp; Build Financial Freedom
        </h3>
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 24px; line-height: 1.6;">
            A unified financial management platform to track incoming earnings, categorize daily expenditures, monitor monthly budget ceilings, and analyze spending patterns with real-time visual charts.
        </p>

        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 24px;">
            <a href="login.php" class="btn btn-primary" style="padding: 11px 24px; font-size: 14px;">Sign In to Account</a>
            <a href="register.php" class="btn btn-secondary" style="padding: 11px 24px; font-size: 14px;">Create Free Account</a>
        </div>

        <div class="demo-account-box">
            <span><strong>Demo Access:</strong> <code>demo@example.com</code> / <code>Password@123</code></span>
            <a href="login.php" style="font-size: 12px; font-weight: 600; text-decoration: underline;">Auto-Fill &rarr;</a>
        </div>
    </div>
    
    <div class="auth-footer">
        Bank-grade session encryption &bull; Real-time budget monitoring &bull; Private data isolation
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
