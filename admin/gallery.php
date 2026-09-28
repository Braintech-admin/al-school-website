<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== ADD PHOTO =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photo'])) {
    $image = uploadImage($_FILES['image'], '../uploads/');
    if ($image) {
        $pdo->prepare("INSERT INTO gallery (album_id, title, image, sort_order) VALUES (?, ?, ?, ?)")
            ->execute([(int)$_POST['album_id'], $_POST['title'], $image, (int)$_POST['sort_order']]);
        header('Location: gallery.php?added=1'); exit;
    }
    $error = "Upload failed! Only jpg, jpeg, png, webp, gif allowed (max 5MB).";
}

// ===== UPDATE PHOTO =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_photo'])) {
    // Nayi image aayi to purani file disk se delete karo (space waste na ho)
    $new_image = !empty($_FILES['image']['name']) ? uploadImage($_FILES['image'], '../uploads/') : null;
    if ($new_image && !empty($_POST['current_image'])) {
        $old = '../' . $_POST['current_image'];
        if (file_exists($old)) unlink($old);
    }
    $pdo->prepare("UPDATE gallery SET album_id = ?, title = ?, image = ?, sort_order = ? WHERE id = ?")
        ->execute([(int)$_POST['album_id'], $_POST['title'], $new_image ?: $_POST['current_image'], (int)$_POST['sort_order'], (int)$_POST['id']]);
    header('Location: gallery.php?updated=1'); exit;
}

// ===== DELETE (file bhi delete hogi) =====
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists('../' . $img)) unlink('../' . $img);
    $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: gallery.php'); exit;
}

// ===== EDIT MODE =====
 $editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

 $items  = $pdo->query("SELECT g.*, a.name AS album_name FROM gallery g LEFT JOIN albums a ON g.album_id = a.id ORDER BY g.album_id, g.sort_order ASC")->fetchAll();
 $albums = $pdo->query("SELECT id, name FROM albums ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-images"></i> Manage Gallery</h2>

        <?php if (isset($_GET['added'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Photo added!</div>"; ?>
        <?php if (isset($_GET['updated'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Photo updated!</div>"; ?>
        <?php if (!empty($error)) echo "<div class='alert-error'><i class='fas fa-exclamation-triangle'></i> $error</div>"; ?>

        <?php if (empty($albums)): ?>
        <!-- Albums hi nahi hain — pehle album banao -->
        <div class="alert-error">
            <i class="fas fa-folder-open"></i>
            Koi album nahi hai! Photo daalne se pehle <a href="albums.php" style="color:#0f2a5c; font-weight:700;"><b>Albums</b></a> page se ek album banao.
        </div>
        <?php else: ?>

        <!-- Add/Edit Form -->
        <div class="form-box">
            <h3><?= $editing ? 'Edit Photo #' . (int)$editing['id'] : 'Add New Photo' ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editing): ?>
                    <input type="hidden" name="update_photo" value="1">
                    <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
                    <input type="hidden" name="current_image" value="<?= e($editing['image']) ?>">
                <?php else: ?>
                    <input type="hidden" name="add_photo" value="1">
                <?php endif; ?>

                <div class="form-row">
                    <div>
                        <label>In Which Album?</label>
                        <select name="album_id" required>
                            <?php $current_album = $editing['album_id'] ?? ''; ?>
                            <?php foreach ($albums as $al): ?>
                            <option value="<?= (int)$al['id'] ?>" <?= $current_album == $al['id'] ? 'selected' : '' ?>>
                                <?= e($al['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Photo Title</label>
                        <input type="text" name="title" value="<?= e($editing['title'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label>Sort Order (chhota = pehle)</label>
                        <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
                    </div>
                </div>

                <label>Upload Image (JPG/PNG/WebP — max 5MB)<?= $editing ? ' — khali chhodo to purani rahegi' : '' ?></label>
                <input type="file" name="image" accept="image/*" <?= $editing ? '' : 'required' ?>>

                <?php if ($editing): ?>
                <div style="margin:10px 0;">
                    <img src="../<?= e($editing['image']) ?>" style="width:120px; height:85px; object-fit:cover; border-radius:8px; border:2px solid #eee;" alt="Current">
                </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-add">
                    <i class="fas fa-<?= $editing ? 'save' : 'plus' ?>"></i>
                    <?= $editing ? 'Update Photo' : 'Add Photo' ?>
                </button>
                <?php if ($editing): ?>
                    <a href="gallery.php" class="btn" style="background:#64748b; color:#fff; margin-left:10px;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Photos Table -->
        <table>
            <tr><th>#</th><th>Photo</th><th>Album</th><th>Title</th><th>Sort</th><th>Action</th></tr>
            <?php foreach ($items as $i => $g): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><img src="../<?= e($g['image']) ?>" alt=""></td>
                <td><b><?= e($g['album_name'] ?? '-') ?></b></td>
                <td><?= e($g['title']) ?></td>
                <td><?= (int)$g['sort_order'] ?></td>
                <td>
                    <a class="btn" style="background:#0f2a5c; color:#fff;" href="?edit=<?= (int)$g['id'] ?>"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-del" href="?delete=<?= (int)$g['id'] ?>" onclick="return confirm('Delete this photo?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr><td colspan="6" style="text-align:center; color:#999; padding:30px;">No photos yet — upar se pehli photo add karo!</td></tr>
            <?php endif; ?>
        </table>

        <?php endif; // albums empty check ?>
    </div>
</body>
</html>