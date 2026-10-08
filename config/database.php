<?php
/**
 * Database Configuration and PDO Connection
 * Personal Expense Management System
 */

// Database credentials for local XAMPP
define('DB_HOST', 'localhost');
define('DB_NAME', 'expense_manager');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Returns a shared PDO database connection instance.
 *
 * @return PDO
 */
function getDBConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Friendly error message for college testing without exposing passwords
            die("<div style='font-family:sans-serif;max-width:600px;margin:50px auto;padding:25px;border:1px solid #f5c6cb;background:#f8d7da;color:#721c24;border-radius:8px;'>"
                . "<h2 style='margin-top:0;'>Database Connection Failed</h2>"
                . "<p>Unable to connect to MySQL database <strong>" . htmlspecialchars(DB_NAME) . "</strong>.</p>"
                . "<p><strong>Quick Checklist:</strong></p>"
                . "<ol>"
                . "<li>Make sure <strong>Apache</strong> and <strong>MySQL</strong> are started in the XAMPP Control Panel.</li>"
                . "<li>Make sure you created the database <code>expense_manager</code> in <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a> and imported <code>database/expense_manager.sql</code>.</li>"
                . "<li>Verify credentials in <code>config/database.php</code> (Default username is <code>root</code> with empty password).</li>"
                . "</ol>"
                . "<p style='font-size:12px;color:#666;'>Technical Detail: " . htmlspecialchars($e->getMessage()) . "</p>"
                . "</div>");
        }
    }

    return $pdo;
}

// Global connection variable for convenient access across scripts
$pdo = getDBConnection();
