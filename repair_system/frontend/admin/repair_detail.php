<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$repair_id = filter_input(INPUT_GET, 'repair_id', FILTER_VALIDATE_INT);

if (!$repair_id || $repair_id <= 0) {
    header("Location: repair.php");
    exit;
}

// Fetch repair details
$stmt = $conn->prepare("
    SELECT
        r.id,
        CONCAT(u.title, ' ', u.firstname, ' ', u.lastname) as user_name,
        e.name as equipment_name,
        ld.name as location_detail_name,
        l.name as location_name,
        r.image,
        r.created_at,
        r.updated_at
    FROM repair r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN location_detail ld ON r.locationD_id = ld.id
    LEFT JOIN location l ON ld.location_id = l.id
    WHERE r.id = :repair_id
");
$stmt->execute([':repair_id' => $repair_id]);
$repair = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$repair) {
    header("Location: repair.php");
    exit;
}

// Fetch all repair details for this repair
$detailStmt = $conn->prepare("
    SELECT 
        rd.*,
        t.username as technician_name,
        s.username as staff_name
    FROM repair_detail rd
    LEFT JOIN users t ON rd.technical_id = t.id
    LEFT JOIN users s ON rd.staff_id = s.id
    WHERE rd.repair_id = :repair_id
    ORDER BY rd.created_at DESC
");
$detailStmt->execute([':repair_id' => $repair_id]);
$repair_details = $detailStmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Repair Detail</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .repair-image {
            max-height: 300px;
            object-fit: contain;
            border-radius: 5px;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
        }
        .status-waiting {
            background-color: #ffc107;
            color: #000;
        }
        .status-progress {
            background-color: #0d6efd;
            color: white;
        }
        .status-complete {
            background-color: #198754;
            color: white;
        }
        .timeline {
            position: relative;
            padding-left: 30px;
            margin-left: 10px;
            border-left: 2px solid #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
            padding-left: 20px;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -26px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #0d6efd;
        }
    </style>
</head>

<body>
    <?php require_once '../layouts/navbar.php' ?>

    <main class="container mt-5">
        
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
        
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="bi bi-receipt"></i> รายละเอียดการแจ้งซ่อม #<?= $repair['id'] ?></h4>
                    </div>
                    <div class="card-body">
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">ผู้แจ้ง</h6>
                                <p><?= htmlspecialchars($repair['user_name'] ?? 'N/A') ?></p>
                                
                                <h6 class="text-muted">อุปกรณ์</h6>
                                <p><?= htmlspecialchars($repair['equipment_name'] ?? 'N/A') ?></p>
                                
                                <h6 class="text-muted">ตำแหน่ง</h6>
                                <p>
                                    <?= htmlspecialchars($repair['location_name'] ?? 'N/A') ?> - 
                                    <?= htmlspecialchars($repair['location_detail_name'] ?? 'N/A') ?>
                                </p>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-muted">วันที่แจ้ง</h6>
                                <p><?= date('d/m/Y H:i', strtotime($repair['created_at'])) ?></p>
                                
                                <h6 class="text-muted">วันที่อัพเดตล่าสุด</h6>
                                <p><?= !empty($repair['updated_at']) ? date('d/m/Y H:i', strtotime($repair['updated_at'])) : '-' ?></p>
                                
                                <h6 class="text-muted">สถานะปัจจุบัน</h6>
                                <?php if (!empty($repair_details) && isset($repair_details[0]['status'])): ?>
                                    <p>
                                        <span class="status-badge 
                                            <?php 
                                                if ($repair_details[0]['status'] === 'รอซ่อม') echo 'status-waiting';
                                                elseif ($repair_details[0]['status'] === 'กำลังซ่อม') echo 'status-progress';
                                                else echo 'status-complete';
                                            ?>
                                        ">
                                            <?= htmlspecialchars($repair_details[0]['status']) ?>
                                        </span>
                                    </p>
                                <?php else: ?>
                                    <p><span class="status-badge status-waiting">รออนุมัติ</span></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>รูปภาพที่แนบ</h5>
                            <div class="border rounded p-3">
                                <?php if (!empty($repair['image']) && file_exists('../../' . $repair['image'])): ?>
                                    <img src="../../<?= $repair['image'] ?>" alt="Repair Image" class="repair-image img-fluid img-thumbnail">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <span class="text-muted">ไม่มีรูปภาพที่แนบมา</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Assign Repair Section -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="bi bi-person-plus"></i> มอบหมายงานซ่อม</h5>
                    </div>
                    <div class="card-body">
                        <form action="../../backend/repair_detail_action.php" method="POST">
                            <input type="hidden" name="repair_id" value="<?= $repair['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">ช่างเทคนิค</label>
                                <select class="form-select" name="technical_id" required>
                                    <option value="">-- เลือกช่างเทคนิค --</option>
                                    <?php foreach ($technicians as $tech): ?>
                                        <option value="<?= $tech['id'] ?>"><?= htmlspecialchars($tech['username']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">เจ้าหน้าที่</label>
                                <select class="form-select" name="staff_id" required>
                                    <option value="">-- เลือกเจ้าหน้าที่ --</option>
                                    <?php foreach ($staff_list as $staff): ?>
                                        <option value="<?= $staff['id'] ?>"><?= htmlspecialchars($staff['username']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">สถานะ</label>
                                <select class="form-select" name="status" required>
                                    <option value="รอซ่อม">รอซ่อม</option>
                                    <option value="กำลังซ่อม">กำลังซ่อม</option>
                                    <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                                </select>
                            </div>
                            
                            <button type="submit" name="add_repair_detail" class="btn btn-success w-100">
                                <i class="bi bi-save"></i> บันทึกการมอบหมาย
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Timeline Section -->
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-clock-history"></i> ประวัติการดำเนินการ</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($repair_details)): ?>
                            <p class="text-muted">ยังไม่มีประวัติการดำเนินการ</p>
                        <?php else: ?>
                            <div class="timeline">
                                <?php foreach ($repair_details as $detail): ?>
                                    <div class="timeline-item">
                                        <strong>
                                            <?= htmlspecialchars($detail['status']) ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            โดย <?= htmlspecialchars($detail['technician_name'] ?? 'N/A') ?> และ 
                                            <?= htmlspecialchars($detail['staff_name'] ?? 'N/A') ?>
                                        </small>
                                        <br>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($detail['created_at'])) ?>
                                        </small>
                                        
                                        <!-- Action buttons for editing/deleting -->
                                        <div class="mt-2">
                                            <a href="form_repair_detail.php?id=<?= $detail['id'] ?>&repair_id=<?= $repair['id'] ?>" 
                                               class="btn btn-sm btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i> แก้ไข
                                            </a>
                                            <a href="../../backend/repair_detail_action.php?delete_repair_detail=<?= $detail['id'] ?>&repair_id=<?= $repair['id'] ?>" 
                                               class="btn btn-sm btn-danger btn-sm" 
                                               onclick="return confirm('ยืนยันการลบ?')">
                                                <i class="bi bi-trash"></i> ลบ
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <a href="repair.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> กลับไปยังรายการแจ้งซ่อม
            </a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>