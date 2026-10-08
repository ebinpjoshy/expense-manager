# Personal Expense Management System
**Alternative Title:** Personal Expense Manager – Income, Expense and Financial Analysis System

A modern, secure, and fully functional full-stack web application designed for personal financial accounting, weekly/monthly spending analysis, budget monitoring, and cash-flow reporting.

Built specifically as a college group project using pure web technologies: **HTML5, CSS3, Vanilla JavaScript, PHP 8+, and MySQL**, runnable locally on Windows using **XAMPP (Apache + MySQL)** without external frameworks like React, Node.js, Bootstrap, or Tailwind.

---

## 1. Table of Contents
- [Project Overview](#2-project-overview)
- [Key Features](#3-key-features)
- [Technologies Used](#4-technologies-used)
- [System Architecture & Folder Structure](#5-system-architecture--folder-structure)
- [Database Schema & ER Relationships](#6-database-schema--er-relationships)
- [Step-by-Step Installation Guide (Windows XAMPP)](#7-step-by-step-installation-guide-windows-xampp)
- [Demo Credentials](#8-demo-credentials)
- [How the Financial Dashboard Works](#9-how-the-financial-dashboard-works)
- [Security Features](#10-security-features)
- [Troubleshooting & FAQs](#11-troubleshooting--faqs)
- [Group Member Work Allocation](#12-group-member-work-allocation)
- [College Viva Voce Q&A](#13-college-viva-voce-qa)

---

## 2. Project Overview
Most simple student expense trackers are basic CRUD applications that only add and delete transactions in a flat list. This project is specifically designed to provide **meaningful financial analytics and intelligence**:
- Real-time balance calculations (`Total Income - Total Expenses`).
- Dynamic time-period filtering: *This Week*, *Last Week*, *This Month*, *Last Month*, *This Year*, and *Custom Date Range*.
- Automated **Budget Monitoring System** with intelligent warning alerts when spending crosses 80% and 100% of defined monthly limits.
- Category-wise analysis (Food, Transport, Rent, Bills, Education, Shopping, etc.) and Payment-channel analysis (Cash, GPay, PhonePe, Bank, Card).
- Visual charts using Chart.js with full local offline fallback support.
- Complete multi-user data isolation: every registered student or user maintains their own private financial records.

---

## 3. Key Features
1. **User Authentication & Session Management**:
   - Secure registration with strong password policies (uppercase, lowercase, number, special character).
   - BCrypt hashing via `password_hash()` and `password_verify()`.
   - Protection against session fixation with `session_regenerate_id(true)`.
   - CSRF protection on forms.
2. **Income & Expense Tracking**:
   - Categorized income (Salary, Scholarship, Freelance, Pocket Money, Business, Other).
   - Categorized expenses (Food, Transport, Education, Shopping, Bills, Entertainment, Health, Rent, Travel, Other).
   - Payment method recording (Cash, GPay, PhonePe, Bank, Card, Other).
3. **Dynamic Financial Dashboard**:
   - 4 Live Metric Cards: Total Income, Total Expenses, Remaining Balance, and Transaction Count.
   - Day-by-Day spending analysis (Monday through Sunday) for weekly tracking.
   - Week 1 through Week 5 monthly spending trends.
   - Interactive Doughnut and Bar charts.
4. **Intelligent Monthly Budget System**:
   - Set monthly limits for any expense category.
   - Compares budgeted amount against actual spending.
   - Dynamic visual progress bars.
   - Color-coded alerts: Green (under control), Amber Warning (>= 80%), Red Exceeded (>= 100%).
5. **Comprehensive Reports & Trends**:
   - 12-Month cash flow comparison (Income vs Expense month by month).
   - Top spending category detection and savings rate percentage.
   - One-click print-ready reporting format.
6. **Transactions Management**:
   - Filter by date range, transaction type, category, and payment channel.
   - Keyword search across descriptions and categories.
   - In-place editing and deletion with JavaScript confirmation safeguards.
7. **Profile & Security Settings**:
   - Update full name and contact number.
   - Change password with mandatory current password authentication.

---

## 4. Technologies Used
- **Frontend**:
  - **HTML5**: Semantic tags (`<header>`, `<aside>`, `<main>`, `<section>`, `<nav>`, `<table>`, `<form>`).
  - **CSS3**: Responsive flexbox and CSS grid layouts, CSS custom properties (variables), card UI, mobile drawers.
  - **Vanilla JavaScript**: Pure JS without libraries for form validation, delete modals, responsive sidebar toggle, and Chart.js instantiation.
  - **Chart.js**: Included via CDN with a bundled offline local file (`js/chart.min.js`) for reliable presentation in college labs without internet.
- **Backend**:
  - **PHP 8+**: Procedural + structured PHP, PDO (PHP Data Objects) with prepared statements, session management, and CSRF protection.
- **Database**:
  - **MySQL (XAMPP MariaDB/MySQL)**: Relational schema, InnoDB storage engine, foreign keys with cascade constraints, unique compound indexes.
- **Web Server**:
  - **Apache HTTP Server** (via XAMPP for Windows).

---

## 5. System Architecture & Folder Structure
```text
C:\xampp\htdocs\expense-manager\
│
├── index.php                 # Project landing page with demo details
├── login.php                 # User login interface
├── register.php              # User registration interface
├── logout.php                # Secure logout and session destruction
│
├── dashboard.php             # Core analytics dashboard (cards, charts, filters)
├── add_income.php            # Form to record incoming earnings
├── add_expense.php           # Form to log expenditures
├── transactions.php          # Full transaction statement with multi-filter search
├── edit_transaction.php     # Form to modify an existing transaction
├── delete_transaction.php   # Direct deletion proxy
├── reports.php               # Long-term trends, cash flow, top spending reports
├── budget.php                # Monthly category budget setter and warning system
├── profile.php               # Profile manager and password updater
│
├── config/
│   └── database.php          # PDO database connection configuration
│
├── includes/
│   ├── auth.php              # Session checks, requireLogin(), CSRF helpers
│   ├── header.php            # Shared header, navigation sidebar, and topbar
│   ├── footer.php            # Shared footer layout and JS script tags
│   └── functions.php         # Reusable SQL calculation functions & analytics
│
├── actions/
│   ├── register_action.php   # Handles account registration POST
│   ├── login_action.php      # Handles credentials check and session initialization
│   ├── income_action.php     # Processes new income transactions
│   ├── expense_action.php    # Processes expenses and checks budget thresholds
│   ├── update_transaction.php# Executes transaction updates
│   ├── delete_transaction.php# Executes transaction deletions safely
│   └── budget_action.php     # Sets, updates, and deletes budget limits
│
├── css/
│   └── style.css             # Pure CSS3 stylesheet (no Bootstrap / Tailwind)
│
├── js/
│   ├── script.js             # Mobile sidebar toggle, date toggler, delete confirm
│   ├── validation.js         # Client-side form input validation
│   ├── charts.js             # Chart.js initialization for Doughnut & Bar charts
│   └── chart.min.js          # Standalone offline copy of Chart.js
│
├── database/
│   └── expense_manager.sql   # Complete SQL schema with tables & sample demo data
│
└── README.md                 # Complete documentation and viva guide
```

---

## 6. Database Schema & ER Relationships

### Database Name: `expense_manager`

### 1. Table `users`
| Column | Type | Constraints | Description |
|---|---|---|---|
| `user_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique user identifier |
| `name` | VARCHAR(100) | NOT NULL | User's full name |
| `email` | VARCHAR(150) | NOT NULL, UNIQUE | User login email |
| `phone` | VARCHAR(15) | NOT NULL | User contact number |
| `password` | VARCHAR(255) | NOT NULL | BCrypt hashed password |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Account creation timestamp |

### 2. Table `categories`
| Column | Type | Constraints | Description |
|---|---|---|---|
| `category_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique category identifier |
| `category_name`| VARCHAR(100) | NOT NULL | Category name |
| `type` | ENUM('income','expense') | NOT NULL | Categorizes income or expense |

### 3. Table `transactions`
| Column | Type | Constraints | Description |
|---|---|---|---|
| `transaction_id`| INT | PRIMARY KEY, AUTO_INCREMENT | Unique transaction identifier |
| `user_id` | INT | NOT NULL, FOREIGN KEY (users) | User who owns the record |
| `category_id` | INT | NOT NULL, FOREIGN KEY (categories) | Category reference |
| `amount` | DECIMAL(10,2)| NOT NULL | Monetary amount |
| `type` | ENUM('income','expense') | NOT NULL | Direction of fund |
| `payment_method`| VARCHAR(50)| NOT NULL | Mode: Cash, GPay, Bank, etc. |
| `transaction_date`| DATE | NOT NULL | Date transaction occurred |
| `description` | VARCHAR(255)| DEFAULT NULL | Note or merchant memo |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Record insertion timestamp |

### 4. Table `budgets`
| Column | Type | Constraints | Description |
|---|---|---|---|
| `budget_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique budget identifier |
| `user_id` | INT | NOT NULL, FOREIGN KEY (users) | User setting the limit |
| `category_id` | INT | NOT NULL, FOREIGN KEY (categories) | Budgeted expense category |
| `amount` | DECIMAL(10,2)| NOT NULL | Target spending ceiling |
| `month` | INT | NOT NULL | Calendar month (1 - 12) |
| `year` | INT | NOT NULL | Calendar year (e.g. 2026) |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Creation timestamp |
| **Unique Key** | `(user_id, category_id, month, year)` | Prevents duplicate records per cycle |

---

## 7. Step-by-Step Installation Guide (Windows XAMPP)

### Prerequisites:
1. **Windows 10 / 11**.
2. **XAMPP for Windows** (Download from [apachefriends.org](https://www.apachefriends.org/)).
3. Google Chrome, Edge, or Firefox browser.
4. Visual Studio Code (optional, for code inspection).

---

### Step 1: Start XAMPP Services
1. Open the **XAMPP Control Panel** from the Windows Start menu or `C:\xampp\xampp-control.exe`.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.
4. Confirm both modules show green status badges.

---

### Step 2: Verify Project Location
Ensure the project folder is placed in:
```text
C:\xampp\htdocs\expense-manager\
```

---

### Step 3: Create Database & Import SQL
1. Open your web browser and navigate to:
   ```text
   http://localhost/phpmyadmin/
   ```
2. Click on the **Databases** tab at the top.
3. In the "Database name" box, type:
   ```text
   expense_manager
   ```
4. Select `utf8mb4_unicode_ci` and click **Create**.
5. Select the newly created `expense_manager` database from the left sidebar.
6. Click on the **Import** tab on the top menu bar.
7. Click **Choose File** and browse to:
   ```text
   C:\xampp\htdocs\expense-manager\database\expense_manager.sql
   ```
8. Scroll to the bottom and click **Import** (or **Go**).
9. phpMyAdmin will confirm: *"Import has been successfully finished, queries executed."*

---

### Step 4: Verify Database Connection Configuration
Open `C:\xampp\htdocs\expense-manager\config\database.php` in any text editor.
Default settings match standard XAMPP configurations:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'expense_manager');
define('DB_USER', 'root');
define('DB_PASS', '');
```
*(If your local MySQL root user has a custom password, enter it in `DB_PASS`)*.

---

### Step 5: Launch the Application
Open your browser and enter:
```text
http://localhost/expense-manager/
```
The home landing page will load.

---

## 8. Demo Credentials
For viva voce demonstrations and immediate evaluation, a pre-configured user with realistic transactions and budget limits is included in `database/expense_manager.sql`:

- **Email**: `demo@example.com`
- **Password**: `Password@123`
- **User Name**: Rahul Sharma

*(You can also register a brand new user on `register.php` with your own details!)*

---

## 9. How the Financial Dashboard Works
When `dashboard.php` loads:
1. **Authentication Check**: `requireLogin()` verifies that `$_SESSION['user_id']` exists.
2. **Date Period Resolver**: `resolveDateRange()` translates the selected period filter (`this_week`, `last_week`, `this_month`, `last_month`, `this_year`, `custom`) into precise SQL `BETWEEN` dates.
3. **Database Calculations**:
   - `getTotalIncome()` executes `SELECT SUM(amount) WHERE type = 'income' AND date BETWEEN ? AND ?`.
   - `getTotalExpenses()` executes `SELECT SUM(amount) WHERE type = 'expense' AND date BETWEEN ? AND ?`.
   - Balance is computed as `Income - Expense`.
4. **Visual Analytics**:
   - `getCategoryExpenses()` groups expenses by category and calculates percentages.
   - `getWeeklyDailyExpenses()` aggregates spending for each day from Monday to Sunday.
   - `getMonthlyWeeklyBreakdown()` aggregates spending for Week 1 to Week 5.
   - `getPaymentMethodExpenses()` groups payments by GPay, PhonePe, Cash, Bank, Card.
5. **Interactive Charting**:
   - PHP encodes data into JSON (`json_encode()`).
   - `js/charts.js` renders responsive Chart.js Doughnut and Bar charts.
   - If the computer is offline, the local `js/chart.min.js` file is loaded automatically.

---

## 10. Security Features
1. **Prepared Statements with PDO**:
   - All SQL queries use positional parameters (`?`) bound through PDO.
   - Prevents SQL Injection (SQLi) attacks.
2. **BCrypt Password Hashing**:
   - Implemented using PHP's native `password_hash($pwd, PASSWORD_BCRYPT)`.
   - Verified via `password_verify()`. No plain-text passwords ever touch the database.
3. **Session Fixation Countermeasures**:
   - Upon successful login, `session_regenerate_id(true)` invalidates the previous session token and issues a fresh one.
4. **Data Isolation (Authorization Enforcement)**:
   - Every `SELECT`, `UPDATE`, and `DELETE` query includes `WHERE user_id = ?`.
   - Prevents IDOR (Insecure Direct Object Reference) vulnerabilities so users cannot view or delete another user's financial records.
5. **Cross-Site Scripting (XSS) Prevention**:
   - All user inputs rendered into HTML are sanitized using `htmlspecialchars($string, ENT_QUOTES, 'UTF-8')`.
6. **CSRF Tokens**:
   - State-changing forms generate and check cryptographically secure tokens via `bin2hex(random_bytes(32))` and `hash_equals()`.

---

## 11. Troubleshooting & FAQs

### Q1: "Database Connection Failed" Error
- **Cause**: MySQL service is not running in XAMPP or the database hasn't been created yet.
- **Solution**: Open XAMPP Control Panel and click "Start" next to MySQL. Ensure you created `expense_manager` in phpMyAdmin and imported `database/expense_manager.sql`.

### Q2: Charts are blank or not loading
- **Cause**: External CDN blocked by college lab firewall.
- **Solution**: The application already includes `js/chart.min.js` as an automatic local fallback. Ensure JavaScript is enabled in your browser settings.

### Q3: Password validation fails during registration
- **Cause**: Password policy requires at least 8 characters with at least one uppercase letter, one lowercase letter, one number, and one special character (e.g. `Student@2026`).

---

## 12. Group Member Work Allocation
For a 4-member college project group:

### Member 1: Frontend Design & Responsive UI
- HTML5 structural templates for all pages (`header.php`, `footer.php`, forms, tables).
- Pure CSS3 styling (`style.css`), typography, CSS grid cards, color system, and mobile sidebar navigation.

### Member 2: Client-Side JavaScript & Data Visualization
- Form validation scripts in `validation.js` (regex checks, real-time feedback).
- Chart.js integration in `charts.js` (Doughnut, Bar, Trend charts, tooltips, local fallback).
- User interaction logic in `script.js` (sidebar toggler, custom date range toggler, delete confirmation dialogs).

### Member 3: PHP Backend Architecture & Business Logic
- Authentication flow (`login_action.php`, `register_action.php`, `logout.php`, session management).
- Transaction CRUD operations (`income_action.php`, `expense_action.php`, `update_transaction.php`, `delete_transaction.php`).
- Budget calculation algorithms and 80%/100% threshold alert triggers.

### Member 4: Database Administration, Security & Testing
- MySQL database schema (`expense_manager.sql`), relational keys, InnoDB engine, and indexes.
- Security enforcement: PDO prepared statements, password hashing, XSS escaping, and CSRF tokens.
- Test suites, sample dataset generation, and viva voce presentation preparation.

---

## 13. College Viva Voce Q&A

### HTML & CSS:
**Q1: What is Semantic HTML and why did you use it?**
> Semantic HTML tags (like `<header>`, `<aside>`, `<main>`, `<nav>`, `<table>`) clearly describe their meaning to both the browser and the developer. This improves code readability, search engine indexing, and screen reader accessibility compared to generic `<div>` tags.

**Q2: How is the application made responsive without Bootstrap or Tailwind?**
> We used native CSS3 Flexbox and CSS Grid alongside `@media (max-width: 992px)` and `@media (max-width: 600px)` media queries. On mobile, grid columns collapse into single columns and the sidebar transforms into an off-canvas drawer.

---

### JavaScript:
**Q3: Why perform both client-side and server-side validation?**
> Client-side validation (in `validation.js`) provides immediate feedback to the user without reloading the page, saving server bandwidth. However, client-side validation can be bypassed by disabling JavaScript or using tools like Postman/cURL. Server-side validation in PHP is mandatory for absolute security.

**Q4: How are charts generated dynamically?**
> PHP queries the MySQL database, aggregates the amounts, and outputs them as JSON arrays using `json_encode()`. Vanilla JavaScript receives these datasets and passes them to Chart.js canvas elements to render visual charts.

---

### PHP:
**Q5: What is the difference between `password_hash()` and MD5 / SHA-1?**
> MD5 and SHA-1 are fast hashing algorithms that are vulnerable to rainbow table attacks and collision vulnerabilities. PHP's `password_hash()` uses BCrypt, which automatically incorporates a cryptographically random salt and a configurable work factor (cost) to resist brute-force attacks.

**Q6: What is a PHP Session and how does it maintain authentication?**
> HTTP is a stateless protocol. PHP sessions allow the server to store user state across multiple requests. When `session_start()` is called, PHP sends a unique cookie (`PHPSESSID`) to the browser. The server associates this ID with stored variables like `$_SESSION['user_id']`.

**Q7: Why did you use PDO over `mysqli`?**
> PDO (PHP Data Objects) is an object-oriented database abstraction layer that supports 12 different database drivers (allowing easy database portability), supports named and positional prepared statements, and provides robust exception handling.

---

### MySQL:
**Q8: What is a Foreign Key and why is `ON DELETE CASCADE` used?**
> A foreign key links a column in one table to the primary key of another table, ensuring referential integrity. `ON DELETE CASCADE` ensures that if a user deletes their account, all associated transactions and budgets are automatically deleted to prevent orphaned records.

**Q9: Why use Prepared Statements?**
> Prepared statements separate the query structure from the data. The SQL query is pre-compiled by the database engine, and user parameters are sent separately as literals. This makes it impossible for malicious user input to alter the SQL logic, completely preventing SQL injection.

---

### Project-Specific Questions:
**Q10: How does the budget warning system work?**
> When viewing budgets or logging an expense, PHP computes `(actual_spent / budget_limit) * 100`. If this ratio is between 80% and 99%, the system assigns an amber warning status. If it reaches or exceeds 100%, it triggers a red "Budget Exceeded" alert.

**Q11: How do you prevent User A from seeing User B's transactions?**
> Every data query strictly filters by the currently authenticated user's session ID (`WHERE user_id = ?`). Even if a user alters the URL parameter `id` on `edit_transaction.php` or `delete_transaction.php`, the query verifies ownership (`WHERE transaction_id = ? AND user_id = ?`). If ownership does not match, access is denied.
