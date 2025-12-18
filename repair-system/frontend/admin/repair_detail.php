<?php
require_once '../../configs/admin_only.php';
require_once '../../configs/connect.php';

$repair_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($repair_id <= 0) {
    header('Location: repairs.php?error=' . urlencode('ไม่พบข้อมูลการแจ้งซ่อม'));
    exit();
}

// ดึงข้อมูลการแจ้งซ่อม
$stmt = $conn->prepare("
    SELECT r.*, s.title as student_title, s.firstname as student_firstname, s.lastname as student_lastname,
           e.name as equipment_name
    FROM repair r
    JOIN student s ON r.student_id = s.id
    JOIN equipment e ON r.equipment_id = e.id
    WHERE r.id = :id
");
$stmt->execute([':id' => $repair_id]);
$repair = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$repair) {
    header('Location: repairs.php?error=' . urlencode('ไม่พบข้อมูลการแจ้งซ่อม'));
    exit();
}

// ดึงข้อมูลช่างเทคนิคทั้งหมด
$stmt_tech = $conn->prepare("SELECT * FROM technical ORDER BY firstname ASC");
$stmt_tech->execute();
$technicals = $stmt_tech->fetchAll();

// ดึงประวัติสถานะการซ่อมทั้งหมด
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
    <title>จัดการรายละเอียดการซ่อม #<?=$repair['id']?> | ผู้ดูแลระบบ</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php require_once '../layouts/navbar.php'; ?>

<div class="container mt-4">
    <a href="repairs.php" class="btn btn-secondary mb-3">&laquo; กลับไปยังรายการแจ้งซ่อม</a>
    
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">รายละเอียดการแจ้งซ่อม #<?=$repair['id']?></h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>ข้อมูลการแจ้งซ่อม</h5>
                    <p><strong>อุปกรณ์:</strong> <?=$repair['equipment_name']?></p>
                    <p><strong>นักศึกษา:</strong> <?=$repair['student_title']?> <?=$repair['student_firstname']?> <?=$repair['student_lastname']?></p>
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
                    <h5>เพิ่ม/แก้ไขรายละเอียดการซ่อม</h5>
                    <form action="../../backend/repair_detail_action.php" method="post">
                        <input type="hidden" name="repair_id" value="<?=$repair['id']?>">
                        
                        <div class="mb-3">
                            <label class="form-label">ช่างเทคนิค</label>
                            <select name="technical_id" class="form-select" required>
                                <option value="">เลือกช่างเทคนิค</option>
                                <?php foreach ($technicals as $tech): ?>
                                    <option value="<?=$tech['id']?>">
                                        <?=$tech['title']?> <?=$tech['firstname']?> <?=$tech['lastname']?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select" required>
                                <option value="">เลือกสถานะ</option>
                                <option value="รอซ่อม">รอซ่อม</option>
                                <option value="กำลังซ่อม">กำลังซ่อม</option>
                                <option value="เสร็จสิ้น">เสร็จสิ้น</option>
                            </select>
                        </div>
                        
                        <button type="submit" name="add_repair_detail" class="btn btn-success">เพิ่มข้อมูลการซ่อม</button>
                    </form>
                </div>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-12">
                    <h5>รายละเอียดการซ่อมทั้งหมด</h5>
                    <?php if (count($repair_details) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>ช่างเทคนิค</th>
                                        <th>สถานะ</th>
                                        <th>วันที่อัปเดต</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($repair_details as $key => $detail): ?>
                                        <tr>
                                            <td><?=++$key?></td>
                                            <td>
                                                <?php if ($detail['tech_title']): ?>
                                                    <?=$detail['tech_title']?> <?=$detail['tech_firstname']?> <?=$detail['tech_lastname']?>
                                                <?php else: ?>
                                                    <span class="text-muted">ไม่ระบุ</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch($detail['status']) {
                                                    case 'รอซ่อม': $status_class = 'badge bg-warning text-dark'; break;
                                                    case 'กำลังซ่อม': $status_class = 'badge bg-info'; break;
                                                    case 'เสร็จสิ้น': $status_class = 'badge bg-success'; break;
                                                    default: $status_class = 'badge bg-secondary';
                                                }
                                                ?>
                                                <span class="<?=$status_class?>"><?=$detail['status']?></span>
                                            </td>
                                            <td><?=$detail['created_at']?></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-outline-danger"
                                                   onclick="if(confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?')) window.location='../../backend/repair_detail_action.php?delete_repair_detail=<?=$detail['id']?>';"
                                                >ลบ</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่มีรายละเอียดการซ่อม</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>