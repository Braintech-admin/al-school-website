<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== PAGE UPDATE (redirect pattern — refresh safe) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_page'])) {
    $banner = !empty($_FILES['banner_image']['name'])
        ? uploadImage($_FILES['banner_image'], '../uploads/')
        : $_POST['current_banner'];

    $pdo->prepare("UPDATE pages SET page_title = ?, page_subtitle = ?, content = ?, banner_image = ? WHERE slug = ?")
        ->execute([$_POST['page_title'], $_POST['page_subtitle'], $_POST['content'], $banner, $_POST['slug']]);
    header('Location: pages.php?edit=' . urlencode($_POST['slug']) . '&updated=1');
    exit;
}

// ===== MISSION/VISION ADD =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_mv'])) {
    $pdo->prepare("INSERT INTO mission_vision (icon, title, description, sort_order) VALUES (?, ?, ?, ?)")
        ->execute([$_POST['icon'], $_POST['title'], $_POST['description'], (int)$_POST['sort_order']]);
    header('Location: pages.php?edit=about&mv_added=1');
    exit;
}

// ===== MISSION/VISION UPDATE (naya — edit support) =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_mv'])) {
    $pdo->prepare("UPDATE mission_vision SET icon = ?, title = ?, description = ?, sort_order = ? WHERE id = ?")
        ->execute([$_POST['icon'], $_POST['title'], $_POST['description'], (int)$_POST['sort_order'], (int)$_POST['id']]);
    header('Location: pages.php?edit=about&mv_updated=1');
    exit;
}

// ===== MISSION/VISION DELETE =====
if (isset($_GET['del_mv'])) {
    $pdo->prepare("DELETE FROM mission_vision WHERE id = ?")->execute([(int)$_GET['del_mv']]);
    header('Location: pages.php?edit=about');
    exit;
}

// ===== Kaunsa page edit kar rahe hain =====
 $edit_slug = $_GET['edit'] ?? 'about';
if (!preg_match('/^[a-z0-9\-]+$/', $edit_slug)) $edit_slug = 'about';

 $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
 $stmt->execute([$edit_slug]);
 $page = $stmt->fetch();

 $all_pages = $pdo->query("SELECT slug, page_title FROM pages ORDER BY id ASC")->fetchAll();
 $mv_items  = $pdo->query("SELECT * FROM mission_vision ORDER BY sort_order ASC")->fetchAll();

// MV edit mode
 $editing_mv = null;
if ($edit_slug === 'about' && isset($_GET['edit_mv'])) {
    $stmt = $pdo->prepare("SELECT * FROM mission_vision WHERE id = ?");
    $stmt->execute([(int)$_GET['edit_mv']]);
    $editing_mv = $stmt->fetch();
}
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
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-file-alt"></i> Manage Pages</h2>

        <?php if (isset($_GET['updated'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Page updated successfully!</div>"; ?>
        <?php if (isset($_GET['mv_added'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Card added!</div>"; ?>
        <?php if (isset($_GET['mv_updated'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Card updated!</div>"; ?>

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

                <label>Page Content (editor me likho — Word jaisa hi hai)</label>
                <textarea id="pageEditor" name="content"><?= e($page['content']) ?></textarea>

                <label>Banner Image (chhod do to purani rahegi)</label>
                <input type="file" name="banner_image" accept="image/*">
                <?php if ($page['banner_image']): ?>
                <div style="margin:8px 0;">
                    <img src="../<?= e($page['banner_image']) ?>" style="width:200px; height:80px; object-fit:cover; border-radius:8px; border:2px solid #eee;" alt="Banner">
                </div>
                <?php endif; ?>

                <button type="submit" class="btn btn-add" style="margin-top:12px;"><i class="fas fa-save"></i> Update Page</button>
            </form>
        </div>
        <?php else: ?>
        <div class="alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            Is slug ka page database me nahi mila. Upar dropdown se dusra page chuno.
        </div>
        <?php endif; ?>

        <!-- Editor Guide (alag box — nested nahi) -->
        <div class="form-box" style="border-left: 4px solid #f97316;">
            <h3 style="font-size:15px;"><i class="fas fa-circle-question"></i> Editor Buttons ki Guide</h3>
            <table style="font-size:13px;">
                <tr><td style="width:180px;"><b>B I U</b></td><td>Text ko Bold, Italic (tircha), ya Underline karo — pehle text select karo, phir button dabao</td></tr>
                <tr><td><b>¶ wala dropdown</b></td><td>Heading banane ke liye — "Heading 2" select karo to bada title banega, "Paragraph" normal text ke liye</td></tr>
                <tr><td><b>⬅ ⬌ ➡</b></td><td>Text ko left / center / right align karna</td></tr>
                <tr><td><b>• aur 1. wale icons</b></td><td>Round wali list (bullet points) ya number wali list banane ke liye</td></tr>
                <tr><td><b>🔗 Link icon</b></td><td>Kisi word ko clickable link banana — word select karo → icon dabao → URL daalo</td></tr>
                <tr><td><b>🖼️ Image icon</b></td><td>Photo add karna — icon dabao → "Upload" choose karo → computer se photo chuno</td></tr>
                <tr><td><b>Table icon</b></td><td>Fee structure jaisi table banana</td></tr>
                <tr><td><b>✗ removeformat</b></td><td>Selected text ki saari formatting hata deta hai</td></tr>
                <tr><td><b>Hindi typing</b></td><td>Editor me Hindi bhi likh sakte ho — Windows me Win + Space se Hindi keyboard on karo</td></tr>
            </table>
        </div>

        <!-- Mission/Vision Manager (sirf About ke liye) -->
        <?php if ($edit_slug === 'about'): ?>
        <div class="form-box">
            <h3><?= $editing_mv ? 'Edit Card #' . (int)$editing_mv['id'] : 'Mission / Vision / Values Cards' ?></h3>
            <form method="POST">
                <?php if ($editing_mv): ?>
                    <input type="hidden" name="update_mv" value="1">
                    <input type="hidden" name="id" value="<?= (int)$editing_mv['id'] ?>">
                <?php else: ?>
                    <input type="hidden" name="add_mv" value="1">
                <?php endif; ?>
                <div class="form-row">
                    <div>
                        <label>Font Awesome Icon (e.g. fa-star)</label>
                        <input type="text" name="icon" value="<?= e($editing_mv['icon'] ?? 'fa-star') ?>" required>
                    </div>
                    <div>
                        <label>Title</label>
                        <input type="text" name="title" value="<?= e($editing_mv['title'] ?? '') ?>" placeholder="e.g. Our Mission" required>
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?= (int)($editing_mv['sort_order'] ?? 0) ?>">
                    </div>
                </div>
                <label>Description</label>
                <textarea name="description" rows="2" required><?= e($editing_mv['description'] ?? '') ?></textarea>
                <button type="submit" class="btn btn-add">
                    <i class="fas fa-<?= $editing_mv ? 'save' : 'plus' ?>"></i>
                    <?= $editing_mv ? 'Update Card' : 'Add Card' ?>
                </button>
                <?php if ($editing_mv): ?>
                    <a href="pages.php?edit=about" class="btn" style="background:#64748b; color:#fff; margin-left:10px;">Cancel</a>
                <?php endif; ?>
            </form>

            <table style="margin-top:20px;">
                <tr><th>Icon</th><th>Title</th><th>Description</th><th>Sort</th><th>Action</th></tr>
                <?php foreach ($mv_items as $mv): ?>
                <tr>
                    <td><i class="fas <?= e($mv['icon']) ?>" style="color:#f97316; font-size:18px;"></i> <small><?= e($mv['icon']) ?></small></td>
                    <td><b><?= e($mv['title']) ?></b></td>
                    <td><?= e(mb_substr($mv['description'], 0, 60)) ?><?= mb_strlen($mv['description']) > 60 ? '...' : '' ?></td>
                    <td><?= (int)$mv['sort_order'] ?></td>
                    <td>
                        <a class="btn" style="background:#0f2a5c; color:#fff;" href="?edit=about&edit_mv=<?= (int)$mv['id'] ?>"><i class="fas fa-edit"></i></a>
                        <a class="btn btn-del" href="?del_mv=<?= (int)$mv['id'] ?>" onclick="return confirm('Delete this card?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($mv_items)): ?>
                <tr><td colspan="5" style="text-align:center; color:#999; padding:25px;">Koi card nahi — upar se add karo!</td></tr>
                <?php endif; ?>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.3/tinymce.min.js"></script>
    <script>
    tinymce.init({
        selector: '#pageEditor',
        height: 450,
        menubar: false,
        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount',
        toolbar: 'undo redo | bold italic underline | blocks | alignleft aligncenter alignright | bullist numlist | link image table | removeformat | preview fullscreen',
        images_upload_url: 'editor-upload.php',
        automatic_uploads: true,
        file_picker_types: 'image',
        file_picker_callback: function (cb) {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    const id = 'blobid' + (new Date()).getTime();
                    const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    const blobInfo = blobCache.create(id, file, reader.result);
                    blobCache.add(blobInfo);
                    cb(blobInfo.blobUri(), { title: file.name });
                });
                reader.readAsDataURL(file);
            });
            input.click();
        },
        content_style: 'body { font-family: Poppins, sans-serif; font-size: 15px; } img { max-width: 100%; height: auto; }'
    });
    </script>
</body>
</html>