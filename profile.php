<?php
/**
 * User Profile and Security Settings
 * ExpenseMgr — Personal Financial Intelligence System
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$userId = getCurrentUserId();
$pdo = getDBConnection();

// Handle Profile Details Update (Name & Phone)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
        setFlash('error', "Security verification failed.");
        header("Location: profile.php");
        exit();
    }

    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $cleanedPhone = preg_replace('/\D/', '', $phone);

    if (mb_strlen($name) < 2) {
        setFlash('error', "Name must be at least 2 characters.");
    } elseif (strlen($cleanedPhone) !== 10) {
        setFlash('error', "Phone number must be exactly 10 digits.");
    } else {
        $updateStmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE user_id = ?");
        $updateStmt->execute([$name, $cleanedPhone, $userId]);
        $_SESSION['user_name'] = $name;
        setFlash('success', "Profile details successfully updated.");
    }

    header("Location: profile.php");
    exit();
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
        setFlash('error', "Security verification failed.");
        header("Location: profile.php");
        exit();
    }

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $userStmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
    $userStmt->execute([$userId]);
    $userRow = $userStmt->fetch();

    if (!$userRow || !password_verify($currentPassword, $userRow['password'])) {
        setFlash('error', "Current password is incorrect.");
    } elseif (strlen($newPassword) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]).{8,}$/', $newPassword)) {
        setFlash('error', "New password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
    } elseif ($newPassword !== $confirmPassword) {
        setFlash('error', "New password and Confirm Password do not match.");
    } else {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $pwdStmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $pwdStmt->execute([$hashed, $userId]);
        setFlash('success', "Password successfully updated.");
    }

    header("Location: profile.php");
    exit();
}

// Fetch current user details
$user = getUserById($userId);
$pageTitle = "Account Settings";
$csrfToken = generateCSRFToken();

require_once __DIR__ . '/includes/header.php';
?>

<div class="charts-grid" style="align-items: start;">
    <!-- Profile Info & Personal Details Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Personal Information</h2>
        </div>
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 22px;">
                <div class="avatar" style="width: 52px; height: 52px; font-size: 20px;">
                    <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <h3 style="font-size: 16px; margin-bottom: 2px; font-weight: 700;"><?= htmlspecialchars($user['name']) ?></h3>
                    <p style="font-size: 12px; color: var(--text-muted);">Account Active &bull; Member since <?= formatDate($user['created_at']) ?></p>
                </div>
            </div>

            <form action="profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="update_profile">

                <div class="form-group">
                    <label class="form-label">Email Address (Primary Identity)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" disabled style="background:#f8fafc; cursor:not-allowed;">
                    <p class="form-hint">Email address is bound to your account identity.</p>
                </div>

                <div class="form-group">
                    <label for="profile_name" class="form-label">Full Name <span class="required">*</span></label>
                    <input type="text" id="profile_name" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="profile_phone" class="form-label">Mobile Number <span class="required">*</span></label>
                    <input type="tel" id="profile_phone" name="phone" maxlength="10" value="<?= htmlspecialchars($user['phone']) ?>" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top: 6px;">
                    Save Profile Changes
                </button>
            </form>
        </div>
    </div>

    <!-- Security & Password Change Form -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Security &amp; Password</h2>
        </div>
        <div class="card-body">
            <form action="profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password <span class="required">*</span></label>
                    <input type="password" id="current_password" name="current_password" class="form-control" placeholder="Enter current password" required>
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">New Password <span class="required">*</span></label>
                    <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Minimum 8 characters" required>
                    <p class="form-hint">Requires uppercase, lowercase, digit, and symbol.</p>
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm New Password <span class="required">*</span></label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                </div>

                <button type="submit" class="btn btn-danger" style="margin-top: 6px;">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
