<?php
require_once '../../configs/technical_only.php';
require_once '../../configs/connect.php';

// ดึงข้อมูลช่างเทคนิคที่ล็อกอิน
$stmt_tech = $conn->prepare("SELECT * FROM technical WHERE auth_id = :auth_id");
$stmt_tech->execute([':auth_id' => $_SESSION['auth_id']]);
$technical = $stmt_tech->fetch(PDO::FETCH_ASSOC);

// ดึงข้อมูลงานซ่อมที่ได้รับมอบหมาย
$stmt_assigned = $conn->prepare("
    SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name,
           (SELECT rd.status FROM repair_detail rd WHERE rd.repair_id = r.id ORDER BY rd.created_at DESC LIMIT 1) as current_status
    FROM repair r
    JOIN student s ON r.student_id = s.id
    JOIN equipment e ON r.equipment_id = e.id
    JOIN repair_detail rd ON r.id = rd.repair_id
    WHERE rd.technical_id = :technical_id
    GROUP BY r.id
    ORDER BY r.created_at DESC
");
$stmt_assigned->execute([':technical_id' => $technical['id']]);
$assigned_repairs = $stmt_assigned->fetchAll();

// ดึงข้อมูลงานซ่อมทั้งหมดที่ยังไม่ได้มีการจัดการ
$stmt_pending = $conn->prepare("
    SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name
    FROM repair r
    JOIN student s ON r.student_id = s.id
    JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN repair_detail rd ON r.id = rd.repair_id
    WHERE rd.repair_id IS NULL
    ORDER BY r.created_at ASC
    LIMIT 20
");
$stmt_pending->execute();
$pending_repairs = $stmt_pending->fetchAll();
?>

<!doctype html>
<html lang="th">
<head>
    <title>หน้าหลัก | ช่างเทคนิค</title>
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
    <h2>ระบบจัดการงานซ่อม | ช่างเทคนิค</h2>
    
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

    <!-- งานซ่อมที่ได้รับมอบหมาย -->
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="mb-0">งานซ่อมที่ได้รับมอบหมาย</h4>
        </div>
        <div class="card-body">
            <?php if (count($assigned_repairs) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>อุปกรณ์</th>
                                <th>นักศึกษา</th>
                                <th>รายละเอียด</th>
                                <th>สถานะ</th>
                                <th>วันที่แจ้ง</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assigned_repairs as $key => $repair): ?>
                                <tr>
                                    <td><?=++$key?></td>
                                    <td><?=$repair['equipment_name']?></td>
                                    <td><?=$repair['title']?> <?=$repair['firstname']?> <?=$repair['lastname']?></td>
                                    <td><?=htmlspecialchars(substr($repair['details'], 0, 50))?><?php if (strlen($repair['details']) > 50) echo '...'; ?></td>
                                    <td>
                                        <?php
                                        $status = $repair['current_status'] ?? 'รออนุมัติ';
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
                                        <a href="view_repair.php?id=<?=$repair['id']?>" class="btn btn-sm btn-outline-primary">ดูรายละเอียด</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">ยังไม่มีงานซ่อมที่ได้รับมอบหมาย</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- งานซ่อมที่รอดำเนินการ -->
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">งานซ่อมที่รอดำเนินการ</h4>
        </div>
        <div class="card-body">
            <?php if (count($pending_repairs) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>อุปกรณ์</th>
                                <th>นักศึกษา</th>
                                <th>รายละเอียด</th>
                                <th>วันที่แจ้ง</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_repairs as $key => $repair): ?>
                                <tr>
                                    <td><?=++$key?></td>
                                    <td><?=$repair['equipment_name']?></td>
                                    <td><?=$repair['title']?> <?=$repair['firstname']?> <?=$repair['lastname']?></td>
                                    <td><?=htmlspecialchars(substr($repair['details'], 0, 50))?><?php if (strlen($repair['details']) > 50) echo '...'; ?></td>
                                    <td><?=$repair['created_at']?></td>
                                    <td>
                                        <a href="view_repair.php?id=<?=$repair['id']?>" class="btn btn-sm btn-outline-primary">ดูรายละเอียด</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted">ไม่มีงานซ่อมใหม่ที่รอดำเนินการ</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>