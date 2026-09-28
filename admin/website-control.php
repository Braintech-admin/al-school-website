<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
if (($_SESSION['admin_role'] ?? '') !== 'super') { header('Location: dashboard.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

 $err = '';

// ===== ONE-CLICK TOGGLE =====
if (isset($_GET['toggle'])) {
    try {
        $cur = (int) $pdo->query("SELECT site_status FROM settings WHERE id = 1")->fetchColumn();
        $new = $cur ? 0 : 1;
        $pdo->prepare("UPDATE settings SET site_status = ? WHERE id = 1")->execute([$new]);
        header('Location: website-control.php');
        exit;
    } catch (PDOException $ex) {
        $err = 'Database Error: ' . $ex->getMessage() . ' — Fix: phpMyAdmin me ye run karo → ALTER TABLE settings ADD COLUMN site_status TINYINT(1) DEFAULT 1;';
    }
}

try {
    $status = (int) $pdo->query("SELECT site_status FROM settings WHERE id = 1")->fetchColumn();
} catch (PDOException $ex) {
    $status = 1;
    $err = 'Database Error: ' . $ex->getMessage() . ' — Fix: phpMyAdmin me ye run karo → ALTER TABLE settings ADD COLUMN site_status TINYINT(1) DEFAULT 1;';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Website Control</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-power-off"></i> Website Control</h2>

        <?php if ($err): ?>
        <div class="alert-error"><i class="fas fa-exclamation-triangle"></i> <?= e($err) ?></div>
        <?php endif; ?>

        <div class="form-box" style="text-align:center; padding:50px 30px;">
            <div style="font-size:80px; color: <?= $status ? '#16a34a' : '#dc2626' ?>; margin-bottom:15px;">
                <i class="fas <?= $status ? 'fa-circle-check' : 'fa-circle-pause' ?>"></i>
            </div>
            <h2 style="color: <?= $status ? '#16a34a' : '#dc2626' ?>; font-size:26px; margin-bottom:8px;">
                Website <?= $status ? 'LIVE Hai' : 'OFF Hai' ?>
            </h2>
            <p style="color:#64748b; margin-bottom:30px; max-width:500px; margin-left:auto; margin-right:auto;">
                <?php if ($status): ?>
                    Website abhi public ke liye chal rahi hai. Ek click me band kar sakte ho — visitors ko hold message dikhega.
                <?php else: ?>
                    Website band hai — visitors (incognito/logout state me) ko hold message dikhega. Aap preview dekh sakte ho.
                <?php endif; ?>
            </p>

            <?php if ($status): ?>
            <a href="?toggle=1" onclick="return confirm('Website band karni hai? Saare visitors ko hold message dikhega.')"
               style="background:#dc2626; color:#fff; padding:16px 40px; font-size:17px; border-radius:10px; text-decoration:none; display:inline-block;">
                <i class="fas fa-power-off"></i> Website Band Karein
            </a>
            <?php else: ?>
            <a href="?toggle=1" onclick="return confirm('Website wapas chalu karni hai?')"
               style="background:#16a34a; color:#fff; padding:16px 40px; font-size:17px; border-radius:10px; text-decoration:none; display:inline-block;">
                <i class="fas fa-play"></i> Website Chalu Karein
            </a>
            <?php endif; ?>
        </div>

        <div class="form-box" style="border-left:4px solid #f97316;">
            <h3 style="font-size:15px;"><i class="fas fa-info-circle"></i> Kaise kaam karta hai?</h3>
            <p style="font-size:14px; color:#334155; line-height:1.8;">
                • <b>Website Band</b> karne par home, gallery, news — sab public pages band ho jate hain aur visitors ko hold message dikhta hai.<br>
                • <b>Admin panel aur login</b> band NAHI hote — aap aur school admin kaam karte rah sakte ho.<br>
                • Aap (Super Admin) band website ko <b>preview</b> kar sakte ho — asli test ke liye <b>Incognito window</b> use karo ya logout karke dekho.<br>
                • Setting <b>database me save</b> hoti hai — server restart ya cache clear hone par bhi bani rahegi.
            </p>
        </div>
    </div>
</body>
</html>