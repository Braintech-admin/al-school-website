<?php
require_once 'includes/header.php';

 $success = $error = '';

// ===== FORM SUBMIT =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Honeypot — spam bots isse bharte hain, insaan nahi
    if (!empty($_POST['website'])) {
        // Bot hai — success dikhao par kuch save na karo
        $success = "Message bhej diya gaya hai!";
    } else {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validation
        if ($name === '' || $message === '') {
            $error = "Naam aur Message bharna zaroori hai.";
        } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Email address sahi nahi hai.";
        } elseif (mb_strlen($message) > 3000) {
            $error = "Message bahut lamba hai (max 3000 characters).";
        } else {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            $success = "Dhanyavaad {$name}! Aapka message receive ho gaya hai. Hum jaldi sampark karenge.";
        }
    }
}
?>

<!-- ===== PAGE BANNER ===== -->
<section class="page-banner">
    <div class="container">
        <h1>Contact Us</h1>
        <p><a href="index.php">Home</a> &nbsp;/&nbsp; Contact</p>
    </div>
</section>

<!-- ===== CONTACT INFO CARDS ===== -->
<section class="section" style="padding-bottom:20px;">
    <div class="container">
        <div class="contact-info-grid">
            <div class="contact-info-card">
                <i class="fas fa-map-marker-alt"></i>
                <h4>Address</h4>
                <p><?= e($settings['address']) ?></p>
            </div>
            <div class="contact-info-card notranslate">
                <i class="fas fa-phone"></i>
                <h4>Phone</h4>
                <p><?= e($settings['phone']) ?></p>
            </div>
            <div class="contact-info-card notranslate">
                <i class="fas fa-envelope"></i>
                <h4>Email</h4>
                <p><?= e($settings['email']) ?></p>
            </div>
            <div class="contact-info-card">
                <i class="fas fa-clock"></i>
                <h4>School Timing</h4>
                <p><?= e($settings['timing']) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- ===== FORM + MAP ===== -->
<section class="section">
    <div class="container contact-main-grid">

        <!-- Form -->
        <div class="contact-form-box">
            <h2>Send Us a Message</h2>
            <p class="contact-form-sub">Koi sawal ya jankari chahiye? Neeche form bharein — hum 24 ghante me jawab denge.</p>

            <?php if ($success): ?>
            <div class="alert-success" style="border-radius:8px; padding:14px 18px; background:#dcfce7; color:#16a34a; margin-bottom:15px;">
                <i class="fas fa-check-circle"></i> <?= e($success) ?>
            </div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="alert-error" style="border-radius:8px; padding:14px 18px; background:#fee2e2; color:#dc2626; margin-bottom:15px;">
                <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <!-- Honeypot (invisible) -->
                <input type="text" name="website" style="display:none;" tabindex="-1" autocomplete="off">

                <div class="form-row">
                    <div>
                        <label>Aapka Naam *</label>
                        <input type="text" name="name" required maxlength="100">
                    </div>
                    <div>
                        <label>Phone Number</label>
                        <input type="tel" name="phone" maxlength="20">
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Email</label>
                        <input type="email" name="email" maxlength="150">
                    </div>
                    <div>
                        <label>Subject</label>
                        <input type="text" name="subject" maxlength="200" placeholder="Admission jankari / Any query...">
                    </div>
                </div>
                <label>Message *</label>
                <textarea name="message" rows="6" required maxlength="3000"></textarea>
                <button type="submit" class="btn btn-orange">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>

        <!-- Map -->
        <div class="contact-map-box">
            <?php if (!empty($settings['map_embed'])): ?>
            <iframe src="<?= e($settings['map_embed']) ?>" style="width:100%; height:100%; min-height:450px; border:0; border-radius:12px;" allowfullscreen loading="lazy"></iframe>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php require_once 'includes/footer.php'; ?>