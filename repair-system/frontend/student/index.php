<?php
require_once '../../configs/student_only.php';
require_once '../../configs/connect.php';

$stmt = $conn->prepare("
    SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name
    FROM repair r
    JOIN student s ON r.student_id = s.id
    JOIN equipment e ON r.equipment_id = e.id
    WHERE s.auth_id = :auth_id
    ORDER BY r.created_at DESC
");
$stmt->execute([':auth_id' => $_SESSION['auth_id']]);
$repairs = $stmt->fetchAll();
?>

<!doctype html>
<html lang="th">
<head>
    <title>หน้าหลัก | นักศึกษา</title>
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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>รายการแจ้งซ่อมของฉัน</h2>
        <a href="form_repair.php" class="btn btn-success">+ เพิ่มรายการแจ้งซ่อม</a>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?=htmlspecialchars($_GET['status'])?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?=htmlspecialchars($_GET['error'])?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (count($repairs) > 0): ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>อุปกรณ์</th>
                        <th>รายละเอียด</th>
                        <th>สถานะ</th>
                        <th>วันที่แจ้ง</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($repairs as $key => $repair): ?>
                        <tr>
                            <td><?=++$key?></td>
                            <td><?=$repair['equipment_name']?></td>
                            <td><?=htmlspecialchars($repair['details'])?></td>
                            <td>
                                <?php
                                // Check status from repair_detail table
                                $stmt_status = $conn->prepare("
                                    SELECT rd.status
                                    FROM repair_detail rd
                                    WHERE rd.repair_id = :repair_id
                                    ORDER BY rd.created_at DESC
                                    LIMIT 1
                                ");
                                $stmt_status->execute([':repair_id' => $repair['id']]);
                                $status_row = $stmt_status->fetch(PDO::FETCH_ASSOC);
                                
                                $status = $status_row ? $status_row['status'] : 'รออนุมัติ';
                                $badge_class = '';
                                switch($status) {
                                    case 'รออนุมัติ': $badge_class = 'bg-secondary'; break;
                                    case 'รอซ่อม': $badge_class = 'bg-warning text-dark'; break;
                                    case 'กำลังซ่อม': $badge_class = 'bg-info'; break;
                                    case 'เสร็จสิ้น': $badge_class = 'bg-success'; break;
                                    default: $badge_class = 'bg-secondary';
                                }
                                ?>
                                <span class="badge <?=$badge_class?>"><?=$status?></span>
                            </td>
                            <td><?=$repair['created_at']?></td>
                            <td>
                                <a href="form_repair.php?id=<?=$repair['id']?>" class="btn btn-sm btn-outline-primary">แก้ไข</a>
                                <a href="repair.php?view=<?=$repair['id']?>" class="btn btn-sm btn-outline-info">ดูรายละเอียด</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">ยังไม่มีรายการแจ้งซ่อม</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>