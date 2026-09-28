<?php
/**
 * A.L. Convent School & Girls' College Website
 *
 * Copyright © 2026 Braintech IT Services.
 * All rights reserved.
 *
 * This source code is proprietary.
 * Unauthorized copying, reproduction, modification,
 * redistribution or commercial use is prohibited
 * without prior written permission.
 */
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: ' . (($_SESSION['admin_role'] ?? '') === 'super' ? 'website-control.php' : 'dashboard.php'));
    exit;
}

 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([trim($_POST['username'])]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($_POST['password'], $admin['password'])) {
        if ($admin['status'] == 0) {
            $error = 'आपका account inactive कर दिया गया है। कृपया Super Admin से संपर्क करें।';
        } else {
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_name'] = $admin['name'] ?: $admin['username'];
            header('Location: ' . ($admin['role'] === 'super' ? 'website-control.php' : 'dashboard.php'));
            exit;
        }
    } else {
        $error = 'गलत username या password!';
    }
}

// Login page notices (news table se latest 3)
  $notices = $pdo->query("SELECT title, created_at FROM notices WHERE status = 1 ORDER BY id DESC LIMIT 4")->fetchAll();
 $settings = $pdo->query("SELECT school_name, logo FROM settings LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — <?= e($settings['school_name']) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Poppins',sans-serif; background:#d6eaf8; min-height:100vh; padding:20px; }
.wrap { max-width:1250px; margin:0 auto; min-height:calc(100vh - 40px); border:2px solid #29abe2; border-radius:10px; display:flex; background:#f4fafe; overflow:hidden; }

/* LEFT */
.left { flex:1.15; padding:40px 45px; display:flex; flex-direction:column; }
.dev-logo {
    display: flex;
    align-items: center;    /* logo + text vertically center */
    gap: 12px;
    margin-bottom: 35px;
}
.dev-logo-name {
    font-size: 23px;
    font-weight: 800;
    color: #1b3f94;         /* BRAIN — navy */
    line-height: 1;
    letter-spacing: .5px;
}
.dev-logo-name b { color: #f97316; }   /* TECH — orange */
.dev-logo-tag {
    color: #1b3f94;         /* IT SERVICES — navy (original jaisa) */
    font-size: 11.5px;
    font-weight: 600;
    letter-spacing: 4.5px;  /* wide spacing — original jaisa */
    margin-top: 5px;
}
.notice-box { background:#fff; border-radius:12px; padding:28px 30px; box-shadow:0 4px 18px rgba(0,0,0,.06); flex:1; min-height:280px; }
.notice-box h3 { color:#0f2a5c; font-size:20px; margin-bottom:18px; padding-bottom:14px; border-bottom:1px solid #eee; }
.notice-box h3 i { color:#dc2626; margin-right:8px; }
.notice-box ul { list-style:none; }
.notice-box li { padding:10px 0; border-bottom:1px dashed #eee; font-size:14px; color:#334155; }
.notice-box li:last-child { border:none; }
.n-date { background:#fff4ec; color:#f97316; font-weight:600; font-size:11.5px; padding:3px 10px; border-radius:20px; margin-right:10px; }
.no-notice { color:#94a3b8; font-size:15px; }
.dev-footer { margin-top:35px; }
.dev-footer p { color:#64748b; font-size:14px; }
.dev-footer h4 { color:#1b3f94; font-size:20px; }
.dev-footer h4 span { color:#f97316; }

/* RIGHT */
.right { flex:1; display:flex; align-items:center; justify-content:center; padding:30px; }
.card { background:#fff; border-radius:14px; overflow:hidden; width:100%; max-width:480px; box-shadow:0 10px 40px rgba(0,0,0,.12); }
.card-head { background:#0f2a5c; color:#fff; text-align:center; padding:32px 25px; }
.card-head img, .card-head .fallback { width:78px; height:78px; border-radius:50%; object-fit:contain; background:#fff; padding:4px; }
.card-head .fallback { display:flex; align-items:center; justify-content:center; font-size:30px; color:#f97316; margin:0 auto; }
.card-head h2 { font-size:19px; margin:14px 0 4px; }
.card-head p { font-size:13px; color:#cbd5e1; }
.card-body { padding:32px 35px; }
.card-body h3 { color:#0f2a5c; font-size:18px; display:flex; align-items:center; gap:9px; }
.dot { width:11px; height:11px; background:#f5b301; border-radius:50%; display:inline-block; }
.sub { color:#64748b; font-size:13.5px; margin:6px 0 20px; }
.error { background:#fee2e2; color:#dc2626; padding:11px 15px; border-radius:8px; font-size:13.5px; margin-bottom:16px; }
label { display:block; font-size:14px; font-weight:600; color:#0f2a5c; margin-bottom:7px; }
.input-wrap { position:relative; margin-bottom:18px; }
input { width:100%; padding:13px 15px; border:1px solid #d7e3ee; border-radius:8px; font-size:14px; font-family:inherit; }
input:focus { outline:none; border-color:#29abe2; box-shadow:0 0 0 3px rgba(41,171,226,.12); }
.toggle-pass { position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#29abe2; cursor:pointer; font-size:13px; }
.login-btn { width:100%; background:#0f2a5c; color:#fff; border:none; padding:14px; border-radius:8px; font-size:15.5px; font-weight:600; cursor:pointer; font-family:inherit; transition:.3s; }
.login-btn:hover { background:#f97316; }
.card-foot { text-align:center; color:#94a3b8; font-size:12.5px; margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9; }

@media (max-width:900px) {
    .wrap { flex-direction:column; }
    .left { padding:25px; }
    .notice-box { min-height:auto; }
}
@keyframes noticeGlow { 0%,100% { background:#fff; } 50% { background:#fff7ed; } }
.notice-glow { animation: noticeGlow 1.6s infinite; border-radius:8px; padding-left:8px !important; }
.n-badge { background:#dc2626; color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px; margin-left:8px; }
</style>
</head>
<body>
<div class="wrap">

    <!-- LEFT: Developer + Notices -->
    <div class="left">
            <div class="dev-logo">
                <?php if (file_exists(__DIR__ . '/../uploads/braintech-logo.png')): ?>
                    <img src="../uploads/braintech-logo.png" alt="BrainTech"
                        style="height: 62px; width: auto; display: block;">
                <?php else: ?>
                    <i class="fas fa-shield-halved" style="font-size: 52px; color: #1b3f94;"></i>
                <?php endif; ?>
                <div class="dev-logo-text">
                    <div class="dev-logo-name">BRAIN<b>TECH</b></div>
                    <div class="dev-logo-tag">IT SERVICES</div>
                </div>
            </div>
        <div class="notice-box">
            <h3><i class="fas fa-bullhorn"></i> Notices :</h3>
            <?php if (empty($notices)): ?>
                <p class="no-notice">अभी कोई सूचना उपलब्ध नहीं है।</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($notices as $n):
                        $is_new = (time() - strtotime($n['created_at'])) < (7 * 86400); // 7 din tak NEW
                    ?>
                    <li class="<?= $is_new ? 'notice-glow' : '' ?>">
                        <span class="n-date"><?= date('d M', strtotime($n['created_at'])) ?></span>
                        <?= e($n['title']) ?>
                        <?php if ($is_new): ?><span class="n-badge">NEW</span><?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="dev-footer">
            <p>Design & Developed By</p>
            <h4>BrainTech <span>IT SERVICES</span></h4>
        </div>
    </div>

    <!-- RIGHT: Login Card -->
    <div class="right">
        <div class="card">
            <div class="card-head">
                <?php if (!empty($settings['logo']) && file_exists('../' . $settings['logo'])): ?>
                    <img src="../<?= e($settings['logo']) ?>" alt="School Logo">
                <?php else: ?>
                    <div class="fallback"><i class="fas fa-school"></i></div>
                <?php endif; ?>
                <h2><?= e($settings['school_name']) ?></h2>
                <p>Website Administration Panel</p>
            </div>
            <div class="card-body">
                <h3><span class="dot"></span> Admin Login</h3>
                <p class="sub">अपने authorized account से login करें।</p>

                <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>

                <form method="POST">
                    <label>Username</label>
                    <div class="input-wrap">
                        <input type="text" name="username" placeholder="अपना username दर्ज करें" required>
                    </div>
                    <label>Password</label>
                    <div class="input-wrap">
                        <input type="password" name="password" id="passField" placeholder="अपना password दर्ज करें" required>
                        <span class="toggle-pass" onclick="var f=document.getElementById('passField'); f.type=f.type==='password'?'text':'password';"><b>दिखाएँ</b></span>
                    </div>
                    <button type="submit" class="login-btn">Login करें <i class="fas fa-arrow-right"></i></button>
                </form>
                <p style="text-align:center; margin-top:15px;">
    <a href="forgot-password.php" style="color:#29abe2; font-size:13.5px; text-decoration:none;">
        <i class="fas fa-lock-open"></i> Password bhool gaye?
    </a>
</p>
                <p class="card-foot">केवल अधिकृत विद्यालय प्रशासन के लिए</p>
            </div>
        </div>
    </div>

</div>
</body>
</html>