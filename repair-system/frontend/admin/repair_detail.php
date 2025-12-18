<?php
require_once('../../configs/admin_only.php');
require_once('../../configs/connect.php');

// Fetch all repair details with related data
$stmt = $conn->prepare("
    SELECT rd.*, r.details as repair_details, e.name as equipment_name, s.firstname, s.lastname, t.firstname as tech_firstname, t.lastname as tech_lastname
    FROM repair_detail rd
    LEFT JOIN repair r ON rd.repair_id = r.id
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN student s ON r.student_id = s.id
    LEFT JOIN technical t ON rd.technical_id = t.id
    ORDER BY rd.created_at DESC
");
$stmt->execute();
$repair_details = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการซ่อม - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>รายละเอียดการซ่อม</h2>
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
                                <th>คำร้อง</th>
                                <th>อุปกรณ์</th>
                                <th>ผู้แจ้ง</th>
                                <th>ช่าง</th>
                                <th>สถานะ</th>
                                <th>วันที่</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($repair_details) > 0): ?>
                                <?php foreach ($repair_details as $index => $detail): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>#<?= $detail['repair_id'] ?></td>
                                        <td><?= htmlspecialchars($detail['equipment_name']) ?></td>
                                        <td><?= htmlspecialchars($detail['firstname'] . ' ' . $detail['lastname']) ?></td>
                                        <td><?= htmlspecialchars($detail['tech_firstname'] . ' ' . $detail['tech_lastname']) ?></td>
                                        <td><?= htmlspecialchars($detail['status']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($detail['created_at'])) ?></td>
                                        <td>
                                            <a href="../backend/repair_detail_action.php?delete_repair_detail=<?= $detail['id'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('คุณต้องการลบรายละเอียดนี้หรือไม่?')">ลบ</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">ไม่มีข้อมูลรายละเอียดการซ่อม</td>
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