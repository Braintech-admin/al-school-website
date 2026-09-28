<?php
// ===== Admin Topbar (Braintech) — self-contained =====
if (!isset($pdo)) require_once __DIR__ . '/../config/db.php';
 $school = $pdo->query("SELECT school_name FROM settings LIMIT 1")->fetch();
?>
<style>
.admin-topbar{position:fixed;top:0;left:0;right:0;height:54px;background:#0a1f47;display:flex;align-items:center;justify-content:space-between;padding:0 20px;z-index:2000;box-shadow:0 2px 12px rgba(0,0,0,.35);}
.atb-left{display:flex;align-items:center;gap:14px;}
.atb-brand{display:flex;align-items:center;text-decoration:none;}
.atb-brand-img{height:38px;width:auto;display:block;}
.atb-sep{width:1px;height:24px;background:rgba(255,255,255,.18);}
.atb-panel-name{color:#94a3b8;font-size:12.5px;display:flex;align-items:center;gap:6px;}
.atb-panel-name i{color:#f97316;font-size:12px;}
.atb-right{display:flex;align-items:center;gap:12px;}
.atb-school-name{color:#94a3b8;font-size:13px;max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.atb-school-name i{color:#f97316;margin-right:4px;}
.atb-btn{color:#cbd5e1;text-decoration:none;font-size:13px;padding:7px 14px;border-radius:6px;background:rgba(255,255,255,.08);transition:.2s;white-space:nowrap;display:inline-flex;align-items:center;gap:6px;}
.atb-btn:hover{background:#f97316;color:#fff;}
.atb-danger:hover{background:#dc2626;}
@media(max-width:768px){.atb-school-name,.atb-panel-name,.atb-sep{display:none;}.atb-btn span{display:none;}}
</style>
<div class="admin-topbar">
    <div class="atb-left">
        <a href="dashboard.php" class="atb-brand">
            <?php if (file_exists(__DIR__ . '/../uploads/braintech-logo.svg')): ?>
                <img src="../uploads/braintech-logo.svg" alt="BrainTech" class="atb-brand-img">
            <?php else: ?>
                <span style="font-weight:800;font-size:17px;color:#fff;">Brain<b style="color:#f97316;">tech</b></span>
            <?php endif; ?>
        </a>
        <span class="atb-sep"></span>
        <span class="atb-panel-name"><i class="fas fa-shield-halved"></i> Admin Panel</span>
    </div>
    <div class="atb-right">
        <span class="atb-school-name" title="<?= htmlspecialchars($school['school_name'] ?? '') ?>">
            <i class="fas fa-school"></i> <?= htmlspecialchars(mb_strimwidth($school['school_name'] ?? 'School', 0, 32, '...')) ?>
        </span>
        <a href="../index.php" target="_blank" class="atb-btn"><i class="fas fa-globe"></i><span> Website</span></a>
        <a href="logout.php" class="atb-btn atb-danger"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a>
    </div>
</div>