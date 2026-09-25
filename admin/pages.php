<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== PAGE UPDATE =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_page'])) {
    $banner = !empty($_FILES['banner_image']['name'])
        ? uploadImage($_FILES['banner_image'], '../uploads/')
        : $_POST['current_banner'];

    $pdo->prepare("UPDATE pages SET page_title = ?, page_subtitle = ?, content = ?, banner_image = ? WHERE slug = ?")
        ->execute([$_POST['page_title'], $_POST['page_subtitle'], $_POST['content'], $banner, $_POST['slug']]);
    $success = "Page updated successfully!";
    $edit_slug = $_POST['slug'];
}

// ===== MISSION/VISION ADD =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_mv'])) {
    $pdo->prepare("INSERT INTO mission_vision (icon, title, description, sort_order) VALUES (?, ?, ?, ?)")
        ->execute([$_POST['icon'], $_POST['title'], $_POST['description'], (int)$_POST['sort_order']]);
    $success = "Card added!";
    $edit_slug = 'about';
}

// ===== MISSION/VISION DELETE =====
if (isset($_GET['del_mv'])) {
    $pdo->prepare("DELETE FROM mission_vision WHERE id = ?")->execute([$_GET['del_mv']]);
    header('Location: pages.php?edit=about');
    exit;
}

// ===== Kaunsa page edit kar rahe hain =====
 $edit_slug = $_GET['edit'] ?? 'about';
 $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
 $stmt->execute([$edit_slug]);
 $page = $stmt->fetch();

 $all_pages = $pdo->query("SELECT slug, page_title FROM pages ORDER BY id ASC")->fetchAll();
 $mv_items  = $pdo->query("SELECT * FROM mission_vision ORDER BY sort_order ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Pages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <h2 class="page-title"><i class="fas fa-file-alt"></i> Manage Pages</h2>

        <?php if (!empty($success)) echo "<div class='alert-success'><i class='fas fa-check'></i> $success</div>"; ?>

        <!-- Page Selector -->
        <div class="form-box">
            <label><b>Select Page to Edit</b></label>
            <select onchange="location='pages.php?edit='+this.value">
                <?php foreach ($all_pages as $p): ?>
                <option value="<?= e($p['slug']) ?>" <?= $edit_slug == $p['slug'] ? 'selected' : '' ?>>
                    <?= e($p['page_title']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if ($page): ?>
        <!-- Page Editor -->
        <div class="form-box">
            <h3>Editing: <?= e($page['page_title']) ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_page" value="1">
                <input type="hidden" name="slug" value="<?= e($page['slug']) ?>">
                <input type="hidden" name="current_banner" value="<?= e($page['banner_image']) ?>">

                <label>Page Title</label>
                <input type="text" name="page_title" value="<?= e($page['page_title']) ?>" required>

                <label>Subtitle (orange text — optional)</label>
                <input type="text" name="page_subtitle" value="<?= e($page['page_subtitle']) ?>">

                <label>Page Content (HTML allowed — &lt;h3&gt;, &lt;p&gt;, &lt;b&gt;, &lt;ul&gt; etc.)</label>
                <textarea name="content" rows="14" style="font-family:monospace; font-size:13px;"><?= e($page['content']) ?></textarea>

                <label>Banner Image (chhod do to purani rahegi)</label>
                <input type="file" name="banner_image" accept="image/*">

                <button type="submit" class="btn btn-add"><i class="fas fa-save"></i> Update Page</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Mission/Vision Manager (sirf About ke liye) -->
        <?php if ($edit_slug === 'about'): ?>
        <div class="form-box">
            <h3>Mission / Vision / Values Cards</h3>
            <form method="POST">
                <input type="hidden" name="add_mv" value="1">
                <div class="form-row">
                    <div>
                        <label>Font Awesome Icon (e.g. fa-star)</label>
                        <input type="text" name="icon" value="fa-star" required>
                    </div>
                    <div>
                        <label>Title</label>
                        <input type="text" name="title" placeholder="e.g. Our Mission" required>
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="0">
                    </div>
                </div>
                <label>Description</label>
                <textarea name="description" rows="2" required></textarea>
                <button type="submit" class="btn btn-add"><i class="fas fa-plus"></i> Add Card</button>
            </form>

            <table style="margin-top:20px;">
                <tr><th>Icon</th><th>Title</th><th>Description</th><th>Action</th></tr>
                <?php foreach ($mv_items as $mv): ?>
                <tr>
                    <td><i class="fas <?= e($mv['icon']) ?>" style="color:#f97316;"></i> <?= e($mv['icon']) ?></td>
                    <td><b><?= e($mv['title']) ?></b></td>
                    <td><?= e(substr($mv['description'], 0, 60)) ?>...</td>
                    <td><a class="btn btn-del" href="?del_mv=<?= $mv['id'] ?>" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>