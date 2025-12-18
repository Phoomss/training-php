<?php
require_once '../../configs/student_only.php';
require_once '../../configs/connect.php';

$view_id = isset($_GET['view']) ? (int)$_GET['view'] : 0;

if ($view_id > 0) {
    $stmt = $conn->prepare("
        SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name
        FROM repair r
        JOIN student s ON r.student_id = s.id
        JOIN equipment e ON r.equipment_id = e.id
        WHERE r.id = :id AND s.auth_id = :auth_id
    ");
    $stmt->execute([
        ':id' => $view_id,
        ':auth_id' => $_SESSION['auth_id']
    ]);
    $repair = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$repair) {
        header('Location: index.php?error=' . urlencode('ไม่พบข้อมูลรายการแจ้งซ่อม'));
        exit();
    }

    // Get repair details history
    $stmt_details = $conn->prepare("
        SELECT rd.*, t.title as tech_title, t.firstname as tech_firstname, t.lastname as tech_lastname
        FROM repair_detail rd
        LEFT JOIN technical t ON rd.technical_id = t.id
        WHERE rd.repair_id = :repair_id
        ORDER BY rd.created_at ASC
    ");
    $stmt_details->execute([':repair_id' => $view_id]);
    $repair_details = $stmt_details->fetchAll();
} else {
    header('Location: index.php');
    exit();
}
?>

<!doctype html>
<html lang="th">
<head>
    <title>รายละเอียดการแจ้งซ่อม | นักศึกษา</title>
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
            <span class="navbar-text me-3">ยินดีต้อนรับ, <?=$_SESSION['username']?></span>
            <a class="nav-link" href="../../logout.php">ออกจากระบบ</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <a href="index.php" class="btn btn-secondary mb-3">&laquo; กลับไปยังรายการแจ้งซ่อม</a>
    
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">รายละเอียดการแจ้งซ่อม #<?=$repair['id']?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>อุปกรณ์:</strong> <?=$repair['equipment_name']?></p>
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
                    $current_status_stmt->execute([':repair_id' => $view_id]);
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
            
            <div class="mt-3">
                <a href="form_repair.php?id=<?=$repair['id']?>" class="btn btn-primary">แก้ไขรายการ</a>
                <a href="index.php" class="btn btn-secondary">กลับไปยังรายการ</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>