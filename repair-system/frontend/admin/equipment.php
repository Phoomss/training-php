<?php
require_once '../../configs/connect.php';

$stmt = $conn->query("SELECT * FROM equiment ORDER BY id DESC");
$equipments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="th">
<head>
    <title>จัดการอุปกรณ์</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    />
</head>

<body>

<?php require_once '../layouts/navbar.php'; ?>

<main class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>📦 จัดการอุปกรณ์</h4>
        <a href="form_equipment.php" class="btn btn-primary">
            ➕ เพิ่มอุปกรณ์
        </a>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>ชื่ออุปกรณ์</th>
                <th width="160">จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($equipments)): ?>
                <?php foreach ($equipments as $i => $eq): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($eq['name']) ?></td>
                        <td>
                            <!-- EDIT -->
                            <a href="form_equipment.php?id=<?= $eq['id'] ?>"
                               class="btn btn-sm btn-warning">
                                แก้ไข
                            </a>

                            <!-- DELETE -->
                            <a href="../../backend/equipment_action.php?delete_equiment=<?= $eq['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('ลบอุปกรณ์นี้หรือไม่?')">
                               ลบ
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        ไม่มีข้อมูลอุปกรณ์
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
