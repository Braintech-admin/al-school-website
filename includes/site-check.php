<?php
// Website OFF hai to maintenance page dikhao (Super Admin preview kar sakta hai)
if (isset($settings['site_status']) && $settings['site_status'] == 0) {

    if (session_status() === PHP_SESSION_NONE) session_start();
    $is_super = (($_SESSION['admin_role'] ?? '') === 'super');

    if (!$is_super) {
        ?><!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Website On Hold</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Poppins',sans-serif; background: linear-gradient(135deg, #0a1f47, #0f2a5c); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
.card { background:#fff; border-radius:16px; max-width:560px; width:100%; padding:50px 40px; text-align:center; border-top:6px solid #f97316; }
.logo { width:90px; height:90px; border-radius:50%; object-fit:contain; border:3px solid #0f2a5c; padding:4px; }
.icon { font-size:56px; color:#f97316; margin:20px 0 10px; }
h1 { color:#0f2a5c; font-size:22px; margin-bottom:18px; }
.msg { background:#fff7ed; border:1px solid #fed7aa; color:#9a3412; padding:18px 20px; border-radius:10px; font-size:16px; line-height:1.8; margin-bottom:20px; }
.contact { color:#64748b; font-size:14px; }
.contact i { color:#f97316; margin:0 5px; }
</style>
</head>
<body>
    <div class="card">
        <?php if (!empty($settings['logo']) && file_exists($settings['logo'])): ?>
            <img src="<?= htmlspecialchars($settings['logo']) ?>" alt="Logo" class="logo">
        <?php endif; ?>
        <div class="icon"><i class="fas fa-pause-circle"></i></div>
        <h1><?= htmlspecialchars($settings['school_name']) ?></h1>
        <div class="msg">
            आपकी website आंतरिक कारणों से <b>HOLD</b> की गई है।<br>
            अधिक जानकारी के लिए अपने <b>Web Admin</b> से संपर्क करें।
        </div>
        <p class="contact">
            <i class="fas fa-phone"></i> <?= htmlspecialchars($settings['phone']) ?> &nbsp;&nbsp;
            <i class="fas fa-envelope"></i> <?= htmlspecialchars($settings['email']) ?>
        </p>
    </div>
</body>
</html>
        <?php
        exit;
    }
}