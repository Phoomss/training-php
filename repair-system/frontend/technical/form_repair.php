<?php
require_once '../../configs/technical_only.php';
require_once '../../configs/connect.php';

// ดึงข้อมูลช่างเทคนิคที่ล็อกอิน
$stmt_tech = $conn->prepare("SELECT * FROM technical WHERE auth_id = :auth_id");
$stmt_tech->execute([':auth_id' => $_SESSION['auth_id']]);
$technical = $stmt_tech->fetch(PDO::FETCH_ASSOC);

$repair_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($repair_id <= 0) {
    header('Location: index.php?error=' . urlencode('ไม่พบข้อมูลการแจ้งซ่อม'));
    exit();
}

// ดึงข้อมูลการแจ้งซ่อม
$stmt = $conn->prepare("
    SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name
    FROM repair r
    JOIN student s ON r.student_id = s.id
    JOIN equipment e ON r.equipment_id = e.id
    WHERE r.id = :id
");
$stmt->execute([':id' => $repair_id]);
$repair = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$repair) {
    header('Location: index.php?error=' . urlencode('ไม่พบข้อมูลการแจ้งซ่อม'));
    exit();
}

// ดึงประวัติสถานะการซ่อม
$stmt_details = $conn->prepare("
    SELECT rd.*, t.title as tech_title, t.firstname as tech_firstname, t.lastname as tech_lastname
    FROM repair_detail rd
    LEFT JOIN technical t ON rd.technical_id = t.id
    WHERE rd.repair_id = :repair_id
    ORDER BY rd.created_at ASC
");
$stmt_details->execute([':repair_id' => $repair_id]);
$repair_details = $stmt_details->fetchAll();
?>

<!doctype html>
<html lang="th">
<head>
    <title>จัดการงานซ่อม #<?=$repair['id']?> | ช่างเทคนิค</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">ระบบแจ้งซ่อมอุปกรณ์</a>
        
        <div class="navbar-nav ms-auto">
            <span class="navbar-text me-3">ยินดีต้อนรับ, <?=$technical['title']?> <?=$technical['firstname']?> <?=$technical['lastname']?></span>
            <a class="nav-link" href="../../logout.php">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">&laquo; กลับไปยังรายการงานซ่อม</a>
    
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">จัดการงานซ่อม #<?=$repair['id']?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>ข้อมูลการแจ้งซ่อม</h5>
                    <p><strong>อุปกรณ์:</strong> <?=$repair['equipment_name']?></p>
                    <p><strong>นักศึกษา:</strong> <?=$repair['title']?> <?=$repair['firstname']?> <?=$repair['lastname']?></p>
                    <p><strong>รายละเอียด:</strong> <?=htmlspecialchars($repair['details'])?></p>
                    <p><strong>วันที่แจ้ง:</strong> <?=$repair['created_at']?></p>
                    
                    <!-- Show image if exists -->
                    <?php if ($repair['image']): ?>
                        <p><strong>รูปภาพ:</strong></p>
                        <img src="../../<?=$repair['image']?>" alt="ภาพประกอบการแจ้งซ่อม" class="img-fluid rounded border" style="max-height: 300px;">
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6">
                    <h5>สถานะปัจจุบัน</h5>
                    <?php
                    // Determine current status
                    $current_status_stmt = $conn->prepare("
                        SELECT rd.status, rd.created_at, t.title, t.firstname, t.lastname
                        FROM repair_detail rd
                        LEFT JOIN technical t ON rd.technical_id = t.id
                        WHERE rd.repair_id = :repair_id
                        ORDER BY rd.created_at DESC
                        LIMIT 1
                    ");
                    $current_status_stmt->execute([':repair_id' => $repair_id]);
                    $current_status = $current_status_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($current_status):
                    ?>
                        <div class="alert alert-info">
                            <h6>สถานะ: <?=$current_status['status']?></h6>
                            <small>อัปเดตเมื่อ: <?=$current_status['created_at']?></small>
                            <?php if ($current_status['title']): ?>
                                <br><small>โดย: <?=$current_status['title']?> <?=$current_status['firstname']?> <?=$current_status['lastname']?></small>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-secondary">
                            <h6>สถานะ: รออนุมัติ</h6>
                        </div>
                    <?php endif; ?>
                    
                    <h5>ประวัติการอัปเดต</h5>
                    <?php if (count($repair_details) > 0): ?>
                        <div class="timeline">
                            <?php foreach ($repair_details as $detail): ?>
                                <div class="border-start ps-3 mb-2" style="border-left: 3px solid #007bff !important;">
                                    <small><?=$detail['created_at']?> - <?=$detail['status']?></small>
                                    <?php if ($detail['tech_title']): ?>
                                        <br><small>โดย: <?=$detail['tech_title']?> <?=$detail['tech_firstname']?> <?=$detail['tech_lastname']?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่มีประวัติการอัปเดต</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-md-6">
                    <h5>อัปเดตสถานะ</h5>
                    <form action="../../backend/repair_detail_action.php" method="post">
                        <input type="hidden" name="from_technical" value="true">
                        <input type="hidden" name="repair_id" value="<?=$repair['id']?>">
                        <input type="hidden" name="technical_id" value="<?=$technical['id']?>">
                        
                        <div class="mb-3">
                            <label class="form-label">สถานะใหม่</label>
                            <select name="status" class="form-select" required>
                                <option value="">เลือกสถานะ</option>
                                <option value="รอซ่อม">รอซ่อม</option>
                                <option value="กำลังซ่อม">กำลังซ่อม</option>
                                <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="update_status" class="btn btn-primary">อัปเดตสถานะ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>