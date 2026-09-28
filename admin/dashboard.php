<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// Super admin ka dashboard nahi hota
if (($_SESSION['admin_role'] ?? '') === 'super') { header('Location: website-control.php'); exit; }

// ===== MARK NOTICES AS READ =====
if (isset($_GET['seen'])) {
    $pdo->prepare("UPDATE admins SET notices_seen_at = NOW() WHERE id = ?")->execute([$_SESSION['admin_id']]);
    header('Location: dashboard.php'); exit;
}

// ===== UNREAD NOTICES (last seen ke baad wale) =====
 $stmt = $pdo->prepare("SELECT n.id, n.title, n.description, n.created_at
    FROM notices n JOIN admins a ON a.id = ?
    WHERE n.status = 1 AND (a.notices_seen_at IS NULL OR n.created_at > a.notices_seen_at)
    ORDER BY n.id DESC");
 $stmt->execute([$_SESSION['admin_id']]);
 $unread_notices = $stmt->fetchAll();

// ===== NEWS ADD =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news'])) {
    $image = uploadImage($_FILES['image'], '../uploads/');
    $stmt = $pdo->prepare("INSERT INTO news (title, description, event_date, image, show_popup) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['title'], $_POST['description'], $_POST['event_date'], $image,
                    isset($_POST['show_popup']) ? 1 : 0]);
    header('Location: dashboard.php?added=1'); exit;
}

// ===== NEWS UPDATE (edit + image replace) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_news'])) {
    $new_image = !empty($_FILES['image']['name']) ? uploadImage($_FILES['image'], '../uploads/') : null;
    if ($new_image && !empty($_POST['current_image'])) {
        $old = '../' . $_POST['current_image'];
        if (file_exists($old)) unlink($old);
    }
    $pdo->prepare("UPDATE news SET title = ?, description = ?, event_date = ?, image = ?, show_popup = ? WHERE id = ?")
        ->execute([$_POST['title'], $_POST['description'], $_POST['event_date'],
                   $new_image ?: $_POST['current_image'], isset($_POST['show_popup']) ? 1 : 0, (int)$_POST['id']]);
    header('Location: dashboard.php?updated=1'); exit;
}

// ===== NEWS DELETE (file bhi delete) =====
if (isset($_GET['delete_news'])) {
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->execute([(int)$_GET['delete_news']]);
    $img = $stmt->fetchColumn();
    if ($img && file_exists('../' . $img)) unlink('../' . $img);
    $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([(int)$_GET['delete_news']]);
    header('Location: dashboard.php'); exit;
}

// ===== POPUP QUICK-TOGGLE (list se ek click me on/off) =====
if (isset($_GET['toggle_popup'])) {
    $pdo->prepare("UPDATE news SET show_popup = 1 - show_popup WHERE id = ?")->execute([(int)$_GET['toggle_popup']]);
    header('Location: dashboard.php'); exit;
}

// ===== EDIT MODE =====
 $editing = null;
if (isset($_GET['edit_news'])) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([(int)$_GET['edit_news']]);
    $editing = $stmt->fetch();
}

 $all_news = $pdo->query("SELECT * FROM news ORDER BY event_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <!-- ===== NOTICE BANNER ===== -->
        <?php if (!empty($unread_notices)): ?>
        <div class="notice-banner">
            <h4><i class="fas fa-bullhorn"></i> Naye Notices — kripya dhyan dein</h4>
            <ul>
                <?php foreach ($unread_notices as $un): ?>
                <li>
                    <b><?= e($un['title']) ?></b>
                    <small>(<?= date('d M Y', strtotime($un['created_at'])) ?>)</small>
                    <?php if ($un['description']): ?><br><span style="font-size:13px;"><?= e($un['description']) ?></span><?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
            <a href="?seen=1" class="btn btn-add" style="padding:8px 18px; font-size:13px;">
                <i class="fas fa-check"></i> Mark as Read
            </a>
        </div>
        <?php endif; ?>

        <h2 class="page-title"><i class="fas fa-newspaper"></i> Manage News & Events</h2>

        <?php if (isset($_GET['added'])) echo "<div class='alert-success'><i class='fas fa-check'></i> News added successfully!</div>"; ?>
        <?php if (isset($_GET['updated'])) echo "<div class='alert-success'><i class='fas fa-check'></i> News updated successfully!</div>"; ?>

        <!-- Add/Edit Form -->
        <div class="form-box">
            <h3><?= $editing ? 'Edit News #' . (int)$editing['id'] : 'Add New News/Event' ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editing): ?>
                    <input type="hidden" name="update_news" value="1">
                    <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
                    <input type="hidden" name="current_image" value="<?= e($editing['image']) ?>">
                <?php else: ?>
                    <input type="hidden" name="add_news" value="1">
                <?php endif; ?>

                <input type="text" name="title" placeholder="News Title"
                       value="<?= e($editing['title'] ?? '') ?>" required>
                <textarea name="description" placeholder="Description" rows="3" required><?= e($editing['description'] ?? '') ?></textarea>
                <input type="date" name="event_date" value="<?= e($editing['event_date'] ?? '') ?>" required>
                <input type="file" name="image" accept="image/*">
                <?php if ($editing && $editing['image']): ?>
                <div style="margin-bottom:12px;">
                    <img src="../<?= e($editing['image']) ?>" style="width:110px; height:70px; object-fit:cover; border-radius:6px; border:2px solid #eee;" alt="Current">
                    <small style="color:#64748b; display:block; margin-top:4px;">Current image — change karni ho to upar nayi choose karo</small>
                </div>
                <?php endif; ?>

                <!-- POPUP CHECKBOX — submit se PEHLE (form ke andar upar) -->
                <label style="display:flex; align-items:center; gap:8px; background:#fff7ed; border:1px solid #fed7aa; border-radius:8px; padding:12px 15px; margin:12px 0; cursor:pointer;">
                    <input type="checkbox" name="show_popup" value="1" <?= ($editing && $editing['show_popup']) ? 'checked' : '' ?>
                           style="width:auto; transform:scale(1.3); cursor:pointer;">
                    <span style="font-size:14px; color:#9a3412;">
                        <b>🔔 Website khulte hi POPUP dikhao</b> (special news/event — visitors ko sabse pehle dikhega)
                    </span>
                </label>

                <button type="submit" class="btn btn-add">
                    <i class="fas fa-<?= $editing ? 'save' : 'plus' ?>"></i>
                    <?= $editing ? 'Update News' : 'Add News' ?>
                </button>
                <?php if ($editing): ?>
                    <a href="dashboard.php" class="btn" style="background:#64748b; color:#fff; margin-left:10px;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- News List -->
        <table>
            <tr><th>Date</th><th>Title</th><th>Description</th><th>Image</th><th>Popup</th><th>Action</th></tr>
            <?php foreach ($all_news as $n): ?>
            <tr>
                <td><?= date('d M Y', strtotime($n['event_date'])) ?></td>
                <td><b><?= e($n['title']) ?></b></td>
                <td><?= e(mb_substr($n['description'], 0, 60)) ?><?= mb_strlen($n['description']) > 60 ? '...' : '' ?></td>
                <td>
                    <?php if ($n['image']): ?>
                    <img src="../<?= e($n['image']) ?>" alt="">
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td>
                    <a href="?toggle_popup=<?= (int)$n['id'] ?>" title="Click karke popup on/off karo"
                       style="text-decoration:none; font-weight:600; font-size:13px; color: <?= $n['show_popup'] ? '#f97316' : '#999' ?>;">
                        <?= $n['show_popup'] ? '🔔 ON' : '○ Off' ?>
                    </a>
                </td>
                <td style="white-space:nowrap;">
                    <a class="btn" style="background:#0f2a5c; color:#fff;" href="?edit_news=<?= (int)$n['id'] ?>"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-del" href="?delete_news=<?= (int)$n['id'] ?>" onclick="return confirm('Delete this news?')"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($all_news)): ?>
            <tr><td colspan="6" style="text-align:center; color:#999; padding:30px;">Koi news nahi — upar se add karo!</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>