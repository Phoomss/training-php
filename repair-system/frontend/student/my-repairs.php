<?php
require_once('../../configs/connect.php');
session_start();

// Check if user is a student
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../../index.php?error=' . urlencode('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'));
    exit();
}

$student_id = $_SESSION['auth_id'];

// Fetch student's repair requests with related data
$stmt = $conn->prepare("
    SELECT r.*, e.name as equipment_name, t.firstname as tech_firstname, t.lastname as tech_lastname
    FROM repair r
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN technical t ON r.technical_id = t.id
    WHERE r.student_id = (
        SELECT id FROM student WHERE auth_id = :auth_id
    )
    ORDER BY r.created_at DESC
");
$stmt->execute([':auth_id' => $student_id]);
$repairs = $stmt->fetchAll();

// Get student info
$student_stmt = $conn->prepare("SELECT * FROM student WHERE auth_id = :auth_id");
$student_stmt->execute([':auth_id' => $student_id]);
$student = $student_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานแจ้งซ่อมของฉัน - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-pending { color: #ffc107; }
        .status-in_progress { color: #0d6efd; }
        .status-completed { color: #198754; }
        .status-rejected { color: #dc3545; }
    </style>
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>รายงานแจ้งซ่อมของฉัน</h2>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5>ข้อมูลผู้ใช้งาน</h5>
                <p>
                    ชื่อ: <?= htmlspecialchars($student['title'] . ' ' . $student['firstname'] . ' ' . $student['lastname']) ?><br>
                    รหัสนักศึกษา: <?= htmlspecialchars($student['student_id']) ?>
                </p>
            </div>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['status']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>อุปกรณ์</th>
                                <th>รายละเอียด</th>
                                <th>ช่าง</th>
                                <th>สถานะ</th>
                                <th>วันที่แจ้ง</th>
                                <th>รูปภาพ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($repairs) > 0): ?>
                                <?php foreach ($repairs as $index => $repair): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($repair['equipment_name']) ?></td>
                                        <td><?= htmlspecialchars(substr($repair['details'], 0, 50)) ?>...</td>
                                        <td>
                                            <?php if ($repair['technical_id']): ?>
                                                <?= htmlspecialchars($repair['tech_firstname'] . ' ' . $repair['tech_lastname']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">ยังไม่ได้กำหนด</span>
                                            <?php endif; ?>
                                        </td>
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
                                            <?php if ($repair['image']): ?>
                                                <a href="../../<?= htmlspecialchars($repair['image']) ?>" target="_blank">
                                                    <img src="../../<?= htmlspecialchars($repair['image']) ?>" 
                                                         alt="รูปภาพ" width="50" height="50" class="img-thumbnail">
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">ไม่มีรูป</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view_repair.php?id=<?= $repair['id'] ?>" 
                                               class="btn btn-sm btn-info">ดูรายละเอียด</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">ไม่มีข้อมูลคำร้องแจ้งซ่อม</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>