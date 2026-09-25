<?php
require_once __DIR__ . '/functions.php';
 $settings = getSettings($pdo);
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
            <a href="#">Student Login</a> | <a href="#">Teacher Login</a> | <a href="admin"> Admin Login</a>
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
            <a href="index.php" class="<?= $current_page == 'index.php' ? 'active' : '' ?>">Home</a>
            <a href="page.php?slug=about" class="<?= $current_page == 'page.php' ? 'active' : '' ?>">About</a>
            <a href="page.php?slug=academics">Academics</a>
            <a href="page.php?slug=admissions">Admissions</a>
            <a href="page.php?slug=facilities">Facilities</a>
            <a href="gallery.php">Gallery</a>
            <a href="contact.php">Contact</a>
            <i class="fas fa-search"></i>
        </nav>
        <button class="hamburger" onclick="document.getElementById('navMenu').classList.toggle('open')">
            <i class="fas fa-bars"></i>
        </button>
    </div>
</header>