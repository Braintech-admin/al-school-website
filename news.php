<?php
require_once 'includes/header.php';

// ===== Detail view (ek specific news) =====
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ? AND status = 1");
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if (!$item) {
        echo '<section class="section"><div class="container" style="text-align:center;">
            <h1 style="font-family:\'Playfair Display\',serif; color:var(--navy);">News Not Found</h1>
            <a href="news.php" class="btn btn-orange" style="margin-top:15px;">Back to News</a></div></section>';
        require_once 'includes/footer.php';
        exit;
    }
    ?>

    <!-- Detail Banner -->
    <section class="page-banner" style="background: linear-gradient(rgba(15,42,92,.88), rgba(15,42,92,.88)), url('<?= e($item['image'] ?: 'uploads/hero.jpg') ?>') center/cover;">
        <div class="container">
            <h1>News & Events</h1>
            <p><a href="index.php">Home</a> &nbsp;/&nbsp; <a href="news.php">News</a> &nbsp;/&nbsp; Detail</p>
        </div>
    </section>

    <!-- Detail Content -->
    <section class="section">
        <div class="container">
            <div class="prose" style="max-width:850px; margin:0 auto;">
                <?php if ($item['image']): ?>
                <img src="<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" style="width:100%; border-radius:12px; margin-bottom:25px;">
                <?php endif; ?>
                <p class="section-tag"><?= date('d M Y', strtotime($item['event_date'])) ?></p>
                <h2 style="font-family:'Playfair Display',serif; color:var(--navy); margin:8px 0 15px;"><?= e($item['title']) ?></h2>
                <p><?= nl2br(e($item['description'])) ?></p>
                <a href="news.php" style="display:inline-block; margin-top:25px; color:var(--orange); font-weight:600;">
                    <i class="fas fa-arrow-left"></i> Back to all news
                </a>
            </div>
        </div>
    </section>

    <?php require_once 'includes/footer.php';
    exit;
}

// ===== List view (saari news) =====
 $all_news = $pdo->query("SELECT * FROM news WHERE status = 1 ORDER BY event_date DESC")->fetchAll();
?>

<!-- Page Banner -->
<section class="page-banner">
    <div class="container">
        <h1>News & Events</h1>
        <p><a href="index.php">Home</a> &nbsp;/&nbsp; News & Events</p>
    </div>
</section>

<!-- News List -->
<section class="section">
    <div class="container">
        <?php if (empty($all_news)): ?>
        <p style="text-align:center; color:#999;">Abhi koi news add nahi hui hai.</p>
        <?php endif; ?>

        <div class="news-list-grid">
            <?php foreach ($all_news as $n): ?>
            <div class="news-list-card">
                <?php if ($n['image']): ?>
                <div class="news-list-img">
                    <img src="<?= e($n['image']) ?>" alt="<?= e($n['title']) ?>">
                </div>
                <?php endif; ?>
                <div class="news-list-body">
                    <div class="news-date">
                        <span class="day"><?= date('d', strtotime($n['event_date'])) ?></span>
                        <span class="month"><?= date('M', strtotime($n['event_date'])) ?></span>
                    </div>
                    <h3><?= e($n['title']) ?></h3>
                    <p><?= e(mb_substr($n['description'], 0, 110)) ?><?= mb_strlen($n['description']) > 110 ? '...' : '' ?></p>
                    <a href="news.php?id=<?= $n['id'] ?>" class="news-readmore">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>