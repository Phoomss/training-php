<?php
session_start();

// ลบค่าทั้งหมดใน session
$_SESSION = [];

// ลบทิ้ง session cookie ด้วย (เพื่อความชัวร์)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// ทำลาย session
session_destroy();

// กลับไปหน้า login
header('Location: index.php');
exit();
