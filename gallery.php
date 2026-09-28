<?php
require_once 'includes/header.php';

// ===== ALBUM DETAIL VIEW =====
if (isset($_GET['album'])) {
    $album_id = (int)$_GET['album'];
    $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ? AND status = 1");
    $stmt->execute([$album_id]);
    $album = $stmt->fetch();

    if (!$album) {
        echo '<section class="section"><div class="container" style="text-align:center;">
            <h1 style="color:var(--navy);">Album Not Found</h1>
            <a href="gallery.php" class="btn btn-orange" style="margin-top:15px;">Back to Gallery</a></div></section>';
        require_once 'includes/footer.php'; exit;
    }

    $photos = getAlbumPhotos($pdo, $album_id);
    $cover = $album['cover_image'] ?: ($photos[0]['image'] ?? 'uploads/hero.jpg');
    ?>

    <section class="page-banner" style="background: linear-gradient(rgba(15,42,92,.88), rgba(15,42,92,.88)), url('<?= e($cover) ?>') center/cover;">
        <div class="container">
            <h1><?= e($album['name']) ?></h1>
            <p><a href="index.php">Home</a> / <a href="gallery.php">Gallery</a> / <?= e($album['name']) ?></p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <a href="gallery.php" style="display:inline-block; margin-bottom:20px; color:var(--orange); font-weight:600;">
                <i class="fas fa-arrow-left"></i> Sabhi Albums
            </a>

            <?php if (empty($photos)): ?>
            <p style="text-align:center; color:#999;">Is album me abhi photos nahi hain.</p>
            <?php else: ?>
            <div class="gallery-grid full">
                <?php foreach ($photos as $g): ?>
                <div class="gallery-item">
                    <img src="<?= e($g['image']) ?>" alt="<?= e($g['title']) ?>">
                    <div class="gallery-overlay"><p><?= e($g['title']) ?></p></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="lightbox" id="lightbox">
        <span class="close" onclick="closeLb()">&times;</span>
        <span class="nav prev" onclick="move(-1)"><i class="fas fa-chevron-left"></i></span>
        <span class="nav next" onclick="move(1)"><i class="fas fa-chevron-right"></i></span>
        <img id="lbImg" src="" alt="">
        <p class="caption" id="lbCaption"></p>
    </div>

    <script>
    const items = [...document.querySelectorAll('.gallery-item')];
    let current = 0;
    items.forEach((item, i) => item.addEventListener('click', () => show(i)));
    function show(i) {
        current = i;
        const img = items[i].querySelector('img');
        document.getElementById('lbImg').src = img.src;
        document.getElementById('lbCaption').textContent = img.alt;
        document.getElementById('lightbox').classList.add('active');
    }
    function move(step) { show((current + step + items.length) % items.length); }
    function closeLb() { document.getElementById('lightbox').classList.remove('active'); }
    document.addEventListener('keydown', e => {
        if (!document.getElementById('lightbox').classList.contains('active')) return;
        if (e.key === 'Escape') closeLb();
        if (e.key === 'ArrowRight') move(1);
        if (e.key === 'ArrowLeft') move(-1);
    });
    </script>

    <?php require_once 'includes/footer.php'; exit;
}

// ===== ALBUMS LIST VIEW (default) =====
 $albums = getAlbums($pdo);
?>

<section class="page-banner">
    <div class="container">
        <h1>Photo Gallery</h1>
        <p><a href="index.php">Home</a> &nbsp;/&nbsp; Gallery</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (empty($albums)): ?>
        <p style="text-align:center; color:#999;">Abhi koi album add nahi hua hai.</p>
        <?php endif; ?>

        <div class="albums-grid">
            <?php foreach ($albums as $a): ?>
            <a href="gallery.php?album=<?= $a['id'] ?>" class="album-card">
                <div class="album-cover">
                    <img src="<?= e($a['display_image'] ?: 'uploads/hero.jpg') ?>" alt="<?= e($a['name']) ?>">
                    <div class="album-count"><i class="fas fa-camera"></i> <?= (int)$a['photo_count'] ?> Photos</div>
                </div>
                <div class="album-name">
                    <h3><?= e($a['name']) ?></h3>
                    <span>View Album <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>