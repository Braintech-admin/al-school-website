<!-- ===== ADMISSIONS CTA (footer ke UPAR — apna alag section) ===== -->
<section class="cta-banner">
    <div class="container cta-inner">
        <div>
            <i class="fas fa-graduation-cap cta-icon"></i>
        </div>
        <div class="cta-text">
            <h2><?= e($settings['cta_title'] ?? 'Admissions Open for 2026-27') ?></h2>
            <p><?= e($settings['cta_text'] ?? ('Give your child the gift of quality education at ' . $settings['school_name'])) ?></p>
        </div>
        <a href="page.php?slug=admissions" class="btn btn-orange">Apply Now <i class="fas fa-arrow-right"></i></a>
    </div>
</section>
<!-- ===== CTA END (yahan section poora band hai) ===== -->

<!-- ===== FOOTER (alag element — CTA ke andar nahi) ===== -->
<footer class="footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <div class="logo-icon big">
                <?php if (!empty($settings['logo']) && file_exists($settings['logo'])): ?>
                    <img src="<?= e($settings['logo']) ?>" alt="School Logo">
                <?php else: ?>
                    <i class="fas fa-school"></i>
                <?php endif; ?>
            </div>
            <h3><?= e($settings['school_name']) ?></h3>
            <p class="tagline"><?= e($settings['tagline']) ?></p>
        </div>

        <div>
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="page.php?slug=about">About</a></li>
                <li><a href="page.php?slug=academics">Academics</a></li>
                <li><a href="page.php?slug=admissions">Admissions</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>

        <div>
            <h4>Contact Us</h4>
            <ul class="footer-contact">
                <li><i class="fas fa-map-marker-alt"></i> <?= e($settings['address']) ?></li>
                <li class="notranslate"><i class="fas fa-phone"></i> <?= e($settings['phone']) ?></li>
                <li class="notranslate"><i class="fas fa-envelope"></i> <?= e($settings['email']) ?></li>
                <li><i class="fas fa-clock"></i> <?= e($settings['timing']) ?></li>
            </ul>
        </div>

        <div>
            <h4>Follow Us</h4>
            <div>
    <h4>Follow Us</h4>
    <div class="social-icons">
        <a href="<?= e($settings['facebook']) ?>" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
        <a href="<?= e($settings['instagram']) ?>" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
        <a href="<?= e($settings['youtube']) ?>" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>
    </div>
</div>
        </div>
    </div>

        <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>&copy; <?= date('Y') ?> <?= e($settings['school_name']) ?>. All Rights Reserved.</p>
            <div class="footer-bottom-right">
                <a href="#">Privacy Policy</a> &nbsp;|&nbsp; <a href="#">Terms & Conditions</a>
            </div>
        </div>
        <div class="container footer-credit">
            <span>Designed with <i class="fas fa-heart"></i> for Education By</span>
            <a href="https://itsbraintech.com" target="_blank" rel="noopener" class="credit-brand">Braintech</a>
        </div>
    </div>
</footer>

<!-- Popup scripts waghera agar header me nahi hain -->
<script src="assets/js/main.js"></script>
</body>
</html>