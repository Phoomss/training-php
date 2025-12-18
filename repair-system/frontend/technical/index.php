<?php
require_once('../../configs/connect.php');
session_start();

// Check if user is a technical staff
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'technical') {
    header('Location: ../../index.php?error=' . urlencode('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'));
    exit();
}

$technical_id = $_SESSION['auth_id'];

// Get technical staff info
$tech_stmt = $conn->prepare("SELECT * FROM technical WHERE auth_id = :auth_id");
$tech_stmt->execute([':auth_id' => $technical_id]);
$technical = $tech_stmt->fetch(PDO::FETCH_ASSOC);

// Calculate statistics for this technical staff
$stats = [];

// Total repairs assigned to this technical staff
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE technical_id = :technical_id");
$stmt->execute([':technical_id' => $technical['id']]);
$stats['total_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Pending repairs assigned to this technical staff
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE technical_id = :technical_id AND status = 'pending'");
$stmt->execute([':technical_id' => $technical['id']]);
$stats['pending_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// In progress repairs assigned to this technical staff
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE technical_id = :technical_id AND status = 'in_progress'");
$stmt->execute([':technical_id' => $technical['id']]);
$stats['in_progress_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Completed repairs assigned to this technical staff
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE technical_id = :technical_id AND status = 'completed'");
$stmt->execute([':technical_id' => $technical['id']]);
$stats['completed_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Repairs without assigned technician (available to take)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE technical_id IS NULL AND status != 'completed' AND status != 'rejected'");
$stmt->execute();
$stats['available_repairs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าหลัก - ช่างเทคนิค</title>
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
        .card-available { border-left-color: #fd7e14; }
    </style>
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2>หน้าหลัก - ช่างเทคนิค</h2>
                <p>ยินดีต้อนรับ <?= htmlspecialchars($_SESSION['username']) ?> (<?= htmlspecialchars($technical['title'] . ' ' . $technical['firstname'] . ' ' . $technical['lastname']) ?>)</p>
            </div>
        </div>

        <!-- Technical Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>ข้อมูลส่วนตัว</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ชื่อ-นามสกุล:</strong> <?= htmlspecialchars($technical['title'] . ' ' . $technical['firstname'] . ' ' . $technical['lastname']) ?></p>
                                <p><strong>เบอร์ติดต่อ:</strong> <?= htmlspecialchars($technical['phone']) ?></p>
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
            <div class="col-md-3 mb-3">
                <div class="card dashboard-card card-available h-100">
                    <div class="card-body">
                        <h5 class="card-title">งานใหม่</h5>
                        <h2 class="text-center"><?= $stats['available_repairs'] ?></h2>
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
                                <a href="repairs.php" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-list"></i> รายการแจ้งซ่อม
                                </a>
                            </div>
                            <?php if ($stats['available_repairs'] > 0): ?>
                                <div class="col-md-6 text-center mb-3">
                                    <a href="repairs.php" class="btn btn-warning btn-lg w-100">
                                        <i class="fas fa-hand-paper"></i> รับงานใหม่
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="col-md-6 text-center mb-3">
                                    <div class="btn btn-success btn-lg w-100 disabled">
                                        <i class="fas fa-check"></i> ไม่มีงานใหม่
                                    </div>
                                </div>
                            <?php endif; ?>
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
                        <h5>คำร้องแจ้งซ่อมล่าสุดที่ได้รับมอบหมาย</h5>
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
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT r.*, e.name as equipment_name, s.firstname, s.lastname
                                        FROM repair r
                                        LEFT JOIN equipment e ON r.equipment_id = e.id
                                        LEFT JOIN student s ON r.student_id = s.id
                                        WHERE r.technical_id = :technical_id
                                        ORDER BY r.created_at DESC
                                        LIMIT 5
                                    ");
                                    $stmt->execute([':technical_id' => $technical['id']]);
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
                                                    <a href="process_repair.php?id=<?= $repair['id'] ?>" 
                                                       class="btn btn-sm <?php echo $repair['status'] === 'completed' ? 'btn-info' : 'btn-warning'; ?>">
                                                        <?php echo $repair['status'] === 'completed' ? 'ดูรายละเอียด' : 'ดำเนินการ'; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">ไม่มีงานที่ได้รับมอบหมาย</td>
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