<?php
require_once('../../configs/connect.php');
session_start();

// Check if user is a technical staff
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'technical') {
    header('Location: ../../index.php?error=' . urlencode('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'));
    exit();
}

$repair_id = $_GET['id'] ?? null;
$technical_id = $_SESSION['auth_id'];

if (!$repair_id) {
    header('Location: repairs.php?error=' . urlencode('ไม่พบข้อมูลคำร้อง'));
    exit();
}

// Get technical staff info
$tech_stmt = $conn->prepare("SELECT * FROM technical WHERE auth_id = :auth_id");
$tech_stmt->execute([':auth_id' => $technical_id]);
$technical = $tech_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch repair details with related data
$stmt = $conn->prepare("
    SELECT r.*, e.name as equipment_name, s.firstname, s.lastname, s.student_id, t.firstname as tech_firstname, t.lastname as tech_lastname
    FROM repair r
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN student s ON r.student_id = s.id
    LEFT JOIN technical t ON r.technical_id = t.id
    WHERE r.id = :id
    LIMIT 1
");
$stmt->execute([':id' => $repair_id]);
$repair = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$repair) {
    header('Location: repairs.php?error=' . urlencode('ไม่พบข้อมูลคำร้อง'));
    exit();
}

// If the repair isn't assigned to this technical, assign it
if (!$repair['technical_id']) {
    $assign_stmt = $conn->prepare("UPDATE repair SET technical_id = :technical_id, status = 'in_progress' WHERE id = :id");
    $assign_stmt->execute([':technical_id' => $technical_id, ':id' => $repair_id]);
    
    // Refresh the repair data
    $stmt->execute([':id' => $repair_id]);
    $repair = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดำเนินการแจ้งซ่อม - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>ดำเนินการแจ้งซ่อม</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>รหัสคำร้อง:</strong> <?= $repair['id'] ?>
                            </div>
                            <div class="col-md-6">
                                <strong>วันที่แจ้ง:</strong> <?= date('d/m/Y H:i', strtotime($repair['created_at'])) ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>อุปกรณ์:</strong> <?= htmlspecialchars($repair['equipment_name']) ?>
                            </div>
                            <div class="col-md-6">
                                <strong>สถานะ:</strong> 
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
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>ผู้แจ้ง:</strong> 
                                <?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']) ?>
                            </div>
                            <div class="col-md-6">
                                <strong>นักศึกษา ID:</strong> 
                                <?= htmlspecialchars($repair['student_id']) ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <strong>รายละเอียด:</strong>
                            <p class="border p-3 bg-light"><?= htmlspecialchars($repair['details']) ?></p>
                        </div>

                        <?php if ($repair['image']): ?>
                            <div class="mb-3">
                                <strong>รูปภาพประกอบ:</strong>
                                <div class="mt-2">
                                    <img src="../../<?= htmlspecialchars($repair['image']) ?>" 
                                         alt="รูปภาพประกอบ" class="img-fluid rounded" style="max-width: 100%;">
                                </div>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <form action="../../backend/repair_action.php" method="post">
                            <input type="hidden" name="id" value="<?= $repair['id'] ?>">
                            <input type="hidden" name="student_id" value="<?= $repair['student_id'] ?>">
                            <input type="hidden" name="equipment_id" value="<?= $repair['equipment_id'] ?>">
                            <input type="hidden" name="details" value="<?= htmlspecialchars($repair['details']) ?>">
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">สถานะการซ่อม</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="in_progress" <?= $repair['status'] === 'in_progress' ? 'selected' : '' ?>>กำลังดำเนินการ</option>
                                    <option value="completed" <?= $repair['status'] === 'completed' ? 'selected' : '' ?>>เสร็จสิ้น</option>
                                    <option value="rejected" <?= $repair['status'] === 'rejected' ? 'selected' : '' ?>>ยกเลิก/ไม่สามารถซ่อมได้</option>
                                </select>
                            </div>

                            <input type="hidden" name="technical_id" value="<?= $technical_id ?>">

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="repairs.php" class="btn btn-secondary me-md-2">กลับ</a>
                                <button type="submit" class="btn btn-primary" name="update_repair">อัปเดตสถานะ</button>
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