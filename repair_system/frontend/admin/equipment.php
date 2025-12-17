<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$stmt = $conn->prepare("
    SELECT
        equipment.id,
        equipment.name AS equipment_name,
        categories.name AS category_name,
        equipment.created_at
    FROM equipment
    LEFT JOIN categories ON equipment.category_id = categories.id
    ORDER BY equipment.id DESC
");
$stmt->execute();

$equipment = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
    <title>Equipment List</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../layouts/navbar.php' ?>

    <main class="container mt-5">
        <div class="card shadow p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>📦 รายการอุปกรณ์</h3>
                <a href="form_equipment.php" class="btn btn-primary">➕ เพิ่มอุปกรณ์</a>
            </div>

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>ชื่ออุปกรณ์</th>
                        <th>หมวดหมู่</th>
                        <th>วันที่เพิ่ม</th>
                        <th width="150">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($equipment)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">ไม่มีข้อมูล</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($equipment as $i => $row): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($row['equipment_name']) ?></td>
                                <td><?= htmlspecialchars($row['category_name'] ?? '-') ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td>
                                    <a href="form_equipment.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                                    <a href="../../backend/equipment_action.php?delete_equipment=<?= $row['id'] ?>"
                                        class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ?')">
                                        ลบ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>