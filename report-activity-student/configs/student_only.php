<?php
// session_start();

if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'STUDENT') {
    header("Location: ../index.php?error=" . urlencode("คุณไม่มีสิทธิ์เข้าหน้านี้"));
    exit();
}
