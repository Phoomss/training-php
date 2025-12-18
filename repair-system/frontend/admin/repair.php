<?php
require_once('../../configs/admin_only.php');
require_once('../../configs/connect.php');

// Fetch all repair requests with related data
$stmt = $conn->prepare("
    SELECT r.*, e.name as equipment_name, s.firstname, s.lastname, t.firstname as tech_firstname, t.lastname as tech_lastname
    FROM repair r
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN student s ON r.student_id = s.id
    LEFT JOIN technical t ON r.technical_id = t.id
    ORDER BY r.created_at DESC
");
$stmt->execute();
$repairs = $stmt->fetchAll();
// var_dump($repairs);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถานะการซ่อม - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-รอซ้อม { color: #ffc107; }
        .status-กำลังซ้อม { color: #0d6efd; }
        .status-เสร็จสิ้น { color: #198754; }
    </style>
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>สถานะการซ่อม</h2>
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
                                <th>ผู้แจ้ง</th>
                                <th>ช่าง</th>
                                <th>สถานะ</th>
                                <th>วันที่แจ้ง</th>
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
                                        <td><?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']) ?></td>
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
                                                        case 'รอซ้อม': echo 'bg-warning text-dark'; break;
                                                        case 'กำลังซ้อม': echo 'bg-primary'; break;
                                                        case 'เสร็จสิ้น': echo 'bg-success'; break;
                                                        default: echo 'bg-secondary';
                                                    } 
                                                ?>
                                            ">
                                                <?= $repair['status'] ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($repair['created_at'])) ?></td>
                                        <td>
                                            <a href="form_repair.php?id=<?= $repair['id'] ?>" 
                                               class="btn btn-sm btn-warning me-1">แก้ไข</a>
                                            <a href="../backend/repair_action.php?delete=<?= $repair['id'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('คุณต้องการลบคำร้องนี้หรือไม่?')">ลบ</a>
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