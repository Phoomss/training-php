<?php
require_once('../../configs/connect.php');
// require_once('../../configs/admin_only.php');

try {
    $stmt = $conn->query("SELECT * FROM equipment ORDER BY name ASC");
    $equipments = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="th">
<head>
    <title>จัดการอุปกรณ์</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body class="bg-light">
    <?php require_once '../layouts/navbar.php'?>

    <main class="container mt-4">
        <!-- Messages -->
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">รายการอุปกรณ์ทั้งหมด</h2>
            <a href="form_equipment.php" class="btn btn-primary">เพิ่มอุปกรณ์ใหม่</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ลำดับ</th>
                        <th>ชื่ออุปกรณ์</th>
                        <th>วันที่เพิ่ม</th>
                        <th>ดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($equipments) > 0): ?>
                        <?php foreach ($equipments as $index => $equipment): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($equipment['name']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($equipment['created_at'])) ?></td>
                                <td>
                                    <a href="form_equipment.php?id=<?= $equipment['id'] ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                    <a href="../../backend/equipment_action.php?delete_equipment=<?= $equipment['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('คุณต้องการลบอุปกรณ์นี้หรือไม่?')">ลบ</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">ไม่มีข้อมูลอุปกรณ์</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

  <?php require_once '../layouts/footer.php'?>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>