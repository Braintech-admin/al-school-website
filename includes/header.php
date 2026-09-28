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
require_once __DIR__ . '/functions.php';
 $settings = getSettings($pdo);
require_once __DIR__ . '/site-check.php';
 $current_page = basename($_SERVER['PHP_SELF']);
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($settings['school_name']) ?></title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ===== TOP BAR ===== -->
<div class="topbar">
    <div class="container topbar-inner">
        <div class="topbar-left">
            <span><i class="fas fa-map-marker-alt"></i> <?= e($settings['address']) ?></span>
            <span class="notranslate"><i class="fas fa-phone"></i> <?= e($settings['phone']) ?></span>
<span class="notranslate"><i class="fas fa-envelope"></i> <?= e($settings['email']) ?></span>
        </div>
        <div class="topbar-right">
            <a href="admin"> Admin Login</a>
            <div class="lang-switch">
                <i class="fas fa-language"></i>
                <select id="langSelect" onchange="changeLang(this.value)">
                    <option value="en">English</option>
                    <option value="hi">हिंदी</option>
                </select>
            </div>
            
        </div>
    </div>
</div>

<!-- ===== HEADER / NAVBAR ===== -->
<header class="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="logo">
            <div class="logo-icon">
    <?php if (!empty($settings['logo']) && file_exists($settings['logo'])): ?>
        <img src="<?= e($settings['logo']) ?>" alt="School Logo">
    <?php else: ?>
        <i class="fas fa-school"></i>
    <?php endif; ?>
</div>
            <div>
                <h1 class="notranslate"><?= e($settings['school_name']) ?></h1>
                <p class="notranslate tagline"><?= e($settings['tagline']) ?></p>
            </div>
        </a>
        <nav class="nav-menu" id="navMenu">
            <a href="index.php" class="<?= navActive('index.php') ?>">Home</a>
            <a href="page.php?slug=about" class="<?= navActive('page.php', 'about') ?>">About</a>
            <a href="page.php?slug=academics" class="<?= navActive('page.php', 'academics') ?>">Academics</a>
            <a href="page.php?slug=admissions" class="<?= navActive('page.php', 'admissions') ?>">Admissions</a>
            <a href="page.php?slug=administration" class="<?= navActive('page.php', 'administration') ?>">Administration</a>
            <a href="gallery.php" class="<?= navActive('gallery.php') ?>">Gallery</a>
            <a href="contact.php" class="<?= navActive('contact.php') ?>">Contact</a>
            <i class="fas fa-search"></i>
        </nav>
        <button class="hamburger" onclick="document.getElementById('navMenu').classList.toggle('open')">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>
<?php
// ===== POPUP NOTICE BOARD (sirf homepage pe, session me ek baar) =====
 $is_home = basename($_SERVER['PHP_SELF']) === 'index.php';
if ($is_home):
    $popup = getPopupNews($pdo);
    if ($popup):
        // Session: ek session me ek hi baar dikhe (har page pe irritate na kare)
        $popup_key = 'popup_seen_' . $popup['id'];
        $show_popup = !isset($_SESSION[$popup_key]);
        $_SESSION[$popup_key] = true;
?>
<?php if ($show_popup): ?>
<!-- POPUP OVERLAY -->
<div class="popup-overlay" id="popupOverlay">
    <div class="popup-box">
        <button class="popup-close" onclick="closePopup()" aria-label="Close">&times;</button>

        <?php if (!empty($popup['image'])): ?>
        <div class="popup-img">
            <img src="<?= e($popup['image']) ?>" alt="<?= e($popup['title']) ?>">
        </div>
        <?php endif; ?>

        <div class="popup-body">
            <div class="popup-badge">
                <i class="fas fa-bullhorn"></i> Notice Board
            </div>
            <div class="popup-date">
                <i class="fas fa-calendar"></i> <?= date('d M Y', strtotime($popup['event_date'])) ?>
            </div>
            <h3 class="popup-title"><?= e($popup['title']) ?></h3>
            <p class="popup-text"><?= nl2br(e($popup['description'])) ?></p>
            <div class="popup-btns">
                <a href="news.php?id=<?= (int)$popup['id'] ?>" class="popup-btn-main">
                    Puri Jankari Dekhein <i class="fas fa-arrow-right"></i>
                </a>
                <button class="popup-btn-skip" onclick="closePopup()">Baad Mein</button>
            </div>
        </div>
    </div>
</div>

<script>
function closePopup() {
    document.getElementById('popupOverlay').classList.add('closing');
    setTimeout(() => document.getElementById('popupOverlay').remove(), 300);
}
// Bahar click → band
document.getElementById('popupOverlay').addEventListener('click', function(e) {
    if (e.target === this) closePopup();
});
// ESC → band
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closePopup();
});
</script>
<?php endif; ?>
<?php endif; endif; ?>