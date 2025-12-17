<?php
session_start(); // Ensure session is started
require_once '../layouts/navbar.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$role = strtolower($_SESSION['role'] ?? '');
$username = htmlspecialchars($_SESSION['username'] ?? 'User');
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../layouts/navbar.php'; ?>

    <main class="container mt-5">

        <div class="row justify-content-center">
            <?php if ($role === 'admin'): ?>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-tools fs-1 mb-2 text-primary"></i>
                            <h5 class="card-title">จัดการแจ้งซ่อม</h5>
                            <a href="repair.php" class="btn btn-primary btn-sm mt-2">จัดการ</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-building fs-1 mb-2 text-success"></i>
                            <h5 class="card-title">จัดการสถานที่</h5>
                            <a href="location.php" class="btn btn-success btn-sm mt-2">จัดการ</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-tags fs-1 mb-2 text-warning"></i>
                            <h5 class="card-title">จัดการหมวดหมู่</h5>
                            <a href="categories.php" class="btn btn-warning btn-sm mt-2">จัดการ</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-laptop fs-1 mb-2 text-info"></i>
                            <h5 class="card-title">จัดการอุปกรณ์</h5>
                            <a href="equipment.php" class="btn btn-info btn-sm mt-2">จัดการ</a>
                        </div>
                    </div>
                </div>
            <?php elseif ($role === 'student'): ?>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-person-circle fs-1 mb-2"></i>
                            <h5 class="card-title">Profile</h5>
                            <a href="profile.php" class="btn btn-primary btn-sm mt-2">View</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-journal-check fs-1 mb-2"></i>
                            <h5 class="card-title">My Activities</h5>
                            <a href="activity_detail.php" class="btn btn-primary btn-sm mt-2">View</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-light text-center py-3 mt-5">
        &copy; <?php echo date("Y"); ?> Repair System. All rights reserved.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>