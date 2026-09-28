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

// Sirf About page ke liye mission/vision
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
            <?= $page['content'] ?>
        </div>
    </div>
</section>

<?php if ($slug === 'about'): ?>

<!-- ===== MISSION / VISION / VALUES ===== -->
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

<!-- ===== FACILITIES (About ke andar) ===== -->
<section class="section">
    <div class="container">
        <p class="section-tag center">OUR FACILITIES</p>
        <h2 class="section-title center">A Safe, Modern and Enriching Campus</h2>
        <div class="facilities-grid about-facilities">
            <?php foreach (getFacilities($pdo) as $f): ?>
            <div class="facility-item">
                <i class="fas <?= e($f['icon']) ?>"></i>
                <p><?= e($f['title']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php endif; ?>

<?php if ($slug === 'administration'): $all_msgs = getAllMessages($pdo); ?>

<!-- ===== ADMINISTRATION: ALL MESSAGES ===== -->
<section class="section bg-light">
    <div class="container">
        <?php if (empty($all_msgs)): ?>
        <p style="text-align:center;">Abhi koi message add nahi hua hai.</p>
        <?php endif; ?>
        <div class="messages-grid">
            <?php foreach ($all_msgs as $m): ?>
            <div class="principal-card">
                <img src="<?= e($m['photo']) ?>" alt="<?= e($m['designation']) ?>" class="principal-photo">
                <div class="principal-content">
                    <p class="section-tag"><?= strtoupper(e($m['designation'])) ?></p>
                    <h3><?= e($m['designation']) ?>'s Message</h3>
                    <p><?= nl2br(e($m['message'])) ?></p>
                    <p class="principal-name">— <?= e($m['name'] ?: $m['designation']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>