<?php
session_start();

if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'ADMIN') {
    die("❌ คุณไม่มีสิทธิ์เข้าหน้านี้ (Admin only)");
}