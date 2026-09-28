<?php
// ===== Admin Topbar (Braintech Branding) =====
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
if (!isset($pdo)) require_once '../config/db.php';
 $school = $pdo->query("SELECT school_name FROM settings LIMIT 1")->fetch();
?>
<div class="admin-topbar">
    <div class="atb-left">
        <a href="dashboard.php" class="atb-brand">
            <img src="../uploads/braintech-logo.svg" alt="BrainTech" class="atb-brand-img">
        </a>
        <span class="atb-sep"></span>
        <span class="atb-panel-name"><i class="fas fa-shield-halved"></i>Admin Panel</span>
    </div>
    <div class="atb-right">
        <span class="atb-school-name" title="<?= htmlspecialchars($school['school_name'] ?? '') ?>">
            <h4><i class="fas fa-school"></i><?= htmlspecialchars(mb_strimwidth($school['school_name'] ?? 'School', 0, 50, '...')) ?></h4>
        </span>
        <a href="../index.php" target="_blank" class="atb-btn"><i class="fas fa-globe"></i><span> Website</span></a>
        <a href="logout.php" class="atb-btn atb-danger"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a>
    </div>
</div>