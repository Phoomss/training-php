<?php
require_once('../../configs/connect.php');
session_start();

// Check if user is a technical staff
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'technical') {
    header('Location: ../../index.php?error=' . urlencode('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'));
    exit();
}

$technical_id = $_SESSION['auth_id'];

// Get technical staff info
$tech_stmt = $conn->prepare("SELECT * FROM technical WHERE auth_id = :auth_id");
$tech_stmt->execute([':auth_id' => $technical_id]);
$technical = $tech_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch repair requests assigned to this technical staff or pending
$stmt = $conn->prepare("
    SELECT r.*, e.name as equipment_name, s.firstname, s.lastname, s.student_id
    FROM repair r
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN student s ON r.student_id = s.id
    WHERE r.technical_id = :technical_id OR r.technical_id IS NULL
    ORDER BY r.created_at ASC
");
$stmt->execute([':technical_id' => $technical_id]);
$repairs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการแจ้งซ่อม - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>รายการแจ้งซ่อม</h2>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5>ข้อมูลช่างเทคนิค</h5>
                <p>
                    ชื่อ: <?= htmlspecialchars($technical['title'] . ' ' . $technical['firstname'] . ' ' . $technical['lastname']) ?><br>
                    เบอร์ติดต่อ: <?= htmlspecialchars($technical['phone']) ?>
                </p>
            </div>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['status']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>อุปกรณ์</th>
                                <th>ผู้แจ้ง</th>
                                <th>นักศึกษา ID</th>
                                <th>รายละเอียด</th>
                                <th>สถานะ</th>
                                <th>วันที่แจ้ง</th>
                                <th>รูปภาพ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($repairs) > 0): ?>
                                <?php foreach ($repairs as $index => $repair): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($repair['equipment_name']) ?></td>
                                        <td><?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']) ?></td>
                                        <td><?= htmlspecialchars($repair['student_id']) ?></td>
                                        <td><?= htmlspecialchars(substr($repair['details'], 0, 50)) ?>...</td>
                                        <td>
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
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($repair['created_at'])) ?></td>
                                        <td>
                                            <?php if ($repair['image']): ?>
                                                <a href="../../<?= htmlspecialchars($repair['image']) ?>" target="_blank">
                                                    <img src="../../<?= htmlspecialchars($repair['image']) ?>" 
                                                         alt="รูปภาพ" width="50" height="50" class="img-thumbnail">
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">ไม่มีรูป</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="process_repair.php?id=<?= $repair['id'] ?>" 
                                               class="btn btn-sm <?php echo $repair['technical_id'] ? 'btn-warning' : 'btn-primary'; ?>">
                                                <?php echo $repair['technical_id'] ? 'ดูรายละเอียด' : 'รับงาน'; ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">ไม่มีข้อมูลคำร้องแจ้งซ่อม</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>