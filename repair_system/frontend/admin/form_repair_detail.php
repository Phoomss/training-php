<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$repair_id = filter_input(INPUT_GET, 'repair_id', FILTER_VALIDATE_INT);

if (!$id || !$repair_id || $id <= 0 || $repair_id <= 0) {
    header("Location: repair.php");
    exit;
}

// Fetch repair detail for editing
$stmt = $conn->prepare("
    SELECT
        rd.*,
        r.id as repair_main_id,
        r.created_at as repair_created_at,
        u.username as user_name,
        e.name as equipment_name
    FROM repair_detail rd
    LEFT JOIN repairs r ON rd.repair_id = r.id
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN equipment e ON r.equipment_id = e.id
    WHERE rd.id = :id
");
$stmt->execute([':id' => $id]);
$repair_detail = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$repair_detail) {
    header("Location: repair.php");
    exit;
}

// Fetch repair info for display
$repairStmt = $conn->prepare("
    SELECT 
        r.id,
        u.username as user_name,
        e.name as equipment_name,
        ld.name as location_detail_name,
        l.name as location_name
    FROM repairs r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN location_detail ld ON r.locationD_id = ld.id
    LEFT JOIN location l ON ld.location_id = l.id
    WHERE r.id = :repair_id
");
$repairStmt->execute([':repair_id' => $repair_id]);
$repair_info = $repairStmt->fetch(PDO::FETCH_ASSOC);

// Fetch technicians and staff for assignment dropdowns
$techStmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'technical' ORDER BY username ASC");
$techStmt->execute();
$technicians = $techStmt->fetchAll(PDO::FETCH_ASSOC);

$staffStmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'staff' ORDER BY username ASC");
$staffStmt->execute();
$staff_list = $staffStmt->fetchAll(PDO::FETCH_ASSOC);

$success_message = $_GET['status'] ?? '';
$error_message = $_GET['error'] ?? '';
?>

<!doctype html>
<html lang="en">

<head>
    <title>Edit Repair Assignment</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php require_once '../layouts/navbar.php' ?>

    <main class="container mt-5">
        <div class="card shadow p-4">
            <h3 class="mb-4"><i class="bi bi-pencil-square"></i> แก้ไขการมอบหมายงานซ่อม</h3>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Repair Summary Card -->
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">ข้อมูลการแจ้งซ่อม #<?= $repair_info['id'] ?></h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ผู้แจ้ง:</strong> <?= htmlspecialchars($repair_info['user_name'] ?? 'N/A') ?></p>
                            <p><strong>อุปกรณ์:</strong> <?= htmlspecialchars($repair_info['equipment_name'] ?? 'N/A') ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>ตำแหน่ง:</strong> 
                                <?= htmlspecialchars($repair_info['location_name'] ?? 'N/A') ?> - 
                                <?= htmlspecialchars($repair_info['location_detail_name'] ?? 'N/A') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="../../backend/repair_detail_action.php" method="POST">
                <input type="hidden" name="id" value="<?= $repair_detail['id'] ?>">
                <input type="hidden" name="repair_id" value="<?= $repair_detail['repair_id'] ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">ช่างเทคนิค *</label>
                            <select class="form-select" name="technical_id" required>
                                <option value="">-- เลือกช่างเทคนิค --</option>
                                <?php foreach ($technicians as $tech): ?>
                                    <option value="<?= $tech['id'] ?>" 
                                        <?= $repair_detail['technical_id'] == $tech['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tech['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">เจ้าหน้าที่ *</label>
                            <select class="form-select" name="staff_id" required>
                                <option value="">-- เลือกเจ้าหน้าที่ --</option>
                                <?php foreach ($staff_list as $staff): ?>
                                    <option value="<?= $staff['id'] ?>" 
                                        <?= $repair_detail['staff_id'] == $staff['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($staff['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">สถานะ *</label>
                    <select class="form-select" name="status" required>
                        <option value="รอซ่อม" <?= $repair_detail['status'] === 'รอซ่อม' ? 'selected' : '' ?>>รอซ่อม</option>
                        <option value="กำลังซ่อม" <?= $repair_detail['status'] === 'กำลังซ่อม' ? 'selected' : '' ?>>กำลังซ่อม</option>
                        <option value="เสร็จสิ้น" <?= $repair_detail['status'] === 'เสร็จสิ้น' ? 'selected' : '' ?>>เสร็จสิ้น</option>
                    </select>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" name="update_repair_detail" class="btn btn-primary">
                        <i class="bi bi-save"></i> อัปเดตการมอบหมาย
                    </button>
                    <a href="repair_detail.php?repair_id=<?= $repair_id ?>" class="btn btn-secondary">
                        <i class="bi bi-x"></i> ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>