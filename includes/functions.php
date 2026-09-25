<?php
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

function getPrincipalMessage($pdo) {
    return $pdo->query("SELECT * FROM principal_message LIMIT 1")->fetch();
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