<!-- ===== ADMISSIONS CTA ===== -->
<section class="cta-banner">
    <div class="container cta-inner">
        <div>
            <i class="fas fa-graduation-cap cta-icon"></i>
        </div>
        <div class="cta-text">
            <h2>Admissions Open for 2026-27</h2>
            <p>Give your child the gift of quality education at <?= e($settings['school_name']) ?>.</p>
        </div>
        <a href="page.php?slug=admissions" class="btn btn-orange">Apply Now <i class="fas fa-arrow-right"></i></a>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <div class="logo-icon big">
    <img src="<?= e($settings['logo']) ?>" alt="School Logo">
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
                <li><i class="fas fa-phone"></i> <?= e($settings['phone']) ?></li>
                <li><i class="fas fa-envelope"></i> <?= e($settings['email']) ?></li>
                <li><i class="fas fa-clock"></i> <?= e($settings['timing']) ?></li>
            </ul>
        </div>
        <div>
            <h4>Follow Us</h4>
            <div class="social-icons">
                <a href="<?= e($settings['facebook']) ?>"><i class="fab fa-facebook-f"></i></a>
                <a href="<?= e($settings['instagram']) ?>"><i class="fab fa-instagram"></i></a>
                <a href="<?= e($settings['youtube']) ?>"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>&copy; <?= date('Y') ?> <?= e($settings['school_name']) ?>. All Rights Reserved.</p>
            <p><a href="#">Privacy Policy</a> &nbsp;|&nbsp; <a href="#">Terms & Conditions</a></p>
        </div>
    </div>
</footer>

<!-- ===== Google Translate: hidden widget + language control ===== -->
<div id="google_translate_element" style="display:none;"></div>

<style>
/* Google ke apne UI (banner/tooltip/gadget) ko poori tarah hide karo */
.goog-te-banner-frame,
iframe.goog-te-banner-frame,
#goog-gt-tt,
.goog-te-balloon-frame,
.goog-te-gadget-icon,
.goog-te-ftab { display: none !important; }
.goog-te-gadget { height: 0 !important; overflow: hidden !important; }
body { top: 0 !important; }
</style>

<script>
function googleTranslateElementInit() {
    new google.translate.TranslateElement({
        pageLanguage: 'en',
        includedLanguages: 'en,hi',
        autoDisplay: false
    }, 'google_translate_element');
}

// googtrans cookie set/delete (path=/ => poori site pe chalega)
function setGoogtrans(value) {
    if (value) {
        document.cookie = 'googtrans=' + value + '; path=/; max-age=31536000';
    } else {
        document.cookie = 'googtrans=; path=/; max-age=0';
        document.cookie = 'googtrans=; path=/; domain=' + location.hostname + '; max-age=0';
    }
}

// Dropdown se call hota hai
function changeLang(lang) {
    setGoogtrans(lang === 'en' ? '' : '/en/' + lang);
    location.reload();  // reload ke baad widget cookie padh ke khud translate karega
}

// Har page load par dropdown sync karo
document.addEventListener('DOMContentLoaded', function () {
    var m = document.cookie.match(/googtrans=\/en\/([a-z\-]+)/);
    document.getElementById('langSelect').value = m ? m[1] : 'en';
});
</script>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

<script src="assets/js/main.js"></script>

</body>
</html>