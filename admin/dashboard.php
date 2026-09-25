<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== NEWS ADD KARNA =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news'])) {
    $image = uploadImage($_FILES['image'], '../uploads/');   // ✅ Fix: sahi folder
    $stmt = $pdo->prepare("INSERT INTO news (title, description, event_date, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['title'], $_POST['description'], $_POST['event_date'], $image]);
    $success = "News added successfully!";
}

// ===== NEWS DELETE =====
if (isset($_GET['delete_news'])) {
    $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([$_GET['delete_news']]);
    header('Location: dashboard.php');
    exit;
}

 $all_news = $pdo->query("SELECT * FROM news ORDER BY event_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">   <!-- ✅ Fix: CSS link -->
</head>
<body>
    <?php include 'sidebar.php'; ?>            <!-- ✅ Fix: shared sidebar -->
    <div class="main">
        <h2 class="page-title"><i class="fas fa-newspaper"></i> Manage News & Events</h2>

        <?php if (!empty($success)) echo "<div class='alert-success'><i class='fas fa-check'></i> $success</div>"; ?>

        <!-- Add News Form -->
        <div class="form-box">
            <h3>Add New News/Event</h3>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="add_news" value="1">
                <input type="text" name="title" placeholder="News Title" required>
                <textarea name="description" placeholder="Description" rows="3" required></textarea>
                <input type="date" name="event_date" required>
                <input type="file" name="image" accept="image/*">
                <button type="submit" class="btn btn-add"><i class="fas fa-plus"></i> Add News</button>
            </form>
        </div>

        <!-- News List -->
        <table>
            <tr><th>Date</th><th>Title</th><th>Description</th><th>Action</th></tr>
            <?php foreach ($all_news as $n): ?>
            <tr>
                <td><?= date('d M Y', strtotime($n['event_date'])) ?></td>
                <td><?= htmlspecialchars($n['title']) ?></td>
                <td><?= htmlspecialchars(substr($n['description'], 0, 50)) ?>...</td>
                <td><a class="btn btn-del" href="?delete_news=<?= $n['id'] ?>" onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>