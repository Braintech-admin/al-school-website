<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== PHOTO ADD =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_photo'])) {
    $image = uploadImage($_FILES['image'], '../uploads/');
    if ($image) {
        $pdo->prepare("INSERT INTO gallery (title, image, sort_order) VALUES (?, ?, ?)")
            ->execute([$_POST['title'], $image, (int)$_POST['sort_order']]);
        $success = "Photo added successfully!";
    } else {
        $error = "Upload failed! Only jpg, jpeg, png, webp, gif allowed (max 5MB).";
    }
}

// ===== PHOTO DELETE (file bhi delete hogi) =====
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists('../' . $img)) unlink('../' . $img);
    $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$_GET['delete']]);
    header('Location: gallery.php');
    exit;
}

 $items = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC")->fetchAll();
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
        <h2 class="page-title"><i class="fas fa-images"></i> Manage Gallery</h2>

        <?php if (!empty($success)) echo "<div class='alert-success'><i class='fas fa-check'></i> $success</div>"; ?>
        <?php if (!empty($error)) echo "<div class='alert-error'><i class='fas fa-exclamation-triangle'></i> $error</div>"; ?>

        <!-- Add Photo Form -->
        <div class="form-box">
            <h3>Add New Photo</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_photo" value="1">
                <div class="form-row">
                    <div>
                        <label>Photo Title</label>
                        <input type="text" name="title" placeholder="e.g. Annual Function 2025" required>
                    </div>
                    <div>
                        <label>Sort Order (chhota number = pehle dikhega)</label>
                        <input type="number" name="sort_order" value="0">
                    </div>
                </div>
                <label>Upload Image (JPG/PNG/WebP — max 5MB)</label>
                <input type="file" name="image" accept="image/*" required>
                <button type="submit" class="btn btn-add"><i class="fas fa-plus"></i> Add Photo</button>
            </form>
        </div>

        <!-- Photos Table -->
        <table>
            <tr><th>#</th><th>Photo</th><th>Title</th><th>Sort</th><th>Action</th></tr>
            <?php foreach ($items as $i => $g): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><img src="../<?= e($g['image']) ?>" alt=""></td>
                <td><?= e($g['title']) ?></td>
                <td><?= (int)$g['sort_order'] ?></td>
                <td>
                    <a class="btn btn-del" href="?delete=<?= $g['id'] ?>" onclick="return confirm('Delete this photo?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr><td colspan="5" style="text-align:center; color:#999; padding:30px;">No photos yet — upar se pehli photo add karo!</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>