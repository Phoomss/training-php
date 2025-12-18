<?php
require_once('../../configs/connect.php');
require_once('../../configs/admin_only.php');

// Get repair statistics
try {
    // Total repair requests
    $stmt = $conn->query("SELECT COUNT(*) as total FROM repair");
    $totalRepairs = $stmt->fetch()['total'];

    // Repair requests by status
    $stmt = $conn->query("
        SELECT
            rd.status,
            COUNT(*) as count
        FROM repair r
        LEFT JOIN repair_detail rd ON r.id = rd.repair_id
            AND rd.created_at = (
                SELECT MAX(created_at)
                FROM repair_detail
                WHERE repair_id = r.id
            )
        GROUP BY rd.status
    ");
    $repairStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Equipment count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM equipment");
    $equipmentCount = $stmt->fetch()['total'];

    // Technical staff count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM technical");
    $technicalCount = $stmt->fetch()['total'];

    // Student count
    $stmt = $conn->query("SELECT COUNT(*) as total FROM student");
    $studentCount = $stmt->fetch()['total'];

    // Repairs in the last 30 days
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM repair WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stmt->execute();
    $recentRepairs = $stmt->fetch()['count'];

} catch (PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="th">
<head>
    <title>หน้าหลัก - ระบบแจ้งซ่อม</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <style>
        .stat-card {
            border-left: 4px solid #0d6efd;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .stat-card.waiting {
            border-left-color: #ffc107;
        }
        .stat-card.in-progress {
            border-left-color: #0d6efd;
        }
        .stat-card.completed {
            border-left-color: #198754;
        }
        .status-waiting { background-color: #ffc107; color: #000; }
        .status-in-progress { background-color: #0d6efd; color: #fff; }
        .status-completed { background-color: #198754; color: #fff; }
    </style>
</head>

<body class="bg-light">
    <?php require_once '../layouts/navbar.php'?>

    <main class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3">ยินดีต้อนรับสู่ ระบบบริหารจัดการแจ้งซ่อม</h1>
                <p class="text-muted">สรุปข้อมูลระบบและสถิติการแจ้งซ่อม</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">อุปกรณ์ทั้งหมด</h5>
                        <h2 class="text-primary"><?= $equipmentCount ?></h2>
                        <p class="card-text text-muted">รายการอุปกรณ์ในระบบ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">รวมแจ้งซ่อม</h5>
                        <h2 class="text-primary"><?= $totalRepairs ?></h2>
                        <p class="card-text text-muted">คำร้องแจ้งซ่อมทั้งหมด</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">ช่างเทคนิค</h5>
                        <h2 class="text-primary"><?= $technicalCount ?></h2>
                        <p class="card-text text-muted">จำนวนช่างในระบบ</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">นักศึกษา</h5>
                        <h2 class="text-primary"><?= $studentCount ?></h2>
                        <p class="card-text text-muted">จำนวนผู้ใช้งานนักศึกษา</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">สถานะการซ่อมทั้งหมด</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card stat-card waiting h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">รอซ่อม</h5>
                                        <h2 class="text-warning"><?= $repairStats['รอซ่อม'] ?? 0 ?></h2>
                                        <p class="card-text text-muted">คำร้องที่ยังไม่ได้รับการดำเนินการ</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card stat-card in-progress h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">กำลังซ่อม</h5>
                                        <h2 class="text-primary"><?= $repairStats['กำลังซ่อม'] ?? 0 ?></h2>
                                        <p class="card-text text-muted">คำร้องที่อยู่ระหว่างดำเนินการ</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card stat-card completed h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">เสร็จสิ้น</h5>
                                        <h2 class="text-success"><?= $repairStats['เสร็จสิ้น'] ?? 0 ?></h2>
                                        <p class="card-text text-muted">คำร้องที่ดำเนินการเสร็จสิ้น</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Repairs Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">รายการแจ้งซ่มล่าสุด</h5>
                        <a href="repairs.php" class="btn btn-primary btn-sm">ดูทั้งหมด</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>อุปกรณ์</th>
                                        <th>รายละเอียด</th>
                                        <th>ผู้แจ้ง</th>
                                        <th>วันที่แจ้ง</th>
                                        <th>สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->prepare("
                                        SELECT
                                            r.id as repair_id,
                                            r.details,
                                            r.created_at as repair_date,
                                            e.name as equipment_name,
                                            s.title as student_title,
                                            s.firstname as student_firstname,
                                            s.lastname as student_lastname,
                                            rd.status as current_status
                                        FROM repair r
                                        LEFT JOIN equipment e ON r.equipment_id = e.id
                                        LEFT JOIN student s ON r.student_id = s.id
                                        LEFT JOIN repair_detail rd ON r.id = rd.repair_id
                                            AND rd.created_at = (
                                                SELECT MAX(created_at)
                                                FROM repair_detail
                                                WHERE repair_id = r.id
                                            )
                                        ORDER BY r.created_at DESC
                                        LIMIT 5
                                    ");
                                    $stmt->execute();
                                    $recentRepairs = $stmt->fetchAll();
                                    ?>
                                    <?php if (count($recentRepairs) > 0): ?>
                                        <?php foreach ($recentRepairs as $repair): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($repair['equipment_name'] ?? 'N/A') ?></td>
                                                <td><?= htmlspecialchars(substr($repair['details'], 0, 50)) . (strlen($repair['details']) > 50 ? '...' : '') ?></td>
                                                <td>
                                                    <?php if ($repair['student_title'] && $repair['student_firstname'] && $repair['student_lastname']): ?>
                                                        <?= htmlspecialchars($repair['student_title'] . ' ' . $repair['student_firstname'] . ' ' . $repair['student_lastname']) ?>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($repair['repair_date'])) ?></td>
                                                <td>
                                                    <?php if ($repair['current_status']): ?>
                                                        <?php $statusClass = '';
                                                            switch($repair['current_status']) {
                                                                case 'รอซ่อม': $statusClass = 'status-waiting'; break;
                                                                case 'กำลังซ่อม': $statusClass = 'status-in-progress'; break;
                                                                case 'เสร็จสิ้น': $statusClass = 'status-completed'; break;
                                                                default: $statusClass = '';
                                                            }
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($repair['current_status']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">ยังไม่มีสถานะ</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">ไม่มีข้อมูลการแจ้งซ่อมล่าสุด</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">การจัดการด่วน</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6 mb-3">
                                <a href="equipment.php" class="text-decoration-none">
                                    <div class="card text-center h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="bg-primary text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-tools"></i> <!-- Bootstrap icon placeholder -->
                                            </div>
                                            <h6 class="card-title">จัดการอุปกรณ์</h6>
                                            <p class="card-text text-muted small">เพิ่ม/แก้ไข/ลบ รายการอุปกรณ์</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="technical.php" class="text-decoration-none">
                                    <div class="card text-center h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="bg-success text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-person-gear"></i> <!-- Bootstrap icon placeholder -->
                                            </div>
                                            <h6 class="card-title">จัดการช่างเทคนิค</h6>
                                            <p class="card-text text-muted small">เพิ่ม/แก้ไข/ลบ ช่างเทคนิค</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="repairs.php" class="text-decoration-none">
                                    <div class="card text-center h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="bg-warning text-dark rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-clipboard-check"></i> <!-- Bootstrap icon placeholder -->
                                            </div>
                                            <h6 class="card-title">สถานะการซ่อม</h6>
                                            <p class="card-text text-muted small">ดูสถานะคำร้องทั้งหมด</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="report.php" class="text-decoration-none">
                                    <div class="card text-center h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="bg-info text-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-file-earmark-bar-graph"></i> <!-- Bootstrap icon placeholder -->
                                            </div>
                                            <h6 class="card-title">รายงานแจ้งซ่อม</h6>
                                            <p class="card-text text-muted small">ดูรายงานและวิเคราะห์ข้อมูล</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once '../layouts/footer.php'?>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
