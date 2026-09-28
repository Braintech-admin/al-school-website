<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== ADD SLIDE =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slide'])) {
    $image = uploadImage($_FILES['image'], '../uploads/');
    if ($image) {
        $pdo->prepare("INSERT INTO hero_slides (small_text, title_line1, title_line2, subtitle, btn1_text, btn1_link, btn2_text, btn2_link, image, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute([$_POST['small_text'], $_POST['title_line1'], $_POST['title_line2'], $_POST['subtitle'],
                       $_POST['btn1_text'], $_POST['btn1_link'], $_POST['btn2_text'], $_POST['btn2_link'],
                       $image, (int)$_POST['sort_order']]);
        header('Location: hero.php?added=1');   // redirect — refresh pe duplicate nahi banega
        exit;
    }
    $error = "Image upload failed! (jpg/png/webp, max 5MB)";
}

// ===== UPDATE SLIDE =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_slide'])) {
    $new_image = !empty($_FILES['image']['name']) ? uploadImage($_FILES['image'], '../uploads/') : null;

    // Nayi image aayi to purani file disk se delete karo (space waste na ho)
    if ($new_image && !empty($_POST['current_image'])) {
        $old = '../' . $_POST['current_image'];
        if (file_exists($old)) unlink($old);
    }

    $pdo->prepare("UPDATE hero_slides SET small_text=?, title_line1=?, title_line2=?, subtitle=?, btn1_text=?, btn1_link=?, btn2_text=?, btn2_link=?, image=?, sort_order=?, status=? WHERE id=?")
        ->execute([$_POST['small_text'], $_POST['title_line1'], $_POST['title_line2'], $_POST['subtitle'],
                   $_POST['btn1_text'], $_POST['btn1_link'], $_POST['btn2_text'], $_POST['btn2_link'],
                   $new_image ?: $_POST['current_image'], (int)$_POST['sort_order'],
                   isset($_POST['status']) ? 1 : 0, (int)$_POST['id']]);
    header('Location: hero.php?updated=1');
    exit;
}

// ===== DELETE (file bhi delete hogi) =====
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT image FROM hero_slides WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists('../' . $img)) unlink('../' . $img);
    $pdo->prepare("DELETE FROM hero_slides WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: hero.php'); exit;
}

// ===== TOGGLE SHOW/HIDE =====
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE hero_slides SET status = 1 - status WHERE id = ?")->execute([(int)$_GET['toggle']]);
    header('Location: hero.php'); exit;
}

// ===== EDIT MODE =====
 $editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM hero_slides WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

 $slides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC")->fetchAll();
 $f = fn($key, $default = '') => e($editing[$key] ?? $default);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hero Banners</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-image"></i> Hero Banners (Slider)</h2>

        <?php if (isset($_GET['added'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Slide added successfully!</div>"; ?>
        <?php if (isset($_GET['updated'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Slide updated!</div>"; ?>
        <?php if (!empty($error)) echo "<div class='alert-error'><i class='fas fa-exclamation-triangle'></i> $error</div>"; ?>

        <!-- Add/Edit Form -->
        <div class="form-box">
            <h3><?= $editing ? 'Edit Slide #' . $editing['id'] : 'Add New Slide' ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editing): ?>
                    <input type="hidden" name="update_slide" value="1">
                    <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
                    <input type="hidden" name="current_image" value="<?= e($editing['image']) ?>">
                <?php else: ?>
                    <input type="hidden" name="add_slide" value="1">
                <?php endif; ?>

                <div class="form-row">
                    <div>
                        <label>Small Text (WELCOME TO wali line)</label>
                        <input type="text" name="small_text" value="<?= $f('small_text', 'WELCOME TO') ?>">
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?= $f('sort_order', '0') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Title Line 1 (white text)</label>
                        <input type="text" name="title_line1" value="<?= $f('title_line1') ?>" required>
                    </div>
                    <div>
                        <label>Title Line 2 (orange text — optional)</label>
                        <input type="text" name="title_line2" value="<?= $f('title_line2') ?>">
                    </div>
                </div>
                <label>Subtitle</label>
                <textarea name="subtitle" rows="2"><?= $f('subtitle') ?></textarea>
                <div class="form-row">
                    <div>
                        <label>Button 1 Text</label>
                        <input type="text" name="btn1_text" value="<?= $f('btn1_text', 'Admissions Open') ?>">
                    </div>
                    <div>
                        <label>Button 1 Kahan Khule? (page chuno)</label>
                        <?php linkDropdown($pdo, 'btn1_link', $f('btn1_link', 'page.php?slug=admissions')); ?>
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Button 2 Text</label>
                        <input type="text" name="btn2_text" value="<?= $f('btn2_text', 'Learn More') ?>">
                    </div>
                    <div>
                        <label>Button 2 Kahan Khule? (page chuno)</label>
                        <?php linkDropdown($pdo, 'btn2_link', $f('btn2_link', 'page.php?slug=about')); ?>
                    </div>
                </div>
                <label>Background Image (1600×600 recommended <?= $editing ? '— khali chhodo to purani rahegi' : '' ?>)</label>
                <input type="file" name="image" accept="image/*" <?= $editing ? '' : 'required' ?>>
                <?php if ($editing): ?>
                <div style="margin:10px 0;">
                    <img src="../<?= e($editing['image']) ?>" style="width:200px; height:80px; object-fit:cover; border-radius:8px; border:2px solid #eee;" alt="Current">
                </div>
                <label><input type="checkbox" name="status" value="1" <?= $editing['status'] ? 'checked' : '' ?> style="width:auto;"> Visible on website</label>
                <?php endif; ?>
                <button type="submit" class="btn btn-add" style="margin-top:15px;">
                    <i class="fas fa-<?= $editing ? 'save' : 'plus' ?>"></i>
                    <?= $editing ? 'Update Slide' : 'Add Slide' ?>
                </button>
                <?php if ($editing): ?>
                    <a href="hero.php" class="btn" style="background:#64748b; color:#fff; margin-left:10px;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Slides List -->
        <table>
            <tr><th>#</th><th>Preview</th><th>Title</th><th>Sort</th><th>Status</th><th>Action</th></tr>
            <?php foreach ($slides as $s): ?>
            <tr>
                <td><?= (int)$s['id'] ?></td>
                <td><img src="../<?= e($s['image']) ?>" style="width:110px; height:55px; object-fit:cover; border-radius:6px;" alt=""></td>
                <td><?= e($s['title_line1']) ?> <?= $s['title_line2'] ? '<br><small style="color:#f97316;">' . e($s['title_line2']) . '</small>' : '' ?></td>
                <td><?= (int)$s['sort_order'] ?></td>
                <td>
                    <a href="?toggle=<?= (int)$s['id'] ?>" style="text-decoration:none; font-weight:600; color: <?= $s['status'] ? 'green' : '#999' ?>;">
                        <?= $s['status'] ? '● Visible' : '○ Hidden' ?>
                    </a>
                </td>
                <td>
                    <a class="btn" style="background:#0f2a5c; color:#fff;" href="?edit=<?= (int)$s['id'] ?>"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-del" href="?delete=<?= (int)$s['id'] ?>" onclick="return confirm('Delete this slide?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($slides)): ?>
            <tr><td colspan="6" style="text-align:center; color:#999; padding:30px;">Koi slide nahi — upar se pehla banner add karo!</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>