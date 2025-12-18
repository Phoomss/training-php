<?php
require_once('../../configs/connect.php');
session_start();

// Check if user is a student
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../../index.php?error=' . urlencode('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'));
    exit();
}

$repair_id = $_GET['id'] ?? null;
$student_id = $_SESSION['auth_id'];

if (!$repair_id) {
    header('Location: my-repairs.php?error=' . urlencode('ไม่พบข้อมูลคำร้อง'));
    exit();
}

// Fetch repair details with related data
$stmt = $conn->prepare("
    SELECT r.*, e.name as equipment_name, s.firstname, s.lastname, s.student_id, t.firstname as tech_firstname, t.lastname as tech_lastname
    FROM repair r
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN student s ON r.student_id = s.id
    LEFT JOIN technical t ON r.technical_id = t.id
    WHERE r.id = :id AND r.student_id = (
        SELECT id FROM student WHERE auth_id = :auth_id
    )
    LIMIT 1
");
$stmt->execute([':id' => $repair_id, ':auth_id' => $student_id]);
$repair = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$repair) {
    header('Location: my-repairs.php?error=' . urlencode('ไม่พบข้อมูลคำร้อง'));
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคำร้อง - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>รายละเอียดคำร้องแจ้งซ่อม</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>รหัสคำร้อง:</strong> <?= $repair['id'] ?>
                            </div>
                            <div class="col-md-6">
                                <strong>วันที่แจ้ง:</strong> <?= date('d/m/Y H:i', strtotime($repair['created_at'])) ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>อุปกรณ์:</strong> <?= htmlspecialchars($repair['equipment_name']) ?>
                            </div>
                            <div class="col-md-6">
                                <strong>สถานะ:</strong> 
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
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>ผู้แจ้ง:</strong> 
                                <?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']) ?>
                            </div>
                            <div class="col-md-6">
                                <strong>ช่าง:</strong> 
                                <?php if ($repair['technical_id']): ?>
                                    <?= htmlspecialchars($repair['tech_firstname'] . ' ' . $repair['tech_lastname']) ?>
                                <?php else: ?>
                                    <span class="text-muted">ยังไม่ได้กำหนด</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>รายละเอียด:</strong>
                            <p class="border p-3 bg-light"><?= htmlspecialchars($repair['details']) ?></p>
                        </div>

                        <?php if ($repair['image']): ?>
                            <div class="mb-3">
                                <strong>รูปภาพประกอบ:</strong>
                                <div class="mt-2">
                                    <img src="../../<?= htmlspecialchars($repair['image']) ?>" 
                                         alt="รูปภาพประกอบ" class="img-fluid rounded" style="max-width: 100%;">
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="my-repairs.php" class="btn btn-secondary">กลับ</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>