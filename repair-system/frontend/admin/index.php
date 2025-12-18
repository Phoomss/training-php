<?php
require_once('../../configs/admin_only.php');
require_once('../../configs/connect.php');

// Calculate statistics
$stats = [];

// Total repairs
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair");
$stmt->execute();
$stats['total_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// รอซ่อม repairs
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE status = 'รอซ่อม'");
$stmt->execute();
$stats['รอซ่อม_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// In progress repairs
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE status = 'กำลังซ่อม'");
$stmt->execute();
$stats['กำลังซ่อม_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// เสร็จสิ้น repairs
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE status = 'เสร็จสิ้น'");
$stmt->execute();
$stats['เสร็จสิ้น_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total equipment
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM equipment");
$stmt->execute();
$stats['total_equipment'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total students
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM student");
$stmt->execute();
$stats['total_students'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Total technical staff
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM technical");
$stmt->execute();
$stats['total_technical'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - ผู้ดูแลระบบ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .card-total { border-left-color: #6c757d; }
        .card-รอซ่อม { border-left-color: #ffc107; }
        .card-in-progress { border-left-color: #0d6efd; }
        .card-เสร็จสิ้น { border-left-color: #198754; }
        .card-equipment { border-left-color: #fd7e14; }
        .card-student { border-left-color: #6f42c1; }
        .card-technical { border-left-color: #dc3545; }
    </style>
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2>หน้าหลัก - ผู้ดูแลระบบ</h2>
                <p>ยินดีต้อนรับ <?= htmlspecialchars($_SESSION['username']) ?> เข้าสู่ระบบจัดการแจ้งซ่อม</p>
            </div>
        </div>

        <!-- Dashboard Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card card-total h-100">
                    <div class="card-body">
                        <h5 class="card-title">คำร้องทั้งหมด</h5>
                        <h2 class="text-center"><?= $stats['total_repairs'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card card-รอซ่อม h-100">
                    <div class="card-body">
                        <h5 class="card-title">รอดำเนินการ</h5>
                        <h2 class="text-center"><?= $stats['รอซ่อม_repairs'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card card-in-progress h-100">
                    <div class="card-body">
                        <h5 class="card-title">กำลังดำเนินการ</h5>
                        <h2 class="text-center"><?= $stats['กำลังซ่อม_repairs'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card card-เสร็จสิ้น h-100">
                    <div class="card-body">
                        <h5 class="card-title">เสร็จสิ้น</h5>
                        <h2 class="text-center"><?= $stats['เสร็จสิ้น_repairs'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card card-equipment h-100">
                    <div class="card-body">
                        <h5 class="card-title">จำนวนอุปกรณ์</h5>
                        <h2 class="text-center"><?= $stats['total_equipment'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card card-student h-100">
                    <div class="card-body">
                        <h5 class="card-title">จำนวนนักศึกษา</h5>
                        <h2 class="text-center"><?= $stats['total_students'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card card-technical h-100">
                    <div class="card-body">
                        <h5 class="card-title">จำนวนช่างเทคนิค</h5>
                        <h2 class="text-center"><?= $stats['total_technical'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>การจัดการด่วน</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-3">
                                <a href="equipment.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-tools"></i> จัดการอุปกรณ์
                                </a>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <a href="repair.php" class="btn btn-info btn-lg w-100">
                                    <i class="fas fa-list"></i> สถานะการซ่อม
                                </a>
                            </div>
                            <div class="col-md-4 text-center mb-3">
                                <a href="report.php" class="btn btn-success btn-lg w-100">
                                    <i class="fas fa-chart-bar"></i> รายงานแจ้งซ่อม
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Repairs -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>คำร้องแจ้งซ่อมล่าสุด</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>อุปกรณ์</th>
                                        <th>ผู้แจ้ง</th>
                                        <th>สถานะ</th>
                                        <th>วันที่แจ้ง</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT r.*, e.name as equipment_name, s.firstname, s.lastname
                                        FROM repair r
                                        LEFT JOIN equipment e ON r.equipment_id = e.id
                                        LEFT JOIN student s ON r.student_id = s.id
                                        ORDER BY r.created_at DESC
                                        LIMIT 5
                                    ");
                                    $stmt->execute();
                                    $recent_repairs = $stmt->fetchAll();
                                    ?>
                                    <?php if (count($recent_repairs) > 0): ?>
                                        <?php foreach ($recent_repairs as $index => $repair): ?>
                                            <tr>
                                                <td><?= $repair['id'] ?></td>
                                                <td><?= htmlspecialchars($repair['equipment_name']) ?></td>
                                                <td><?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']) ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?php 
                                                            switch($repair['status']) {
                                                                case 'รอซ่อม': echo 'bg-warning text-dark'; break;
                                                                case 'กำลังซ่อม': echo 'bg-primary'; break;
                                                                case 'เสร็จสิ้น': echo 'bg-success'; break;
                                                                default: echo 'bg-secondary';
                                                            } 
                                                        ?>
                                                    ">
                                                        <?= $repair['status'] ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($repair['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">ไม่มีข้อมูลคำร้องล่าสุด</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>