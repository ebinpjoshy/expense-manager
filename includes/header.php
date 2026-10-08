<?php
/**
 * Header and Navigation Component
 * ExpenseMgr — Personal Financial Intelligence System
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitle = $pageTitle ?? 'Financial Management';
$isUserLoggedIn = isLoggedIn();
$userName = $isUserLoggedIn ? getCurrentUserName() : '';
$cssVersion = file_exists(__DIR__ . '/../css/style.css') ? filemtime(__DIR__ . '/../css/style.css') : time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title><?= htmlspecialchars($pageTitle) ?> &bull; ExpenseMgr</title>
    <!-- Cache-busted responsive stylesheet -->
    <link rel="stylesheet" href="css/style.css?v=<?= $cssVersion ?>">
    <!-- Chart.js via CDN with local fallback -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        if (typeof Chart === 'undefined') {
            document.write('<script src="js/chart.min.js"><\/script>');
        }
    </script>
</head>
<body class="<?= $isUserLoggedIn ? 'app-layout' : 'auth-layout' ?>">

<?php if ($isUserLoggedIn): ?>
    <!-- Mobile Backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <div class="brand-text">
                <h2>Expense<span class="brand-accent">Mgr</span></h2>
                <span>Financial Intelligence</span>
            </div>
        </div>

        <div class="user-profile-badge">
            <div class="avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
            <div class="user-info">
                <span class="user-name"><?= $userName ?></span>
                <span class="user-role">Personal Account</span>
            </div>
        </div>

        <nav class="sidebar-menu">
            <ul>
                <li class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                    <a href="dashboard.php">
                        <svg class="menu-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span class="menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="<?= $currentPage === 'add_income.php' ? 'active' : '' ?>">
                    <a href="add_income.php">
                        <svg class="menu-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        <span class="menu-text">Add Income</span>
                    </a>
                </li>
                <li class="<?= $currentPage === 'add_expense.php' ? 'active' : '' ?>">
                    <a href="add_expense.php">
                        <svg class="menu-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        <span class="menu-text">Add Expense</span>
                    </a>
                </li>
                <li class="<?= $currentPage === 'transactions.php' ? 'active' : '' ?>">
                    <a href="transactions.php">
                        <svg class="menu-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                        <span class="menu-text">Transactions</span>
                    </a>
                </li>
                <li class="<?= $currentPage === 'budget.php' ? 'active' : '' ?>">
                    <a href="budget.php">
                        <svg class="menu-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="6"></circle>
                            <circle cx="12" cy="12" r="2"></circle>
                        </svg>
                        <span class="menu-text">Budget System</span>
                    </a>
                </li>
                <li class="<?= $currentPage === 'reports.php' ? 'active' : '' ?>">
                    <a href="reports.php">
                        <svg class="menu-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10"></line>
                            <line x1="12" y1="20" x2="12" y2="4"></line>
                            <line x1="6" y1="20" x2="6" y2="14"></line>
                        </svg>
                        <span class="menu-text">Reports & Trends</span>
                    </a>
                </li>
                <li class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>">
                    <a href="profile.php">
                        <svg class="menu-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span class="menu-text">Account Settings</span>
                    </a>
                </li>
                <li class="logout-link">
                    <a href="logout.php">
                        <svg class="menu-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span class="menu-text">Sign Out</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-wrapper">
        <!-- Top Navigation Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="page-headline">
                    <h1><?= htmlspecialchars($pageTitle) ?></h1>
                </div>
            </div>
            <div class="topbar-right">
                <a href="add_income.php" class="btn btn-sm btn-outline-success">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Income
                </a>
                <a href="add_expense.php" class="btn btn-sm btn-outline-danger">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    Expense
                </a>
                <div class="topbar-user">
                    <span><?= $userName ?></span>
                    <a href="logout.php" class="btn btn-sm btn-secondary">Sign Out</a>
                </div>
            </div>
        </header>

        <!-- Main Workspace Container -->
        <main class="content-container">
            <?php
            $flashSuccess = getFlash('success');
            $flashError = getFlash('error');
            ?>
            <?php if ($flashSuccess): ?>
                <div class="alert alert-success">
                    <svg class="alert-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span><?= htmlspecialchars($flashSuccess) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($flashError): ?>
                <div class="alert alert-danger">
                    <svg class="alert-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?= htmlspecialchars($flashError) ?></span>
                </div>
            <?php endif; ?>
<?php endif; ?>
