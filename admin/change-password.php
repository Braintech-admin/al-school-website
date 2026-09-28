<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

 $error = '';
 $success = '';
 $email_success = '';

// ===== CHANGE PASSWORD =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_pass'])) {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    // Current password verify karo
    $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($current, $row['password'])) {
        $error = 'Current password galat hai!';
    } elseif (strlen($new) < 6) {
        $error = 'Naya password kam se kam 6 characters ka ho!';
    } elseif ($new !== $confirm) {
        $error = 'Naya password aur Confirm password match nahi kar rahe!';
    } elseif ($current === $new) {
        $error = 'Naya password purane jaisa nahi ho sakta!';
    } else {
        $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?")
            ->execute([password_hash($new, PASSWORD_DEFAULT), $_SESSION['admin_id']]);
        $success = 'Password change ho gaya! Agli baar naye password se login karna.';
    }
}

// ===== SAVE RECOVERY EMAIL =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_email'])) {
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    if ($email) {
        $pdo->prepare("UPDATE admins SET email = ? WHERE id = ?")
            ->execute([$email, $_SESSION['admin_id']]);
        $email_success = 'Recovery email save ho gaya! Ab password bhoolne par reset link isi email pe aayega.';
    } else {
        $email_success = '';
        $error = 'Valid email address daalo!';
    }
}

// Current recovery email fetch karo
 $stmt = $pdo->prepare("SELECT email FROM admins WHERE id = ?");
 $stmt->execute([$_SESSION['admin_id']]);
 $my_email = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-key"></i> Account Settings</h2>

        <?php if ($success): ?>
        <div class="alert-success"><i class="fas fa-check-circle"></i> <?= e($success) ?></div>
        <?php endif; ?>

        <?php if ($email_success): ?>
        <div class="alert-success"><i class="fas fa-check-circle"></i> <?= e($email_success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert-error"><i class="fas fa-exclamation-triangle"></i> <?= e($error) ?></div>
        <?php endif; ?>

        <!-- ===== SECTION 1: CHANGE PASSWORD ===== -->
        <div class="form-box" style="max-width:520px;">
            <h3><i class="fas fa-key"></i> Change Password</h3>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="change_pass" value="1">
                <label>Current Password</label>
                <input type="password" name="current_password" required>

                <label>Naya Password (min 6 characters)</label>
                <input type="password" name="new_password" minlength="6" required>

                <label>Confirm Naya Password</label>
                <input type="password" name="confirm_password" minlength="6" required>

                <button type="submit" class="btn btn-add" style="margin-top:12px;">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </form>
        </div>

        <!-- ===== SECTION 2: RECOVERY EMAIL ===== -->
        <div class="form-box" style="max-width:520px;">
            <h3><i class="fas fa-envelope"></i> Recovery Email</h3>
            <p style="font-size:13.5px; color:#64748b; margin-bottom:15px;">
                Password bhool jaane par reset link isi email pe aayega. Ye zaroor set karo!
            </p>
            <form method="POST" autocomplete="off">
                <input type="hidden" name="save_email" value="1">
                <label>Apna Email Address</label>
                <input type="email" name="email" value="<?= e($my_email ?: '') ?>"
                       placeholder="e.g. apna@gmail.com" required>
                <?php if ($my_email): ?>
                <p style="font-size:12.5px; color:#16a34a; margin-bottom:10px;">
                    <i class="fas fa-check-circle"></i> Recovery email set hai: <b><?= e($my_email) ?></b>
                </p>
                <?php else: ?>
                <p style="font-size:12.5px; color:#dc2626; margin-bottom:10px;">
                    <i class="fas fa-exclamation-circle"></i> Abhi koi recovery email set nahi hai!
                </p>
                <?php endif; ?>
                <button type="submit" class="btn btn-add">
                    <i class="fas fa-save"></i> Save Email
                </button>
            </form>
        </div>

        <!-- ===== SECURITY TIPS ===== -->
        <div class="form-box" style="border-left:4px solid #f97316; max-width:520px;">
            <h3 style="font-size:14px;"><i class="fas fa-shield-alt"></i> Security Tips</h3>
            <p style="font-size:13.5px; color:#334155; line-height:1.8;">
                • Strong password banao — letters + numbers + special characters mila ke<br>
                • Password kisi ke saath share mat karo, chahe wo team member ho<br>
                • Password bhool gaye to <b>login page → "Password bhool gaye?"</b> se reset karo<br>
                • Recovery email hamesha updated rakho
            </p>
        </div>
    </div>
</body>
</html>