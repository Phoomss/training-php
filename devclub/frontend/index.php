<?php
require_once "../configs/admin_only.php";  
require_once "../configs/connect.php";     
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php require_once "../layouts/navbar.php"; ?>

    <div class="container py-5">

        <!-- Header Section -->
        <div class="mb-4">
            <h2 class="fw-bold">Admin Dashboard</h2>
            <p class="text-muted">
                ยินดีต้อนรับ คุณ <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </p>
        </div>

        <!-- Admin Main Cards -->
        <div class="row g-4">

            <!-- Manage Users -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">👤 จัดการสาขา</h5>
                        <p class="card-text text-muted">เพิ่ม / แก้ไข / ลบ </p>
                        <a href="./majors.php" class="btn btn-primary">จัดการสาขา</a>
                    </div>
                </div>
            </div>

            <!-- Manage Members -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">🧑‍🤝‍🧑 สมาชิก DevClub</h5>
                        <p class="card-text text-muted">ตรวจสอบข้อมูลสมาชิกชมรม DevClub</p>
                        <a href="./members.php" class="btn btn-primary">เปิดดูข้อมูล</a>
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title">⚙️ ตั้งค่าระบบ</h5>
                        <p class="card-text text-muted">ตั้งค่าระบบพื้นฐานสำหรับผู้ดูแลระบบ</p>
                        <a href="./settings.php" class="btn btn-primary">ตั้งค่าระบบ</a>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
