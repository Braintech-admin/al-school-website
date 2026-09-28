<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
if (($_SESSION['admin_role'] ?? '') !== 'super') { header('Location: dashboard.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== ADD =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_notice'])) {
    $pdo->prepare("INSERT INTO notices (title, description, status) VALUES (?, ?, 1)")
        ->execute([trim($_POST['title'] ?? ''), trim($_POST['description'] ?? '')]);
    header('Location: notices.php?added=1'); exit;
}

// ===== UPDATE =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_notice'])) {
    $pdo->prepare("UPDATE notices SET title = ?, description = ? WHERE id = ?")
        ->execute([trim($_POST['title'] ?? ''), trim($_POST['description'] ?? ''), (int)$_POST['id']]);
    header('Location: notices.php?updated=1'); exit;
}

// ===== TOGGLE SHOW/HIDE =====
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE notices SET status = 1 - status WHERE id = ?")->execute([(int)$_GET['toggle']]);
    header('Location: notices.php'); exit;
}

// ===== DELETE =====
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM notices WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: notices.php'); exit;
}

// ===== EDIT MODE =====
 $editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM notices WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

// Latest pehle (id DESC = sabse naya top pe)
 $items = $pdo->query("SELECT * FROM notices ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Notices</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-bullhorn"></i> Manage Notices</h2>

        <?php if (isset($_GET['added'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Notice published! School Admins ko login pe dikhega.</div>"; ?>
        <?php if (isset($_GET['updated'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Notice updated!</div>"; ?>

        <div class="form-box">
            <h3><?= $editing ? 'Edit Notice #' . (int)$editing['id'] : 'Add New Notice' ?></h3>
            <form method="POST">
                <?php if ($editing): ?>
                    <input type="hidden" name="update_notice" value="1">
                    <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
                <?php else: ?>
                    <input type="hidden" name="add_notice" value="1">
                <?php endif; ?>

                <label>Notice Title</label>
                <input type="text" name="title" value="<?= e($editing['title'] ?? '') ?>" required>

                <label>Description (optional)</label>
                <textarea name="description" rows="2"><?= e($editing['description'] ?? '') ?></textarea>

                <button type="submit" class="btn btn-add" style="margin-top:12px;">
                    <i class="fas fa-<?= $editing ? 'save' : 'bullhorn' ?>"></i>
                    <?= $editing ? 'Update Notice' : 'Publish Notice' ?>
                </button>
                <?php if ($editing): ?>
                    <a href="notices.php" class="btn" style="background:#64748b; color:#fff; margin-left:10px;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <table>
            <tr><th>#</th><th>Notice</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($items as $i => $n): ?>
            <tr <?= $i === 0 ? "style='background:#fff7ed;'" : '' ?>>
                <td><?= $i + 1 ?></td>
                <td>
                    <b><?= e($n['title']) ?></b>
                    <?php if ($i === 0 && $n['status']): ?><span style="background:#f97316; color:#fff; font-size:10px; font-weight:700; padding:2px 8px; border-radius:10px; margin-left:6px;">LATEST</span><?php endif; ?>
                    <?php if ($n['description']): ?><br><small style="color:#64748b;"><?= e($n['description']) ?></small><?php endif; ?>
                </td>
                <td><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?></td>
                <td>
                    <a href="?toggle=<?= (int)$n['id'] ?>" style="text-decoration:none; font-weight:600; color: <?= $n['status'] ? 'green' : '#999' ?>;">
                        <?= $n['status'] ? '● Visible' : '○ Hidden' ?>
                    </a>
                </td>
                <td>
                    <a class="btn" style="background:#0f2a5c; color:#fff;" href="?edit=<?= (int)$n['id'] ?>"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-del" href="?delete=<?= (int)$n['id'] ?>" onclick="return confirm('Delete this notice?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr><td colspan="5" style="text-align:center; color:#999; padding:30px;">Koi notice nahi — upar se add karo!</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>