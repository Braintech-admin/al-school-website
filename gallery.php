<?php
require_once 'includes/header.php';

 $all_gallery = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC")->fetchAll();
?>

<!-- ===== PAGE BANNER ===== -->
<section class="page-banner">
    <div class="container">
        <h1>Photo Gallery</h1>
        <p><a href="index.php">Home</a> &nbsp;/&nbsp; Gallery</p>
    </div>
</section>

<!-- ===== GALLERY GRID ===== -->
<section class="section">
    <div class="container">
        <?php if (!empty($all_gallery)): ?>
        <div class="gallery-grid full">
            <?php foreach ($all_gallery as $g): ?>
            <div class="gallery-item">
                <img src="<?= e($g['image']) ?>" alt="<?= e($g['title']) ?>">
                <div class="gallery-overlay"><p><?= e($g['title']) ?></p></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="text-align:center; color:#999;">Abhi koi photo add nahi hui hai.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ===== LIGHTBOX ===== -->
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
function move(step) {
    show((current + step + items.length) % items.length);
}
function closeLb() {
    document.getElementById('lightbox').classList.remove('active');
}

// Keyboard: Esc = close, Arrow keys = next/prev
document.addEventListener('keydown', e => {
    if (!document.getElementById('lightbox').classList.contains('active')) return;
    if (e.key === 'Escape') closeLb();
    if (e.key === 'ArrowRight') move(1);
    if (e.key === 'ArrowLeft') move(-1);
});
</script>

<?php require_once 'includes/footer.php'; ?>