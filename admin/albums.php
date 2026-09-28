<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== ADD =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_album'])) {
    $cover = uploadImage($_FILES['cover_image'], '../uploads/');
    $pdo->prepare("INSERT INTO albums (name, cover_image, sort_order) VALUES (?, ?, ?)")
        ->execute([$_POST['name'], $cover ?: '', (int)$_POST['sort_order']]);
    header('Location: albums.php?added=1'); exit;
}

// ===== UPDATE =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_album'])) {
    $cover = !empty($_FILES['cover_image']['name']) ? uploadImage($_FILES['cover_image'], '../uploads/') : null;
    if ($cover && !empty($_POST['current_cover'])) {
        $old = '../' . $_POST['current_cover'];
        if (file_exists($old)) unlink($old);   // purani cover file delete
    }
    $pdo->prepare("UPDATE albums SET name=?, cover_image=?, sort_order=? WHERE id=?")
        ->execute([$_POST['name'], $cover ?: $_POST['current_cover'], (int)$_POST['sort_order'], (int)$_POST['id']]);
    header('Location: albums.php?updated=1'); exit;
}

// ===== DELETE (album + uski saari photos DB + disk se) =====
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Album ki photos delete (files + DB)
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE album_id = ?");
    $stmt->execute([$id]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $img) {
        if ($img && file_exists('../' . $img)) unlink('../' . $img);
    }
    $pdo->prepare("DELETE FROM gallery WHERE album_id = ?")->execute([$id]);

    // Cover file delete
    $stmt = $pdo->prepare("SELECT cover_image FROM albums WHERE id = ?");
    $stmt->execute([$id]);
    $cover = $stmt->fetchColumn();
    if ($cover && file_exists('../' . $cover)) unlink('../' . $cover);

    $pdo->prepare("DELETE FROM albums WHERE id = ?")->execute([$id]);
    header('Location: albums.php'); exit;
}

// ===== EDIT MODE =====
 $editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM albums WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

// Albums + photo count + fallback cover — EK HI query me (fast & clean)
 $albums = $pdo->query("
    SELECT a.*,
        COALESCE(NULLIF(a.cover_image,''),
            (SELECT g.image FROM gallery g WHERE g.album_id = a.id ORDER BY g.sort_order ASC, g.id ASC LIMIT 1)
        ) AS display_cover,
        (SELECT COUNT(*) FROM gallery g WHERE g.album_id = a.id) AS photo_count
    FROM albums a
    ORDER BY a.sort_order ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Albums</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-folder-open"></i> Manage Albums</h2>

        <?php if (isset($_GET['added'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Album added!</div>"; ?>
        <?php if (isset($_GET['updated'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Album updated!</div>"; ?>

        <div class="form-box">
            <h3><?= $editing ? 'Edit Album' : 'Add New Album (event ke naam se)' ?></h3>
            <form method="POST" enctype="multipart/form-data">
                <?php if ($editing): ?>
                    <input type="hidden" name="update_album" value="1">
                    <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
                    <input type="hidden" name="current_cover" value="<?= e($editing['cover_image']) ?>">
                <?php else: ?>
                    <input type="hidden" name="add_album" value="1">
                <?php endif; ?>
                <div class="form-row">
                    <div>
                        <label>Album Name (e.g. Annual Function 2025)</label>
                        <input type="text" name="name" value="<?= e($editing['name'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
                    </div>
                </div>
                <label>Cover Image (optional — khali chhoda to album ki pehli photo cover ban jayegi)</label>
                <input type="file" name="cover_image" accept="image/*">
                <?php if ($editing && $editing['cover_image']): ?>
                    <img src="../<?= e($editing['cover_image']) ?>" style="width:110px; height:75px; object-fit:cover; border-radius:8px; margin:8px 0;" alt="">
                <?php endif; ?>
                <button type="submit" class="btn btn-add" style="margin-top:12px;">
                    <i class="fas fa-<?= $editing ? 'save' : 'plus' ?>"></i>
                    <?= $editing ? 'Update Album' : 'Add Album' ?>
                </button>
                <?php if ($editing): ?>
                    <a href="albums.php" class="btn" style="background:#64748b; color:#fff; margin-left:10px;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <table>
            <tr><th>#</th><th>Cover</th><th>Album Name</th><th>Photos</th><th>Sort</th><th>Action</th></tr>
            <?php foreach ($albums as $i => $a): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td>
                    <?php if ($a['display_cover']): ?>
                    <img src="../<?= e($a['display_cover']) ?>" alt="">
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td><b><?= e($a['name']) ?></b></td>
                <td><?= (int)$a['photo_count'] ?> photos</td>
                <td><?= (int)$a['sort_order'] ?></td>
                <td>
                    <a class="btn" style="background:#0f2a5c; color:#fff;" href="gallery.php?album=<?= (int)$a['id'] ?>" title="Is album ki photos dekho"><i class="fas fa-images"></i></a>
                    <a class="btn" style="background:#16a34a; color:#fff;" href="?edit=<?= (int)$a['id'] ?>" title="Edit"><i class="fas fa-edit"></i></a>
                    <a class="btn btn-del" href="?delete=<?= (int)$a['id'] ?>" onclick="return confirm('Delete album + iski saari photos?')" title="Delete"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($albums)): ?>
            <tr><td colspan="6" style="text-align:center; color:#999; padding:30px;">Koi album nahi — upar se pehla album add karo!</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>