<?php
 $password = 'Admin@123';   // ← yahan apna password likho
echo "<h3 style='font-family:monospace; background:#f0f4fa; padding:15px; border-radius:8px; word-break:break-all;'>";
echo password_hash($password, PASSWORD_DEFAULT);
echo "</h3>";