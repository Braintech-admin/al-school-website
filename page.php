<?php
require_once 'includes/header.php';

 $slug = $_GET['slug'] ?? 'about';

// Slug safe rakho (sirf a-z, 0-9, hyphen allow)
if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
    $slug = 'about';
}

 $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ? LIMIT 1");
 $stmt->execute([$slug]);
 $page = $stmt->fetch();

// Page na mile to 404
if (!$page) {
    echo '<section class="section"><div class="container" style="text-align:center;">
        <h1 style="font-family:\'Playfair Display\',serif; color:var(--navy); font-size:60px;">404</h1>
        <p style="margin-bottom:20px;">Sorry, ye page exist nahi karta.</p>
        <a href="index.php" class="btn btn-orange">Back to Home</a></div></section>';
    require_once 'includes/footer.php';
    exit;
}

// Sirf About page ke liye extra sections
 $mission_items = ($slug === 'about')
    ? $pdo->query("SELECT * FROM mission_vision ORDER BY sort_order ASC")->fetchAll()
    : [];
?>

<!-- ===== PAGE BANNER ===== -->
<section class="page-banner" style="background: linear-gradient(rgba(15,42,92,.88), rgba(15,42,92,.88)), url('<?= e($page['banner_image']) ?>') center/cover;">
    <div class="container">
        <h1><?= e($page['page_title']) ?></h1>
        <p><a href="index.php">Home</a> &nbsp;/&nbsp; <?= e($page['page_title']) ?></p>
    </div>
</section>

<?php if ($page['page_subtitle']): ?>
<section class="section" style="padding-bottom:0;">
    <div class="container">
        <p class="section-tag"><?= e($page['page_subtitle']) ?></p>
    </div>
</section>
<?php endif; ?>

<!-- ===== MAIN CONTENT ===== -->
<section class="section">
    <div class="container">
        <div class="prose">
            <?= $page['content'] // Admin ka apna content hai, HTML allowed ?>
        </div>
    </div>
</section>

<?php if ($slug === 'about'): ?>

<!-- ===== MISSION / VISION / VALUES (sirf About pe) ===== -->
<section class="section bg-light">
    <div class="container">
        <p class="section-tag center">WHAT WE STAND FOR</p>
        <h2 class="section-title center">Our Mission, Vision & Values</h2>
        <div class="mv-grid">
            <?php foreach ($mission_items as $mv): ?>
            <div class="mv-card">
                <i class="fas <?= e($mv['icon']) ?> mv-icon"></i>
                <h3><?= e($mv['title']) ?></h3>
                <p><?= e($mv['description']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== PRINCIPAL MESSAGE FULL (sirf About pe) ===== -->
<section class="section">
    <div class="container">
        <p class="section-tag center">OUR LEADER</p>
        <h2 class="section-title center">Message from the Principal</h2>
        <?php $principal = getPrincipalMessage($pdo); ?>
        <div class="principal-card" style="max-width:900px; margin:0 auto;">
            <img src="<?= e($principal['photo']) ?>" alt="Principal" class="principal-photo">
            <div class="principal-content">
                <p><?= nl2br(e($principal['message'])) ?></p>
                <p class="principal-name">— <?= e($principal['name']) ?>, <?= e($settings['school_name']) ?></p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>