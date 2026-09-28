<?php
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }

// Live check — status + role fresh DB se
require_once '../config/db.php';
 $stmt = $pdo->prepare("SELECT status, role, name FROM admins WHERE id = ?");
 $stmt->execute([$_SESSION['admin_id']]);
 $me = $stmt->fetch();

if (!$me || $me['status'] == 0) {
    session_destroy();
    header('Location: index.php'); exit;
}

 $is_super   = ($me['role'] === 'super');
 $admin_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <?php if ($is_super): ?>
            Brain<span>Tech</span> Admin
            <small style="display:block; font-size:11px; font-weight:400; color:#f5b301; margin-top:3px;">
                ★ Super Admin
            </small>
        <?php else: ?>
            School <span>Admin</span>
            <small style="display:block; font-size:11px; font-weight:400; color:#94a3b8; margin-top:3px;">
                <?= htmlspecialchars($me['name'] ?: 'School Admin') ?>
            </small>
        <?php endif; ?>
    </div>

    <?php if ($is_super): ?>
        <div class="sidebar-divider">MANAGEMENT</div>
        <a href="notices.php" class="<?= $admin_page == 'notices.php' ? 'active' : '' ?>">
            <i class="fas fa-bullhorn"></i> Notices
        </a>
        <a href="school-admins.php" class="<?= $admin_page == 'school-admins.php' ? 'active' : '' ?>">
            <i class="fas fa-users-cog"></i> School Admins
        </a>
        <a href="website-control.php" class="<?= $admin_page == 'website-control.php' ? 'active' : '' ?>">
            <i class="fas fa-power-off"></i> Website Control
        </a>
    <?php else: ?>
        <a href="dashboard.php" class="<?= $admin_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-newspaper"></i> News & Events</a>
        <a href="hero.php" class="<?= $admin_page == 'hero.php' ? 'active' : '' ?>"><i class="fas fa-image"></i> Hero Banners</a>
        <a href="albums.php" class="<?= $admin_page == 'albums.php' ? 'active' : '' ?>"><i class="fas fa-folder-open"></i> Albums</a>
        <a href="gallery.php" class="<?= $admin_page == 'gallery.php' ? 'active' : '' ?>"><i class="fas fa-images"></i> Gallery</a>
        <a href="pages.php" class="<?= $admin_page == 'pages.php' ? 'active' : '' ?>"><i class="fas fa-file-alt"></i> Pages</a>
        <a href="messages.php" class="<?= $admin_page == 'messages.php' ? 'active' : '' ?>"><i class="fas fa-user-tie"></i> Messages</a>
        <a href="enquiries.php" class="<?= $admin_page == 'enquiries.php' ? 'active' : '' ?>"><i class="fas fa-inbox"></i> Enquiries</a>
         <a href="settings.php" class="<?= $admin_page == 'settings.php' ? 'active' : '' ?>"><i class="fas fa-cog"></i> Site Settings</a>
    <?php endif; ?>
    
    <a href="change-password.php" class="<?= $admin_page == 'change-password.php' ? 'active' : '' ?>">
        <i class="fas fa-key"></i> Change Password
    </a>
    <a href="../index.php" target="_blank"><i class="fas fa-globe"></i> View Website</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>

    <!-- BrainTech Footer Card (Logo) -->
    <a href="https://itsbraintech.com" target="_blank" rel="noopener" class="bt-sidebar-card">
        <?php if (file_exists(__DIR__ . '/../uploads/braintech-logo.svg')): ?>
            <img src="../uploads/braintech-logo.svg" alt="BrainTech IT Services" class="bt-card-logo">
        <?php else: ?>
            <span class="bt-card-fallback">Brain<b>tech</b></span>
        <?php endif; ?>
    </a>
</div>