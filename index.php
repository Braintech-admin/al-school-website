<?php
require_once 'includes/header.php';

 $stats       = getStats($pdo);
 $about       = getAbout($pdo);
 $programs    = getPrograms($pdo);
 $facilities  = getFacilities($pdo);
 $principal   = getPrincipalMessage($pdo);
 $news        = getLatestNews($pdo, 3);
 $gallery     = getGallery($pdo, 6);
?>

<!-- ===== HERO SLIDER ===== -->
<?php $slides = $pdo->query("SELECT * FROM hero_slides WHERE status = 1 ORDER BY sort_order ASC")->fetchAll(); ?>

<section class="hero-slider">
    <?php if (empty($slides)): ?>
    <div class="hero-slide active" style="background: linear-gradient(rgba(10,25,60,.75), rgba(10,25,60,.75)), url('uploads/hero.jpg') center/cover;">
        <div class="container hero-content">
            <h2>Welcome to <?= e($settings['school_name']) ?></h2>
        </div>
    </div>
    <?php else: ?>
        <?php foreach ($slides as $i => $s): ?>
        <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>" style="background: linear-gradient(rgba(10,25,60,.75), rgba(10,25,60,.75)), url('<?= e($s['image']) ?>') center/cover;">
            <div class="container hero-content">
                <?php if ($s['small_text']): ?>
                <p class="hero-welcome"><?= e($s['small_text']) ?> <span class="line"></span></p>
                <?php endif; ?>
                <h2>
                    <?= e($s['title_line1']) ?>
                    <?php if ($s['title_line2']): ?><br><span class="accent"><?= e($s['title_line2']) ?></span><?php endif; ?>
                </h2>
                <?php if ($s['subtitle']): ?><p class="hero-sub"><?= e($s['subtitle']) ?></p><?php endif; ?>
                <div class="hero-btns">
                    <?php if ($s['btn1_text']): ?>
                    <a href="<?= e($s['btn1_link']) ?>" class="btn btn-orange"><?= e($s['btn1_text']) ?> <i class="fas fa-arrow-right"></i></a>
                    <?php endif; ?>
                    <?php if ($s['btn2_text']): ?>
                    <a href="<?= e($s['btn2_link']) ?>" class="btn btn-outline"><?= e($s['btn2_text']) ?> <i class="fas fa-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (count($slides) > 1): ?>
        <div class="hero-dots">
            <?php foreach ($slides as $i => $s): ?>
            <span class="<?= $i === 0 ? 'active' : '' ?>"></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<script>
const heroSlides = document.querySelectorAll('.hero-slide');
const heroDots = document.querySelectorAll('.hero-dots span');
let heroCur = 0, heroTimer = null;

function heroGo(n) {
    heroSlides[heroCur].classList.remove('active');
    heroDots[heroCur]?.classList.remove('active');
    heroCur = (n + heroSlides.length) % heroSlides.length;
    heroSlides[heroCur].classList.add('active');
    heroDots[heroCur]?.classList.add('active');
}
if (heroSlides.length > 1) {
    heroTimer = setInterval(() => heroGo(heroCur + 1), 6000);
    heroDots.forEach((d, i) => d.addEventListener('click', () => {
        clearInterval(heroTimer);
        heroGo(i);
        heroTimer = setInterval(() => heroGo(heroCur + 1), 6000);
    }));
}
</script>

<!-- ===== STATS BAR ===== -->
<section class="stats-bar">
    <div class="container stats-grid">
        <?php foreach ($stats as $stat): ?>
        <div class="stat-item">
            <i class="fas <?= e($stat['icon']) ?> stat-icon"></i>
            <div>
                <h3><?= e($stat['number']) ?></h3>
                <p><?= e($stat['label']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===== ABOUT SECTION ===== -->
<section class="about section">
    <div class="container about-grid">
        <div class="about-img">
            <img src="<?= e($about['image']) ?>" alt="About School">
        </div>
        <div class="about-content">
            <p class="section-tag">ABOUT OUR SCHOOL</p>
            <h2 class="section-title"><?= e($about['title']) ?></h2>
            <p class="about-text"><?= nl2br(e($about['description'])) ?></p>
            <a href="page.php?slug=about" class="btn btn-orange">Read More <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="about-features">
            <?php
            $features = [
                ['fa-book-open', 'Value Based Education', 'Knowledge with strong values'],
                ['fa-users', 'Holistic Development', 'Academic, co-curricular & life skills'],
                ['fa-heart', 'Safe & Supportive Environment', 'A second home for every child'],
                ['fa-star', 'Excellence in Education', 'Preparing future leaders']
            ];
            foreach ($features as $f): ?>
            <div class="feature-item">
                <i class="fas <?= $f[0] ?>"></i>
                <div>
                    <h4><?= $f[1] ?></h4>
                    <p><?= $f[2] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== ACADEMICS / PROGRAMS ===== -->
<section class="academics section bg-light">
    <div class="container">
        <p class="section-tag center">ACADEMICS</p>
        <h2 class="section-title center">Programs for a Brighter Tomorrow</h2>
        <p class="section-sub center">We offer a structured and enriching academic curriculum designed to bring out the best in every student.</p>
        
        <div class="programs-grid">
            <?php foreach ($programs as $p): ?>
            <div class="program-card">
                <i class="fas <?= e($p['icon']) ?> program-icon"></i>
                <h3><?= e($p['title']) ?></h3>
                <p class="program-classes"><?= e($p['classes']) ?></p>
                <p><?= e($p['description']) ?></p>
                <a href="page.php?slug=academics" class="arrow-link"><i class="fas fa-arrow-right"></i></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== FACILITIES ===== -->
<section class="facilities section">
    <div class="container">
        <p class="section-tag center">OUR FACILITIES</p>
        <h2 class="section-title center">A Safe, Modern and Enriching Campus</h2>
        
        <div class="facilities-grid">
            <?php foreach ($facilities as $f): ?>
            <div class="facility-item">
                <i class="fas <?= e($f['icon']) ?>"></i>
                <p><?= e($f['title']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== PRINCIPAL + NEWS ===== -->
<section class="principal-news section bg-light">
    <div class="container pn-grid">
        <!-- Principal -->
        <div class="principal-card">
            <img src="<?= e($principal['photo']) ?>" alt="Principal" class="principal-photo">
            <div class="principal-content">
                <p class="section-tag">OUR LEADER</p>
                <h3>Message from the Principal</h3>
                <p><?= nl2br(e($principal['message'])) ?></p>
                <p class="principal-name">— <?= e($principal['name']) ?></p>
                <a href="page.php?slug=about" class="btn btn-orange">Read Full Message <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>

        <!-- News & Events -->
        <div class="news-card">
            <div class="news-header">
                <h3>Latest News & Events</h3>
                <a href="page.php?slug=news" class="view-all">View All</a>
            </div>
            <?php foreach ($news as $n): ?>
            <div class="news-item">
                <div class="news-date">
                    <span class="day"><?= date('d', strtotime($n['event_date'])) ?></span>
                    <span class="month"><?= date('M', strtotime($n['event_date'])) ?></span>
                </div>
                <div class="news-info">
                    <h4><?= e($n['title']) ?></h4>
                    <p><?= e($n['description']) ?></p>
                </div>
                <i class="fas fa-chevron-right"></i>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== GALLERY ===== -->
<section class="gallery section">
    <div class="container">
        <p class="section-tag center">PHOTO GALLERY</p>
        <h2 class="section-title center">Moments That Inspire</h2>
        
        <div class="gallery-grid">
            <?php foreach ($gallery as $g): ?>
            <div class="gallery-item">
                <img src="<?= e($g['image']) ?>" alt="<?= e($g['title']) ?>">
                <div class="gallery-overlay"><p><?= e($g['title']) ?></p></div>
            </div>
            <?php endforeach; ?>
            <div style="text-align:center; margin-top:35px;">
    <a href="gallery.php" class="btn btn-orange">View Full Gallery <i class="fas fa-arrow-right"></i></a>
</div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>