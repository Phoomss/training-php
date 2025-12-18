<?php
require_once('../../configs/connect.php');
require_once('../../configs/admin_only.php');

try {
    // Query to get repair requests with their latest status and related information
    $stmt = $conn->prepare("
        SELECT 
            r.id as repair_id,
            r.details,
            r.image,
            r.created_at as repair_date,
            r.updated_at as last_updated,
            s.title as student_title,
            s.firstname as student_firstname,
            s.lastname as student_lastname,
            e.name as equipment_name,
            t.title as tech_title,
            t.firstname as tech_firstname,
            t.lastname as tech_lastname,
            rd.status as current_status,
            rd.created_at as status_date
        FROM repair r
        LEFT JOIN student s ON r.student_id = s.id
        LEFT JOIN equipment e ON r.equipment_id = e.id
        LEFT JOIN repair_detail rd ON r.id = rd.repair_id 
            AND rd.created_at = (
                SELECT MAX(created_at) 
                FROM repair_detail 
                WHERE repair_id = r.id
            )
        LEFT JOIN technical t ON rd.technical_id = t.id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute();
    $repairs = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="th">
<head>
    <title>สถานะการซ่อม</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <style>
        .status-waiting { background-color: #ffc107; color: #000; }
        .status-in-progress { background-color: #0d6efd; color: #fff; }
        .status-completed { background-color: #198754; color: #fff; }
    </style>
</head>

<body class="bg-light">
    <?php require_once '../layouts/navbar.php'?>

    <main class="container mt-4">
        <!-- Messages -->
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">สถานะการซ่อมทั้งหมด</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ลำดับ</th>
                        <th>อุปกรณ์</th>
                        <th>รายละเอียด</th>
                        <th>ผู้แจ้งซ่อม</th>
                        <th>ช่างผู้รับผิดชอบ</th>
                        <th>สถานะล่าสุด</th>
                        <th>วันที่แจ้ง</th>
                        <th>วันที่อัปเดตล่าสุด</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($repairs) > 0): ?>
                        <?php foreach ($repairs as $index => $repair): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($repair['equipment_name'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars(substr($repair['details'], 0, 50)) . (strlen($repair['details']) > 50 ? '...' : '') ?></td>
                                <td>
                                    <?php if ($repair['student_title'] && $repair['student_firstname'] && $repair['student_lastname']): ?>
                                        <?= htmlspecialchars($repair['student_title'] . ' ' . $repair['student_firstname'] . ' ' . $repair['student_lastname']) ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($repair['tech_title'] && $repair['tech_firstname'] && $repair['tech_lastname']): ?>
                                        <?= htmlspecialchars($repair['tech_title'] . ' ' . $repair['tech_firstname'] . ' ' . $repair['tech_lastname']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">ยังไม่ได้กำหนดช่าง</span>
                                    <?php endif; ?>
                                </td>
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
                                <td><?= date('d/m/Y H:i', strtotime($repair['repair_date'])) ?></td>
                                <td>
                                    <?php if ($repair['last_updated']): ?>
                                        <?= date('d/m/Y H:i', strtotime($repair['last_updated'])) ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">ไม่มีข้อมูลการแจ้งซ่อม</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Detailed View Section -->
        <div class="mt-5">
            <h3 class="h5 mb-3">ประวัติสถานะการซ่อมทั้งหมด</h3>
            <div class="row">
                <?php foreach ($repairs as $index => $repair): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <strong>อุปกรณ์:</strong> <?= htmlspecialchars($repair['equipment_name'] ?? 'N/A') ?>
                                <br>
                                <small class="text-muted">แจ้งซ่อม: <?= date('d/m/Y H:i', strtotime($repair['repair_date'])) ?></small>
                            </div>
                            <div class="card-body">
                                <p><strong>รายละเอียด:</strong> <?= htmlspecialchars(substr($repair['details'], 0, 70)) . (strlen($repair['details']) > 70 ? '...' : '') ?></p>
                                <p><strong>ผู้แจ้ง:</strong> 
                                    <?php if ($repair['student_title'] && $repair['student_firstname'] && $repair['student_lastname']): ?>
                                        <?= htmlspecialchars($repair['student_title'] . ' ' . $repair['student_firstname'] . ' ' . $repair['student_lastname']) ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </p>

                                <?php 
                                // Get all status updates for this repair
                                $statusStmt = $conn->prepare("
                                    SELECT 
                                        rd.status,
                                        rd.created_at,
                                        t.title as tech_title,
                                        t.firstname as tech_firstname,
                                        t.lastname as tech_lastname
                                    FROM repair_detail rd
                                    LEFT JOIN technical t ON rd.technical_id = t.id
                                    WHERE rd.repair_id = :repair_id
                                    ORDER BY rd.created_at ASC
                                ");
                                $statusStmt->execute([':repair_id' => $repair['repair_id']]);
                                $statusUpdates = $statusStmt->fetchAll();
                                ?>

                                <div class="mt-2">
                                    <strong>สถานะ:</strong>
                                    <?php if (count($statusUpdates) > 0): ?>
                                        <?php foreach ($statusUpdates as $status): ?>
                                            <div class="d-flex justify-content-between align-items-center mb-1 p-2 border rounded bg-light">
                                                <?php $statusClass = '';
                                                    switch($status['status']) {
                                                        case 'รอซ่อม': $statusClass = 'status-waiting'; break;
                                                        case 'กำลังซ่อม': $statusClass = 'status-in-progress'; break;
                                                        case 'เสร็จสิ้น': $statusClass = 'status-completed'; break;
                                                        default: $statusClass = '';
                                                    }
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($status['status']) ?></span>
                                                <small class="text-muted">
                                                    <?php if ($status['tech_title'] && $status['tech_firstname'] && $status['tech_lastname']): ?>
                                                        โดย <?= htmlspecialchars($status['tech_title'] . ' ' . $status['tech_firstname'] . ' ' . $status['tech_lastname']) ?>
                                                    <?php endif; ?>
                                                    <br><?= date('d/m/Y H:i', strtotime($status['created_at'])) ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-muted">ยังไม่มีสถานะการซ่อม</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

  <?php require_once '../layouts/footer.php'?>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>