<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== SAVE SETTINGS =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    // Logo upload (optional)
    $logo = !empty($_FILES['logo']['name']) ? uploadImage($_FILES['logo'], '../uploads/') : $_POST['current_logo'];

    $pdo->prepare("UPDATE settings SET
        school_name = ?, tagline = ?, logo = ?, address = ?, phone = ?, email = ?,
        timing = ?, facebook = ?, instagram = ?, youtube = ?,
        cta_title = ?, cta_text = ?
        WHERE id = 1")
        ->execute([
            trim($_POST['school_name']), trim($_POST['tagline']), $logo,
            trim($_POST['address']), trim($_POST['phone']), trim($_POST['email']),
            trim($_POST['timing']), trim($_POST['facebook']), trim($_POST['instagram']), trim($_POST['youtube']),
            trim($_POST['cta_title']), trim($_POST['cta_text'])
        ]);

    header('Location: settings.php?saved=1'); exit;
}

 $settings = $pdo->query("SELECT * FROM settings WHERE id = 1")->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Site Settings</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-cog"></i> Site Settings</h2>

        <?php if (isset($_GET['saved'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Settings save ho gayi! Website refresh karke dekho.</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="save_settings" value="1">
            <input type="hidden" name="current_logo" value="<?= e($settings['logo']) ?>">

            <!-- ===== SCHOOL INFO ===== -->
            <div class="form-box">
                <h3><i class="fas fa-school"></i> School Information</h3>
                <div class="form-row">
                    <div>
                        <label>School Name</label>
                        <input type="text" name="school_name" value="<?= e($settings['school_name']) ?>" required>
                    </div>
                    <div>
                        <label>Tagline</label>
                        <input type="text" name="tagline" value="<?= e($settings['tagline']) ?>">
                    </div>
                </div>

                <label>Logo (PNG recommended — khali chhodo to purani rahegi)</label>
                <input type="file" name="logo" accept="image/*">
                <?php if (!empty($settings['logo'])): ?>
                <div style="margin:10px 0;">
                    <img src="../<?= e($settings['logo']) ?>" style="width:70px; height:70px; object-fit:contain; border-radius:10px; border:2px solid #eee; background:#fff; padding:4px;" alt="Logo">
                </div>
                <?php endif; ?>

                <div class="form-row">
                    <div>
                        <label>Address</label>
                        <input type="text" name="address" value="<?= e($settings['address']) ?>">
                    </div>
                    <div>
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?= e($settings['phone']) ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" value="<?= e($settings['email']) ?>">
                    </div>
                    <div>
                        <label>School Timing</label>
                        <input type="text" name="timing" value="<?= e($settings['timing']) ?>">
                    </div>
                </div>
            </div>

            <!-- ===== SOCIAL LINKS ===== -->
            <div>
                <label><i class="fab fa-facebook" style="color:#1877f2;"></i>Facebook URL</label>
                <input type="text" name="facebook" value="<?= e($settings['facebook']) ?>"
                        placeholder="https://facebook.com/...">
            </div>
            <div>
                <label><i class="fab fa-instagram" style="color:#e4405f;"></i>Instagram URL</label>
                <input type="text" name="instagram" value="<?= e($settings['instagram']) ?>"
                        placeholder="https://instagram.com/...">
             </div>
             <div>
                <label><i class="fab fa-youtube" style="color:#ff0000;"></i>YouTube URL</label>
                <input type="text" name="youtube" value="<?= e($settings['youtube']) ?>"
                        placeholder="https://youtube.com/...">
            </div>

            <!-- ===== ADMISSIONS CTA (footer ke upar wala banner) ===== -->
            <div class="form-box">
                <h3><i class="fas fa-bullhorn"></i>Admissions CTA Banner (homepage footer ke upar)</h3>
                <div class="form-row">
                    <div>
                        <label>CTA Title</label>
                        <input type="text" name="cta_title" value="<?= e($settings['cta_title'] ?? 'Admissions Open for 2026-27') ?>">
                    </div>
                </div>
                <label>CTA Text</label>
                <textarea name="cta_text" rows="2"><?= e($settings['cta_text'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn btn-add" style="padding:14px 40px; font-size:15px;">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </form>

        <div class="form-box" style="border-left:4px solid #f97316; margin-top:20px;">
            <h3 style="font-size:14px;"><i class="fas fa-info-circle"></i> Ye settings kahan-kahan dikhti hain?</h3>
            <p style="font-size:13.5px; color:#334155; line-height:1.9;">
                • <b>School Name + Logo + Tagline</b> → Header, Footer, Login page, Maintenance page<br>
                • <b>Address / Phone / Email / Timing</b> → Topbar, Footer, Contact page<br>
                • <b>Social Links</b> → Footer ke "Follow Us" icons<br>
                • <b>CTA Banner</b> → Homepage pe footer ke upar admissions banner
            </p>
        </div>
    </div>
</body>
</html>