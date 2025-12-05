<?php
require_once "../configs/admin_only.php";
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ตั้งค่าระบบ</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php require_once "../layouts/navbar.php"; ?>

    <div class="container py-5">

        <!-- Header Section -->
        <div class="mb-4">
            <h2 class="fw-bold">ตั้งค่าระบบ</h2>
            <p class="text-muted">จัดการตั้งค่าระบบพื้นฐานสำหรับระบบ DevClub</p>
        </div>

        <!-- Settings Cards -->
        <div class="row g-4">

            <!-- System Information -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">ℹ️ ข้อมูลระบบ</h5>
                        <p class="card-text">ดูข้อมูลเกี่ยวกับระบบและเวอร์ชันที่ใช้งาน</p>
                        <button class="btn btn-primary" type="button" disabled>ดูข้อมูล</button>
                    </div>
                </div>
            </div>

            <!-- User Management -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">👥 จัดการผู้ใช้งาน</h5>
                        <p class="card-text">ดูและจัดการบัญชีผู้ใช้งานในระบบ</p>
                        <a href="users.php" class="btn btn-primary">จัดการผู้ใช้</a>
                    </div>
                </div>
            </div>

            <!-- Backup & Restore -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">💾 สำรองข้อมูล</h5>
                        <p class="card-text">สำรองและกู้คืนข้อมูลระบบ</p>
                        <button class="btn btn-primary" type="button" disabled>จัดการข้อมูล</button>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">🔒 ความปลอดภัย</h5>
                        <p class="card-text">ตั้งค่าความปลอดภัยของระบบ</p>
                        <button class="btn btn-primary" type="button" disabled>ตั้งค่าความปลอดภัย</button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>