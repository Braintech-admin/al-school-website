<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: index.php'); exit; }
require_once '../config/db.php';
require_once '../includes/functions.php';

// ===== MARK READ / UNREAD =====
if (isset($_GET['toggle_read'])) {
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 - is_read WHERE id = ?")->execute([(int)$_GET['toggle_read']]);
    header('Location: contact-messages.php'); exit;
}

// ===== DELETE =====
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: contact-messages.php'); exit;
}

 $messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
 $unread   = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact Messages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main">
        <?php include 'topbar.php'; ?>

        <h2 class="page-title">
            <i class="fas fa-inbox"></i> Contact Messages
            <?php if ($unread > 0): ?>
            <span style="background:#f97316; color:#fff; font-size:13px; padding:4px 12px; border-radius:20px; vertical-align:middle;">
                <?= (int)$unread ?> naye
            </span>
            <?php endif; ?>
        </h2>

        <table>
            <tr><th>Date</th><th>Name</th><th>Contact</th><th>Subject</th><th>Message</th><th>Status</th><th>Action</th></tr>
            <?php foreach ($messages as $m): ?>
            <tr style="<?= $m['is_read'] ? '' : 'background:#fff7f2;'; ?>">
                <td style="white-space:nowrap;">
                    <?= date('d M Y', strtotime($m['created_at'])) ?><br>
                    <small style="color:#999;"><?= date('h:i A', strtotime($m['created_at'])) ?></small>
                </td>
                <td><b><?= e($m['name']) ?></b></td>
                <td style="font-size:12.5px;">
                    <?php if (!empty($m['phone'])): ?>
                    <i class="fas fa-phone" style="color:#f97316;"></i> <span class="notranslate"><?= e($m['phone']) ?></span><br>
                    <?php endif; ?>
                    <?php if (!empty($m['email'])): ?>
                    <i class="fas fa-envelope" style="color:#f97316;"></i> <?= e($m['email']) ?>
                    <?php endif; ?>
                </td>
                <td><?= e($m['subject'] ?: '—') ?></td>
                <td style="max-width:320px; font-size:13.5px;">
                    <?= e(mb_substr($m['message'], 0, 80)) ?><?= mb_strlen($m['message']) > 80 ? '...' : '' ?>
                    <?php if (mb_strlen($m['message']) > 80): ?>
                    <details style="margin-top:6px;">
                        <summary style="cursor:pointer; color:#f97316; font-weight:600; font-size:12.5px;">Full message padhein</summary>
                        <p style="margin:8px 0 0; white-space:pre-wrap;"><?= e($m['message']) ?></p>
                    </details>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $m['is_read']
                        ? '<span style="color:#999;">Read</span>'
                        : '<b style="color:#f97316;">● New</b>' ?>
                </td>
                <td style="white-space:nowrap;">
                    <?php if (!$m['is_read']): ?>
                    <a class="btn" style="background:#16a34a; color:#fff;" href="?toggle_read=<?= (int)$m['id'] ?>" title="Mark as Read"><i class="fas fa-envelope-open"></i></a>
                    <?php else: ?>
                    <a class="btn" style="background:#64748b; color:#fff;" href="?toggle_read=<?= (int)$m['id'] ?>" title="Mark as Unread"><i class="fas fa-envelope"></i></a>
                    <?php endif; ?>
                    <a class="btn btn-del" href="?delete=<?= (int)$m['id'] ?>" onclick="return confirm('Delete this message?')" title="Delete"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?>
            <tr><td colspan="7" style="text-align:center; color:#999; padding:40px;">
                <i class="fas fa-inbox" style="font-size:30px; display:block; margin-bottom:10px;"></i>
                Abhi koi message nahi aaya hai.
            </td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>