<?php
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
 $admin_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand">School <span>Admin</span></div>
        <a href="hero.php" class="<?= $admin_page == 'hero.php' ? 'active' : '' ?>">
            <i class="fas fa-image"></i> Hero Banners
        </a>
        <a href="dashboard.php" class="<?= $admin_page == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-newspaper"></i> News & Events
        </a>
        <a href="gallery.php" class="<?= $admin_page == 'gallery.php' ? 'active' : '' ?>">
            <i class="fas fa-images"></i> Gallery
        </a>
        <a href="pages.php" class="<?= $admin_page == 'pages.php' ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> Pages
        </a>
        <a href="../index.php" target="_blank"><i class="fas fa-globe">
            </i> View Website</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt">
            </i> Logout</a>
</div>