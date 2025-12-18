<?php
require_once('../../configs/connect.php');
session_start();

// Check if user is a student
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../../index.php?error=' . urlencode('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'));
    exit();
}

$student_id = $_SESSION['auth_id'];

// Fetch all equipment for the form
$stmt = $conn->prepare("SELECT * FROM equipment ORDER BY name ASC");
$stmt->execute();
$equipments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แจ้งซ่อม - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>แจ้งซ่อมอุปกรณ์</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($_GET['error']) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../../backend/repair_action.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="student_id" value="<?= $student_id ?>">

                            <div class="mb-3">
                                <label for="equipment_id" class="form-label">เลือกอุปกรณ์ <span class="text-danger">*</span></label>
                                <select class="form-select" id="equipment_id" name="equipment_id" required>
                                    <option value="">-- เลือกอุปกรณ์ --</option>
                                    <?php foreach ($equipments as $equipment): ?>
                                        <option value="<?= $equipment['id'] ?>"><?= htmlspecialchars($equipment['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="details" class="form-label">รายละเอียดการเสีย <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="details" name="details" rows="4" 
                                          placeholder="กรอกรายละเอียดอาการเสียของอุปกรณ์..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">รูปภาพประกอบ (ถ้ามี)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">เฉพาะไฟล์ภาพ JPG, JPEG, PNG</div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="my-repairs.php" class="btn btn-secondary me-md-2">ยกเลิก</a>
                                <button type="submit" class="btn btn-primary" name="add_repair">แจ้งซ่อม</button>
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