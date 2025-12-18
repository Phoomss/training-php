<?php
require_once('../../configs/admin_only.php');
require_once('../../configs/connect.php');

$equipment_id = $_GET['id'] ?? null;
$equipment = null;

if ($equipment_id) {
    // Update mode - fetch existing equipment data
    $stmt = $conn->prepare("SELECT * FROM equipment WHERE id = :id");
    $stmt->execute([':id' => $equipment_id]);
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$equipment) {
        header("Location: equipment.php?error=" . urlencode("ไม่พบข้อมูลอุปกรณ์"));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $equipment ? 'แก้ไขอุปกรณ์' : 'เพิ่มอุปกรณ์' ?> - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4><?= $equipment ? 'แก้ไขอุปกรณ์' : 'เพิ่มอุปกรณ์' ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_GET['error']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../backend/equipment_action.php" method="post">
                            <?php if ($equipment): ?>
                                <input type="hidden" name="id" value="<?= $equipment['id'] ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="name" class="form-label">ชื่ออุปกรณ์</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?= $equipment ? htmlspecialchars($equipment['name']) : '' ?>" 
                                       required>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="equipment.php" class="btn btn-secondary me-md-2">ยกเลิก</a>
                                <button type="submit" 
                                        class="btn btn-primary" 
                                        name="<?= $equipment ? 'update_equipment' : 'add_equipment' ?>">
                                    <?= $equipment ? 'อัปเดต' : 'เพิ่ม' ?>อุปกรณ์
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>