<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== ADD =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_msg'])) {
    $photo = uploadImage($_FILES['photo'], '../uploads/');
    if (!$photo) {
        $error = "Photo upload failed! (jpg/png/webp, max 5MB)";   // photo ke bina insert nahi
    } else {
        $pdo->prepare("INSERT INTO messages (designation, name, photo, message, show_on_home, sort_order) VALUES (?, ?, ?, ?, ?, ?)")
            ->execute([$_POST['designation'], $_POST['name'], $photo, $_POST['message'],
                       isset($_POST['show_on_home']) ? 1 : 0, (int)$_POST['sort_order']]);
        header('Location: messages.php?added=1'); exit;
    }
}

// ===== UPDATE =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_msg'])) {
    $new_photo = !empty($_FILES['photo']['name']) ? uploadImage($_FILES['photo'], '../uploads/') : null;

    // Nayi photo aayi to purani file disk se delete karo (space waste na ho)
    if ($new_photo && !empty($_POST['current_photo'])) {
        $old = '../' . $_POST['current_photo'];
        if (file_exists($old)) unlink($old);
    }

    $pdo->prepare("UPDATE messages SET designation=?, name=?, photo=?, message=?, show_on_home=?, sort_order=? WHERE id=?")
        ->execute([$_POST['designation'], $_POST['name'], $new_photo ?: $_POST['current_photo'], $_POST['message'],
                   isset($_POST['show_on_home']) ? 1 : 0, (int)$_POST['sort_order'], (int)$_POST['id']]);
    header('Location: messages.php?updated=1'); exit;
}

// ===== DELETE (photo file bhi) =====
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT photo FROM messages WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists('../' . $img)) unlink('../' . $img);
    $pdo->prepare("DELETE FROM messages WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: messages.php'); exit;
}

// ===== HOMEPAGE TOGGLE =====
if (isset($_GET['toggle_home'])) {
    $pdo->prepare("UPDATE messages SET show_on_home = 1 - show_on_home WHERE id = ?")->execute([(int)$_GET['toggle_home']]);
    header('Location: messages.php'); exit;
}

// ===== SHOW/HIDE TOGGLE =====
if (isset($_GET['toggle_status'])) {
    $pdo->prepare("UPDATE messages SET status = 1 - status WHERE id = ?")->execute([(int)$_GET['toggle_status']]);
    header('Location: messages.php'); exit;
}

// ===== EDIT MODE =====
 $editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

 $items = $pdo->query("SELECT * FROM messages ORDER BY sort_order ASC")->fetchAll();
 $f = fn($key, $default = '') => e($editing[$key] ?? $default);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-user-tie"></i> Manage Messages</h2>

        <?php if (isset($_GET['added'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Message added!</div>"; ?>
        <?php if (isset($_GET['updated'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Message updated!</div>"; ?>
        <?php if (!empty($error)) echo "<div class='alert-error'><i class='fas fa-exclamation-triangle'></i> $error</div>"; ?>

        <!-- Add/Edit Form -->
        <div class="form-box">
            <h3><?= $editing ? 'Edit: ' . e($editing['designation']) : 'Add New Message' ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editing): ?>
                    <input type="hidden" name="update_msg" value="1">
                    <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
                    <input type="hidden" name="current_photo" value="<?= e($editing['photo']) ?>">
                <?php else: ?>
                    <input type="hidden" name="add_msg" value="1">
                <?php endif; ?>

                <div class="form-row">
                    <div>
                        <label>Designation (Manager / Principal / Director...)</label>
                        <input type="text" name="designation" value="<?= $f('designation') ?>" required>
                    </div>
                    <div>
                        <label>Name (optional)</label>
                        <input type="text" name="name" value="<?= $f('name') ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div>
                        <label>Sort Order (chhota = pehle)</label>
                        <input type="number" name="sort_order" value="<?= $f('sort_order', '0') ?>">
                    </div>
                    <div>
                        <label>Photo<?= $editing ? ' (khali = purani rahegi)' : '' ?></label>
                        <input type="file" name="photo" accept="image/*" <?= $editing ? '' : 'required' ?>>
                    </div>
                </div>
                <?php if ($editing && $editing['photo']): ?>
                    <div style="margin-bottom:12px;">
                        <img src="../<?= e($editing['photo']) ?>" style="width:80px; height:80px; object-fit:cover; border-radius:8px;" alt="Current">
                    </div>
                <?php endif; ?>
                <label>Message</label>
                <textarea name="message" rows="6" required><?= $f('message') ?></textarea>
                <label style="margin-top:5px;">
                    <input type="checkbox" name="show_on_home" value="1" <?= ($editing && $editing['show_on_home']) || !$editing ? 'checked' : '' ?>
                           style="width:auto; margin-right:6px;">
                    Homepage par dikha do
                </label>
                <button type="submit" class="btn btn-add" style="margin-top:12px;">
                    <i class="fas fa-<?= $editing ? 'save' : 'plus' ?>"></i>
                    <?= $editing ? 'Update Message' : 'Add Message' ?>
                </button>
                <?php if ($editing): ?>
                    <a href="messages.php" class="btn" style="background:#64748b; color:#fff; margin-left:10px;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Messages List -->
        <table>
            <tr><th>Photo</th><th>Designation</th><th>Name</th><th>Homepage</th><th>Sort</th><th>Visibility</th><th>Actions</th></tr>
            <?php foreach ($items as $m): ?>
            <tr>
                <td>
                    <?php if ($m['photo']): ?>
                    <img src="../<?= e($m['photo']) ?>" alt="">
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><b><?= e($m['designation']) ?></b></td>
                <td><?= e($m['name']) ?></td>
                <td>
                    <a href="?toggle_home=<?= (int)$m['id'] ?>" style="text-decoration:none; font-weight:600; color: <?= $m['show_on_home'] ? 'green' : '#999' ?>;">
                        <?= $m['show_on_home'] ? '● On Home' : '○ Off' ?>
                    </a>
                </td>
                <td><?= (int)$m['sort_order'] ?></td>
                <td>
                    <a href="?toggle_status=<?= (int)$m['id'] ?>" style="text-decoration:none; font-weight:600; color: <?= $m['status'] ? 'green' : '#dc2626' ?>;">
                        <?= $m['status'] ? '● Visible' : '○ Hidden' ?>
                    </a>
                </td>
                <td>
                    <a class="btn" style="background:#0f2a5c; color:#fff;" href="?edit=<?= (int)$m['id'] ?>"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-del" href="?delete=<?= (int)$m['id'] ?>" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr><td colspan="7" style="text-align:center; color:#999; padding:30px;">No messages — upar se pehla message add karo!</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>