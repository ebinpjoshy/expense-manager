<?php
/**
 * Footer Component
 * ExpenseMgr — Personal Financial Intelligence System
 */
?>
<?php if (isLoggedIn()): ?>
        </main> <!-- /content-container -->

        <!-- Mobile Bottom Navigation Bar (Visible on phones & tablets <= 768px) -->
        <nav class="mobile-bottom-nav">
            <a href="dashboard.php" class="mobile-nav-item <?= ($currentPage ?? '') === 'dashboard.php' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span>Home</span>
            </a>

            <a href="transactions.php" class="mobile-nav-item <?= ($currentPage ?? '') === 'transactions.php' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                </svg>
                <span>Ledger</span>
            </a>

            <a href="add_expense.php" class="mobile-nav-item fab-item" title="Quick Add Expense">
                <div class="fab-circle">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </div>
                <span>Add</span>
            </a>

            <a href="budget.php" class="mobile-nav-item <?= ($currentPage ?? '') === 'budget.php' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <circle cx="12" cy="12" r="6"></circle>
                    <circle cx="12" cy="12" r="2"></circle>
                </svg>
                <span>Budgets</span>
            </a>

            <a href="reports.php" class="mobile-nav-item <?= ($currentPage ?? '') === 'reports.php' ? 'active' : '' ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
                <span>Analytics</span>
            </a>
        </nav>

        <footer class="app-footer">
            <div class="footer-content">
                <p>&copy; <?= date('Y') ?> ExpenseMgr &mdash; Intelligent Personal Finance &amp; Budget Management System. All rights reserved.</p>
                <p class="footer-tech">Encrypted Sessions &bull; Multi-Account Isolation &bull; Real-time Analytics</p>
            </div>
        </footer>
    </div> <!-- /main-wrapper -->
<?php endif; ?>

    <!-- Application JavaScript -->
    <script src="js/validation.js"></script>
    <script src="js/script.js"></script>
    <script src="js/charts.js"></script>
</body>
</html>
