<?php
session_start();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือยัง
if (!isset($_SESSION['auth_id']) || !isset($_SESSION['role'])) {
    header('Location: ../index.php?error=' . urlencode('กรุณาเข้าสู่ระบบก่อน'));
    exit();
}

// ตรวจสอบว่าเป็น student หรือไม่
if ($_SESSION['role'] !== 'student') {
    header('Location: ../index.php?error=' . urlencode('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'));
    exit();
}
?>