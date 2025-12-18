<?php
require_once('../../configs/connect.php');
session_start();

// Check if user is a student
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../../index.php?error=' . urlencode('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'));
    exit();
}

$student_id = $_SESSION['auth_id'];

// Get student info
$student_stmt = $conn->prepare("SELECT * FROM student WHERE auth_id = :auth_id");
$student_stmt->execute([':auth_id' => $student_id]);
$student = $student_stmt->fetch(PDO::FETCH_ASSOC);

// Calculate statistics for this student
$stats = [];

// Total repairs by this student
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE student_id = :student_id");
$stmt->execute([':student_id' => $student['id']]);
$stats['total_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Pending repairs by this student
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE student_id = :student_id AND status = 'pending'");
$stmt->execute([':student_id' => $student['id']]);
$stats['pending_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// In progress repairs by this student
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE student_id = :student_id AND status = 'in_progress'");
$stmt->execute([':student_id' => $student['id']]);
$stats['in_progress_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Completed repairs by this student
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE student_id = :student_id AND status = 'completed'");
$stmt->execute([':student_id' => $student['id']]);
$stats['completed_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - นักศึกษา</title>
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
        .card-pending { border-left-color: #ffc107; }
        .card-in-progress { border-left-color: #0d6efd; }
        .card-completed { border-left-color: #198754; }
    </style>
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2>หน้าหลัก - นักศึกษา</h2>
                <p>ยินดีต้อนรับ <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($student['title'] . ' ' . $student['firstname'] . ' ' . $student['lastname']) ?>)</p>
            </div>
        </div>

        <!-- Student Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>ข้อมูลส่วนตัว</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ชื่อ-นามสกุล:</strong> <?= htmlspecialchars($student['title'] . ' ' . $student['firstname'] . ' ' . $student['lastname']) ?></p>
                                <p><strong>รหัสนักศึกษา:</strong> <?= htmlspecialchars($student['student_id']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
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
                <div class="card dashboard-card card-pending h-100">
                    <div class="card-body">
                        <h5 class="card-title">รอดำเนินการ</h5>
                        <h2 class="text-center"><?= $stats['pending_repairs'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card card-in-progress h-100">
                    <div class="card-body">
                        <h5 class="card-title">กำลังดำเนินการ</h5>
                        <h2 class="text-center"><?= $stats['in_progress_repairs'] ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card card-completed h-100">
                    <div class="card-body">
                        <h5 class="card-title">เสร็จสิ้น</h5>
                        <h2 class="text-center"><?= $stats['completed_repairs'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>การดำเนินการด่วน</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 text-center mb-3">
                                <a href="create-repair.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-tools"></i> แจ้งซ่อม
                                </a>
                            </div>
                            <div class="col-md-6 text-center mb-3">
                                <a href="my-repairs.php" class="btn btn-info btn-lg w-100">
                                    <i class="fas fa-list"></i> รายงานแจ้งซ่อมของฉัน
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
                        <h5>คำร้องแจ้งซ่อมล่าสุดของฉัน</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>อุปกรณ์</th>
                                        <th>รายละเอียด</th>
                                        <th>สถานะ</th>
                                        <th>วันที่แจ้ง</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT r.*, e.name as equipment_name
                                        FROM repair r
                                        LEFT JOIN equipment e ON r.equipment_id = e.id
                                        WHERE r.student_id = :student_id
                                        ORDER BY r.created_at DESC
                                        LIMIT 5
                                    ");
                                    $stmt->execute([':student_id' => $student['id']]);
                                    $recent_repairs = $stmt->fetchAll();
                                    ?>
                                    <?php if (count($recent_repairs) > 0): ?>
                                        <?php foreach ($recent_repairs as $index => $repair): ?>
                                            <tr>
                                                <td><?= $repair['id'] ?></td>
                                                <td><?= htmlspecialchars($repair['equipment_name']) ?></td>
                                                <td><?= htmlspecialchars(substr($repair['details'], 0, 30)) ?>...</td>
                                                <td>
                                                    <span class="badge 
                                                        <?php 
                                                            switch($repair['status']) {
                                                                case 'pending': echo 'bg-warning text-dark'; break;
                                                                case 'in_progress': echo 'bg-primary'; break;
                                                                case 'completed': echo 'bg-success'; break;
                                                                case 'rejected': echo 'bg-danger'; break;
                                                                default: echo 'bg-secondary';
                                                            } 
                                                        ?>
                                                    ">
                                                        <?= $repair['status'] ?>
                                                    </span>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($repair['created_at'])) ?></td>
                                                <td>
                                                    <a href="view_repair.php?id=<?= $repair['id'] ?>" 
                                                       class="btn btn-sm btn-info">ดูรายละเอียด</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">ไม่มีข้อมูลคำร้อง</td>
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