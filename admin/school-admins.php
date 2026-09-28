<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
if (($_SESSION['admin_role'] ?? '') !== 'super') { header('Location: dashboard.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

 $error = '';

// ===== ADD SCHOOL ADMIN =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $username = trim($_POST['username'] ?? '');
    $name     = trim($_POST['name'] ?? '');
    $password = $_POST['password'] ?? '';

    // Server-side validation (HTML bypass-safe)
    if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
        $error = 'Username me sirf letters, numbers, underscore (3-30 chars) — spaces nahi!';
    } elseif (strlen($password) < 6) {
        $error = 'Password kam se kam 6 characters ka hona chahiye!';
    } else {
        $chk = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
        $chk->execute([$username]);
        if ($chk->fetch()) {
            $error = 'Ye username pehle se exist karta hai!';
        } else {
            $pdo->prepare("INSERT INTO admins (username, password, name, role, status) VALUES (?, ?, ?, 'school', 1)")
                ->execute([$username, password_hash($password, PASSWORD_DEFAULT), $name]);
            header('Location: school-admins.php?added=1'); exit;
        }
    }
}

// ===== RESET PASSWORD =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_pass'])) {
    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) {
        $error = 'Password kam se kam 6 characters ka hona chahiye!';
    } else {
        $pdo->prepare("UPDATE admins SET password = ? WHERE id = ? AND role = 'school'")
            ->execute([password_hash($password, PASSWORD_DEFAULT), (int)$_POST['id']]);
        header('Location: school-admins.php?updated=1'); exit;
    }
}

// ===== ACTIVE/INACTIVE TOGGLE (sirf school role pe) =====
if (isset($_GET['toggle'])) {
    $pdo->prepare("UPDATE admins SET status = 1 - status WHERE id = ? AND role = 'school'")->execute([(int)$_GET['toggle']]);
    header('Location: school-admins.php'); exit;
}

// ===== DELETE (sirf school role pe) =====
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM admins WHERE id = ? AND role = 'school'")->execute([(int)$_GET['delete']]);
    header('Location: school-admins.php'); exit;
}

// ===== EDIT MODE =====
 $editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ? AND role = 'school'");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

 $items = $pdo->query("SELECT * FROM admins WHERE role = 'school' ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>School Admins</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title"><i class="fas fa-users-cog"></i> School Admins</h2>

        <?php if (isset($_GET['added'])) echo "<div class='alert-success'><i class='fas fa-check'></i> School admin created!</div>"; ?>
        <?php if (isset($_GET['updated'])) echo "<div class='alert-success'><i class='fas fa-check'></i> Password updated!</div>"; ?>
        <?php if (!empty($error)) echo "<div class='alert-error'><i class='fas fa-exclamation-triangle'></i> " . e($error) . "</div>"; ?>

        <div class="form-box">
            <h3><?= $editing ? 'Reset Password — ' . e($editing['username']) : 'Add New School Admin' ?></h3>
            <form method="POST" autocomplete="off">
                <?php if ($editing): ?>
                    <input type="hidden" name="reset_pass" value="1">
                    <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
                    <label>New Password</label>
                    <input type="password" name="password" minlength="6" required>
                <?php else: ?>
                    <input type="hidden" name="add_admin" value="1">
                    <div class="form-row">
                        <div>
                            <label>Admin Name</label>
                            <input type="text" name="name" placeholder="e.g. R. Kumar (Office)"
                                   value="<?= e($_POST['name'] ?? '') ?>" required>
                        </div>
                        <div>
                            <label>Username (letters/numbers/underscore)</label>
                            <input type="text" name="username" placeholder="e.g. office01"
                                   value="<?= e($_POST['username'] ?? '') ?>"
                                   pattern="[a-zA-Z0-9_]{3,30}" title="3-30 chars — sirf letters, numbers, underscore" required>
                        </div>
                        <div>
                            <label>Password (min 6 characters)</label>
                            <input type="password" name="password" minlength="6" required>
                        </div>
                    </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-add" style="margin-top:12px;">
                    <i class="fas fa-<?= $editing ? 'key' : 'plus' ?>"></i>
                    <?= $editing ? 'Update Password' : 'Create Admin' ?>
                </button>
                <?php if ($editing): ?>
                    <a href="school-admins.php" class="btn" style="background:#64748b; color:#fff; margin-left:10px;">Cancel</a>
                <?php endif; ?>
            </form>
        </div>

        <table>
            <tr><th>#</th><th>Name</th><th>Username</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($items as $a): ?>
            <tr>
                <td><?= (int)$a['id'] ?></td>
                <td><b><?= e($a['name']) ?></b></td>
                <td><code style="background:#f0f4fa; padding:3px 8px; border-radius:5px;"><?= e($a['username']) ?></code></td>
                <td>
                    <a href="?toggle=<?= (int)$a['id'] ?>" style="text-decoration:none; font-weight:600; color: <?= $a['status'] ? 'green' : '#dc2626' ?>;">
                        <?= $a['status'] ? '● Active' : '○ Inactive' ?>
                    </a>
                </td>
                <td style="white-space:nowrap;">
                    <a class="btn" style="background:#f97316; color:#fff;" href="?edit=<?= (int)$a['id'] ?>" title="Reset Password"><i class="fas fa-key"></i></a>
                    <a class="btn btn-del" href="?delete=<?= (int)$a['id'] ?>" onclick="return confirm('Delete this admin? Wo login nahi kar payega.')" title="Delete"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr><td colspan="5" style="text-align:center; color:#999; padding:30px;">Koi school admin nahi — upar se add karo!</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>