<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

 $error = '';
 $done  = false;
 $valid_token = false;

// URL se token lo
 $raw_token = trim($_GET['token'] ?? $_POST['token'] ?? '');
 $token_hash = $raw_token ? hash('sha256', $raw_token) : '';

// Token valid hai?
if ($token_hash !== '') {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE reset_token = ? AND reset_expires > NOW() AND status = 1");
    $stmt->execute([$token_hash]);
    $admin = $stmt->fetch();
    $valid_token = $admin ? true : false;
}

// ===== Naya password set karo =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($new) < 6) {
        $error = 'Password kam se kam 6 characters ka ho!';
    } elseif ($new !== $confirm) {
        $error = 'Dono passwords match nahi kar rahe!';
    } else {
        $pdo->prepare("UPDATE admins SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?")
            ->execute([password_hash($new, PASSWORD_DEFAULT), $admin['id']]);
        $done = true;
    }
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Poppins',sans-serif; background:#d6eaf8; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
.card { background:#fff; border-radius:14px; width:100%; max-width:440px; overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,.12); }
.card-head { background:#0f2a5c; color:#fff; text-align:center; padding:30px 25px; }
.card-head i { font-size:36px; color:#f97316; }
.card-head h2 { font-size:19px; margin-top:10px; }
.card-body { padding:30px 35px; }
input { width:100%; padding:13px 15px; border:1px solid #d7e3ee; border-radius:8px; font-size:14px; font-family:inherit; margin-bottom:15px; }
input:focus { outline:none; border-color:#29abe2; }
button { width:100%; background:#0f2a5c; color:#fff; border:none; padding:14px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; font-family:inherit; }
button:hover { background:#f97316; }
.error { background:#fee2e2; color:#dc2626; padding:12px 15px; border-radius:8px; font-size:13.5px; margin-bottom:15px; }
.success { background:#dcfce7; color:#166534; padding:15px; border-radius:8px; font-size:14px; line-height:1.7; }
.invalid { color:#dc2626; font-size:14.5px; text-align:center; line-height:1.8; }
.link-c { text-align:center; margin-top:18px; }
.link-c a { color:#f97316; font-weight:600; font-size:13.5px; }
</style>
</head>
<body>
    <div class="card">
        <div class="card-head">
            <i class="fas fa-key"></i>
            <h2>Naya Password Set Karein</h2>
        </div>
        <div class="card-body">

            <?php if ($done): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i> <b>Password change ho gaya!</b><br>
                Ab naye password se login karein.
            </div>
            <div class="link-c"><a href="index.php"><i class="fas fa-arrow-right"></i> Login Karein</a></div>

            <?php elseif (!$valid_token): ?>
            <p class="invalid">
                <i class="fas fa-times-circle" style="font-size:34px;"></i><br><br>
                Ye link <b>invalid ya expire</b> ho gaya hai.<br>
                Dobara reset link request karein.
            </p>
            <div class="link-c"><a href="forgot-password.php"><i class="fas fa-redo"></i> Naya Link Bhejein</a></div>

            <?php else: ?>
            <?php if ($error): ?><div class="error"><i class="fas fa-exclamation-triangle"></i> <?= e($error) ?></div><?php endif; ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?= e($raw_token) ?>">
                <input type="password" name="new_password" placeholder="Naya Password (min 6)" minlength="6" required>
                <input type="password" name="confirm_password" placeholder="Confirm Naya Password" minlength="6" required>
                <button type="submit">Password Set Karein <i class="fas fa-check"></i></button>
            </form>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>