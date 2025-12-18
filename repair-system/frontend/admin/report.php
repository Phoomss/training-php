<?php
require_once('../../configs/admin_only.php');
require_once('../../configs/connect.php');

// Fetch repair reports with related data
$stmt = $conn->prepare("
    SELECT r.*, e.name as equipment_name, s.firstname, s.lastname, s.student_id, t.firstname as tech_firstname, t.lastname as tech_lastname
    FROM repair r
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN student s ON r.student_id = s.id
    LEFT JOIN technical t ON r.technical_id = t.id
    ORDER BY r.created_at DESC
");
$stmt->execute();
$repairs = $stmt->fetchAll();

// Calculate statistics
$total_repairs = count($repairs);
$pending_repairs = count(array_filter($repairs, function($r) { return $r['status'] === 'pending'; }));
$in_progress_repairs = count(array_filter($repairs, function($r) { return $r['status'] === 'in_progress'; }));
$completed_repairs = count(array_filter($repairs, function($r) { return $r['status'] === 'completed'; }));
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานแจ้งซ่อม - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-stat { border-left: 4px solid; }
        .card-pending { border-left-color: #ffc107; }
        .card-in-progress { border-left-color: #0d6efd; }
        .card-completed { border-left-color: #198754; }
        .card-total { border-left-color: #6c757d; }
    </style>
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <h2>รายงานแจ้งซ่อม</h2>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card card-stat card-total">
                    <div class="card-body">
                        <h5 class="card-title">ทั้งหมด</h5>
                        <h3 class="text-center"><?= $total_repairs ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat card-pending">
                    <div class="card-body">
                        <h5 class="card-title">รอดำเนินการ</h5>
                        <h3 class="text-center"><?= $pending_repairs ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat card-in-progress">
                    <div class="card-body">
                        <h5 class="card-title">กำลังดำเนินการ</h5>
                        <h3 class="text-center"><?= $in_progress_repairs ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stat card-completed">
                    <div class="card-body">
                        <h5 class="card-title">เสร็จสิ้น</h5>
                        <h3 class="text-center"><?= $completed_repairs ?></h3>
                    </div>
                </div>
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
                    <table class="table table-striped" id="reportTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>อุปกรณ์</th>
                                <th>ผู้แจ้ง</th>
                                <th>นักศึกษา ID</th>
                                <th>รายละเอียด</th>
                                <th>ช่าง</th>
                                <th>สถานะ</th>
                                <th>วันที่แจ้ง</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($repairs) > 0): ?>
                                <?php foreach ($repairs as $index => $repair): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($repair['equipment_name']) ?></td>
                                        <td><?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']) ?></td>
                                        <td><?= htmlspecialchars($repair['student_id']) ?></td>
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
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">ไม่มีข้อมูลรายงาน</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        แสดง <?= count($repairs) ?> รายการ
                    </div>
                    <button class="btn btn-primary" onclick="window.print()">พิมพ์รายงาน</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>