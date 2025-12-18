<?php
require_once '../../configs/student_only.php';
require_once '../../configs/connect.php';

$repair_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$repair = null;

if ($repair_id > 0) {
    // ดึงข้อมูลสำหรับการแก้ไข
    $stmt = $conn->prepare("
        SELECT r.*, s.auth_id
        FROM repair r
        JOIN student s ON r.student_id = s.id
        WHERE r.id = :id
    ");
    $stmt->execute([':id' => $repair_id]);
    $repair = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่าผู้ใช้คนเดียวกันเป็นคนสร้างหรือไม่
    if (!$repair || $repair['auth_id'] != $_SESSION['auth_id']) {
        header('Location: index.php?error=' . urlencode('ไม่สามารถเข้าถึงข้อมูลนี้ได้'));
        exit();
    }
}

// ดึงรายการอุปกรณ์ทั้งหมด
$stmt_equipment = $conn->prepare("SELECT * FROM equipment ORDER BY name ASC");
$stmt_equipment->execute();
$equipments = $stmt_equipment->fetchAll();
?>

<!doctype html>
<html lang="th">
<head>
    <title>
        <?php if ($repair_id > 0): ?>แก้ไขรายการแจ้งซ่อม<?php else: ?>แจ้งซ่อมอุปกรณ์<?php endif; ?> | นักศึกษา
    </title>
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
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <?php if ($repair_id > 0): ?>
                            แก้ไขรายการแจ้งซ่อม #<?=$repair['id']?>
                        <?php else: ?>
                            แจ้งซ่อมอุปกรณ์
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?=htmlspecialchars($_GET['error'])?>
                            <button class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="../../backend/repair_action.php" method="post" enctype="multipart/form-data">
                        <!-- ฟิลด์ซ่อน ID ถ้าเป็นการแก้ไข -->
                        <?php if ($repair_id > 0): ?>
                            <input type="hidden" name="id" value="<?=$repair['id']?>">
                            <input type="hidden" name="from_student" value="true">
                        <?php else: ?>
                            <input type="hidden" name="from_student" value="true">
                        <?php endif; ?>

                        <!-- ดึงข้อมูลนักศึกษาที่ล็อกอิน -->
                        <?php
                        $stmt_student = $conn->prepare("SELECT * FROM student WHERE auth_id = :auth_id");
                        $stmt_student->execute([':auth_id' => $_SESSION['auth_id']]);
                        $student = $stmt_student->fetch(PDO::FETCH_ASSOC);
                        ?>

                        <input type="hidden" name="student_id" value="<?=$student['id']?>">

                        <div class="mb-3">
                            <label class="form-label">อุปกรณ์ <span class="text-danger">*</span></label>
                            <select name="equipment_id" class="form-select" required>
                                <option value="">เลือกอุปกรณ์</option>
                                <?php foreach ($equipments as $equipment): ?>
                                    <option value="<?=$equipment['id']?>" 
                                        <?php if (isset($repair) && $repair['equipment_id'] == $equipment['id']) echo 'selected'; ?>>
                                        <?=$equipment['name']?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">รายละเอียดการแจ้งซ่อม <span class="text-danger">*</span></label>
                            <textarea name="details" class="form-control" rows="4" placeholder="ระบุปัญหาหรืออาการของอุปกรณ์" required><?=isset($repair) ? htmlspecialchars($repair['details']) : ''?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">รูปภาพประกอบ (ถ้ามี)</label>
                            <?php if (isset($repair) && $repair['image']): ?>
                                <div class="mb-2">
                                    <p>รูปภาพปัจจุบัน:</p>
                                    <img src="../../<?=$repair['image']?>" alt="รูปภาพปัจจุบัน" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <div class="form-text">รองรับไฟล์ JPG, JPEG, PNG เท่านั้น</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-secondary me-md-2">ยกเลิก</a>
                            <?php if ($repair_id > 0): ?>
                                <button type="submit" name="update_repair" class="btn btn-primary">อัปเดตรายการ</button>
                            <?php else: ?>
                                <button type="submit" name="add_repair" class="btn btn-success">แจ้งซ่อม</button>
                            <?php endif; ?>
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