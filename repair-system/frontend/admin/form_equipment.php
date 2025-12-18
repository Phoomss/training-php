<?php
// require_once '../configs/admin_only.php';
require_once '../../configs/connect.php';

$id = $_GET['id'] ?? null;
$equipment = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM equiment WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipment) {
        header('Location: equipment.php');
        exit();
    }
}

$isEdit = $equipment !== null;
?>

<!doctype html>
<html lang="th">
<head>
    <title><?= $isEdit ? 'แก้ไขอุปกรณ์' : 'เพิ่มอุปกรณ์' ?> | ระบบแจ้งซ่อม</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-info-subtle">

<?php require_once '../layouts/navbar.php'; ?>

<main class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-lg-4 col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <i class="bi bi-tools fs-1 text-primary"></i>
                        <h4 class="mt-2 mb-0 fw-bold">
                            <?= $isEdit ? 'แก้ไขอุปกรณ์' : 'เพิ่มอุปกรณ์' ?>
                        </h4>
                        <small class="text-muted">ระบบแจ้งซ่อมอุปกรณ์</small>
                    </div>

                    <form action="../../backend/equipment_action.php" method="post">

                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?= $equipment['id'] ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">ชื่ออุปกรณ์</label>
                            <input
                                type="text"
                                class="form-control form-control-lg"
                                name="name"
                                value="<?= htmlspecialchars($equipment['name'] ?? '') ?>"
                                placeholder="เช่น เครื่องฉาย, คอมพิวเตอร์"
                                required>
                        </div>

                        <div class="d-grid">
                            <button
                                type="submit"
                                name="<?= $isEdit ? 'update_equiment' : 'add_equiment' ?>"
                                class="btn btn-<?= $isEdit ? 'warning' : 'primary' ?> btn-lg">
                                <i class="bi <?= $isEdit ? 'bi-pencil-square' : 'bi-plus-circle' ?>"></i>
                                <?= $isEdit ? 'อัปเดตอุปกรณ์' : 'เพิ่มอุปกรณ์' ?>
                            </button>
                        </div>

                        <?php if ($isEdit): ?>
                            <div class="text-center mt-3">
                                <a href="equipment.php" class="text-decoration-none">
                                    ← กลับไปหน้ารายการอุปกรณ์
                                </a>
                            </div>
                        <?php endif; ?>

                    </form>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
