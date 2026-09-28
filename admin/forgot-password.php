<?php
session_start();
require_once '../config/db.php';
require_once '../includes/functions.php';

// Already logged in? Panel bhejo
if (isset($_SESSION['admin_id'])) { header('Location: dashboard.php'); exit; }

 $sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');

    if ($identifier !== '') {
        // Username YA email se dhundo
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$identifier, $identifier]);
        $admin = $stmt->fetch();

        // Sirf tab email bhejo jab email registered ho
        if ($admin && !empty($admin['email']) && $admin['status'] == 1) {
            $raw_token  = bin2hex(random_bytes(32));           // email me jaane wala token
            $token_hash = hash('sha256', $raw_token);          // DB me safe hash
            $expires    = date('Y-m-d H:i:s', time() + 3600);  // 1 ghanta valid

            $pdo->prepare("UPDATE admins SET reset_token = ?, reset_expires = ? WHERE id = ?")
                ->execute([$token_hash, $expires, $admin['id']]);

            // Reset link banao (domain auto-detect)
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $link = $scheme . '://' . $_SERVER['HTTP_HOST']
                  . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/reset-password.php?token=' . $raw_token;

            $body = "
            <div style='font-family:Arial,sans-serif; max-width:520px; margin:auto; border:1px solid #eee; border-radius:10px; overflow:hidden;'>
                <div style='background:#0f2a5c; color:#fff; padding:25px; text-align:center;'>
                    <h2 style='margin:0;'>Password Reset</h2>
                </div>
                <div style='padding:25px;'>
                    <p>Namaste <b>{$admin['name']}</b>,</p>
                    <p>Aapne password reset ki request ki hai. Neeche wale button pe click karke naya password set karein:</p>
                    <p style='text-align:center; margin:25px 0;'>
                        <a href='{$link}' style='background:#f97316; color:#fff; padding:13px 30px; border-radius:8px; text-decoration:none; font-weight:bold;'>
                            Naya Password Set Karein
                        </a>
                    </p>
                    <p style='font-size:13px; color:#666;'>Ye link <b>1 ghante</b> ke liye valid hai. Agar aapne request nahi ki thi, to is email ko ignore karein — aapka password waise hi rahega.</p>
                </div>
                <div style='background:#f0f4fa; padding:15px; text-align:center; font-size:12px; color:#888;'>
                    Ye automated email hai — reply na karein.
                </div>
            </div>";

            sendMail($admin['email'], 'Password Reset — Admin Panel', $body);
        }
    }
    // Security: hamesha same message (account exist karta hai ya nahi — pata nahi chalna chahiye)
    $sent = true;
}
?>
<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Poppins',sans-serif; background:#d6eaf8; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
.card { background:#fff; border-radius:14px; width:100%; max-width:440px; overflow:hidden; box-shadow:0 10px 40px rgba(0,0,0,.12); }
.card-head { background:#0f2a5c; color:#fff; text-align:center; padding:30px 25px; }
.card-head i { font-size:36px; color:#f97316; }
.card-head h2 { font-size:19px; margin-top:10px; }
.card-body { padding:30px 35px; }
.sub { color:#64748b; font-size:13.5px; margin-bottom:20px; }
input { width:100%; padding:13px 15px; border:1px solid #d7e3ee; border-radius:8px; font-size:14px; font-family:inherit; margin-bottom:18px; }
input:focus { outline:none; border-color:#29abe2; }
button { width:100%; background:#0f2a5c; color:#fff; border:none; padding:14px; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; font-family:inherit; }
button:hover { background:#f97316; }
.success { background:#dcfce7; color:#166534; padding:15px; border-radius:8px; font-size:14px; line-height:1.7; margin-bottom:18px; }
.note { color:#94a3b8; font-size:12.5px; margin-top:18px; text-align:center; line-height:1.7; }
.note a { color:#f97316; font-weight:600; }
</style>
</head>
<body>
    <div class="card">
        <div class="card-head">
            <i class="fas fa-lock-open"></i>
            <h2>Password Bhool Gaye?</h2>
        </div>
        <div class="card-body">
            <?php if ($sent): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                Agar aapka account registered email ke saath hai, to <b>reset link email pe bhej diya gaya hai</b>.
                <br><br>📧 Apna inbox aur <b>Spam folder</b> check karein. Link 1 ghante valid hai.
            </div>
            <?php else: ?>
            <p class="sub">Apna username ya registered email daalein — reset link email pe aa jayega.</p>
            <form method="POST">
                <input type="text" name="identifier" placeholder="Username ya Email" required>
                <button type="submit">Reset Link Bhejein <i class="fas fa-paper-plane"></i></button>
            </form>
            <?php endif; ?>
            <p class="note">
                Email nahi aa raha ya email set nahi hai?<br>
                Apne <b>Web Admin se sampark karein</b>. &nbsp;|&nbsp;
                <a href="index.php"><i class="fas fa-arrow-left"></i> Login pe wapas</a>
            </p>
        </div>
    </div>
</body>
</html>