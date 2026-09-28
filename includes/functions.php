<?php
/**
 * A.L. Convent School & Girls' College Website
 *
 * Copyright © 2026 Braintech IT Services.
 * All rights reserved.
 *
 * This source code is proprietary.
 * Unauthorized copying, reproduction, modification,
 * redistribution or commercial use is prohibited
 * without prior written permission.
 */
require_once __DIR__ . '/../config/db.php';

// Site settings fetch (cached)
function getSettings($pdo) {
    static $settings = null;
    if ($settings === null) {
        $settings = $pdo->query("SELECT * FROM settings LIMIT 1")->fetch();
    }
    return $settings;
}

// Generic fetch functions
function getStats($pdo) {
    return $pdo->query("SELECT * FROM stats ORDER BY sort_order ASC")->fetchAll();
}

function getAbout($pdo) {
    return $pdo->query("SELECT * FROM about LIMIT 1")->fetch();
}

function getPrograms($pdo) {
    return $pdo->query("SELECT * FROM programs WHERE status = 1 ORDER BY sort_order ASC")->fetchAll();
}

function getFacilities($pdo) {
    return $pdo->query("SELECT * FROM facilities ORDER BY sort_order ASC")->fetchAll();
}

function getLatestNews($pdo, $limit = 3) {
    $limit = (int) $limit; // integer me cast karo
    $stmt = $pdo->query("SELECT * FROM news WHERE status = 1 ORDER BY event_date ASC LIMIT $limit");
    return $stmt->fetchAll();
}

function getGallery($pdo, $limit = 6) {
    $limit = (int) $limit; // integer me cast karo
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC LIMIT $limit");
    return $stmt->fetchAll();
}

// Date format: "15 Sep"
function formatDate($date) {
    return date('d', strtotime($date)) . '<br>' . date('M', strtotime($date));
}

// Security: XSS protection
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Image upload helper
function uploadImage($file, $folder = 'uploads/') {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) return null;
    
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) return null;
    if ($file['size'] > 5 * 1024 * 1024) return null; // 5MB limit
    
    $filename = time() . '_' . uniqid() . '.' . $ext;
    
    if (!is_dir($folder)) mkdir($folder, 0755, true);
    move_uploaded_file($file['tmp_name'], $folder . $filename);
    
    return 'uploads/' . $filename;
}
// ===== Admin ke liye friendly link options =====
function getLinkOptions($pdo) {
    $options = ['index.php' => 'Home Page'];
    
    // Pages table ke saare dynamic pages
    $pages = $pdo->query("SELECT slug, page_title FROM pages ORDER BY id ASC")->fetchAll();
    foreach ($pages as $p) {
        $options['page.php?slug=' . $p['slug']] = $p['page_title'];
    }
    
    // Fixed pages
    $options['gallery.php'] = 'Photo Gallery';
    $options['contact.php'] = 'Contact Page';
    
    return $options;
}

// ===== Dropdown render karne ka helper =====
function linkDropdown($pdo, $name, $selected = '') {
    $options = getLinkOptions($pdo);
    
    // Purana custom link ho to kho na jaye — Custom option dikhao
    if ($selected && !isset($options[$selected])) {
        $options[$selected] = 'Custom: ' . $selected;
    }
    
    echo "<select name=\"$name\">";
    foreach ($options as $value => $label) {
        $sel = ($value === $selected) ? 'selected' : '';
        echo "<option value=\"" . htmlspecialchars($value) . "\" $sel>" . htmlspecialchars($label) . "</option>";
    }
    echo "</select>";
}
// Homepage ke liye — sirf jo admin ne select kiye
function getHomeMessages($pdo) {
    return $pdo->query("SELECT * FROM messages WHERE status = 1 AND show_on_home = 1 ORDER BY sort_order ASC")->fetchAll();
}

// Administration page ke liye — SAARE active messages
function getAllMessages($pdo) {
    return $pdo->query("SELECT * FROM messages WHERE status = 1 ORDER BY sort_order ASC")->fetchAll();
}
// Nav active state helper
function navActive($file, $slug = null) {
    if (basename($_SERVER['PHP_SELF']) !== $file) return '';
    if ($slug !== null && ($_GET['slug'] ?? '') !== $slug) return '';
    return 'active';
}
// Albums with cover (cover khali ho to pehli photo cover ban jayegi) + photo count
function getAlbums($pdo) {
    return $pdo->query("
        SELECT a.*,
            COALESCE(NULLIF(a.cover_image,''),
                (SELECT g.image FROM gallery g WHERE g.album_id = a.id ORDER BY g.sort_order ASC, g.id ASC LIMIT 1)
            ) AS display_image,
            (SELECT COUNT(*) FROM gallery g WHERE g.album_id = a.id) AS photo_count
        FROM albums a
        WHERE a.status = 1
        ORDER BY a.sort_order ASC
    ")->fetchAll();
}

function getAlbumPhotos($pdo, $album_id) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE album_id = ? ORDER BY sort_order ASC, id ASC");
    $stmt->execute([(int)$album_id]);
    return $stmt->fetchAll();
}
// ===== SMTP Email sender (PHPMailer) =====
function sendMail($to, $subject, $body) {
    require_once __DIR__ . '/../config/mail.php';
    require_once __DIR__ . '/../phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../phpmailer/src/SMTP.php';
    require_once __DIR__ . '/../phpmailer/src/Exception.php';

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log('Mail Error: ' . $mail->ErrorInfo);   // debug log
        return false;
    }
}
// Latest popup-worthy news (website open hone par modal me dikhegi)
function getPopupNews($pdo) {
    return $pdo->query("SELECT * FROM news WHERE status = 1 AND show_popup = 1 ORDER BY event_date DESC, id DESC LIMIT 1")->fetch();
}