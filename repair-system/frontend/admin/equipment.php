<?php
require_once('../../configs/admin_only.php');
require_once('../../configs/connect.php');

// Fetch all equipment
$stmt = $conn->prepare("SELECT * FROM equipment ORDER BY name ASC");
$stmt->execute();
$equipments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการอุปกรณ์ - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>จัดการอุปกรณ์</h2>
            <a href="form_equipment.php" class="btn btn-primary">เพิ่มอุปกรณ์</a>
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
                                <th>ชื่ออุปกรณ์</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($equipments) > 0): ?>
                                <?php foreach ($equipments as $index => $equipment): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($equipment['name']) ?></td>
                                        <td>
                                            <a href="form_equipment.php?id=<?= $equipment['id'] ?>" 
                                               class="btn btn-sm btn-warning me-2">แก้ไข</a>
                                            <a href="../backend/equipment_action.php?delete_equipment=<?= $equipment['id'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('คุณต้องการลบอุปกรณ์นี้หรือไม่?')">ลบ</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">ไม่มีข้อมูลอุปกรณ์</td>
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