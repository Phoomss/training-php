<?php
require_once '../../configs/student_only.php';
require_once '../../configs/connect.php';

// กำหนดค่า pagination
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// รับค่า search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    if (!empty($search)) {
        // Count repairs with search
        $countStmt = $conn->prepare("
            SELECT COUNT(*)
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            WHERE s.auth_id = :auth_id
              AND (e.name LIKE :search OR r.details LIKE :search)
        ");
        $countStmt->bindValue(':auth_id', $_SESSION['auth_id']);
        $countStmt->bindValue(':search', '%' . $search . '%');
        $countStmt->execute();
        $total_records = $countStmt->fetchColumn();

        // Get repairs with search and pagination
        $stmt = $conn->prepare("
            SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            WHERE s.auth_id = :auth_id
              AND (e.name LIKE :search OR r.details LIKE :search)
            ORDER BY r.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':auth_id', $_SESSION['auth_id']);
        $stmt->bindValue(':search', '%' . $search . '%');
        $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $repairs = $stmt->fetchAll();
    } else {
        // Count repairs without search
        $countStmt = $conn->prepare("
            SELECT COUNT(*)
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            WHERE s.auth_id = :auth_id
        ");
        $countStmt->bindValue(':auth_id', $_SESSION['auth_id']);
        $countStmt->execute();
        $total_records = $countStmt->fetchColumn();

        // Get repairs with pagination
        $stmt = $conn->prepare("
            SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            WHERE s.auth_id = :auth_id
            ORDER BY r.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':auth_id', $_SESSION['auth_id']);
        $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $repairs = $stmt->fetchAll();
    }

    $total_pages = ceil($total_records / $records_per_page);
} catch (PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}
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
        <div class="d-flex">
            <!-- Search Form -->
            <form method="GET" class="d-flex me-2" style="min-width: 300px;">
                <input type="text" name="search" class="form-control me-2" placeholder="ค้นหาอุปกรณ์, รายละเอียด..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit" class="btn btn-outline-primary">ค้นหา</button>
                <?php if (!empty($search)): ?>
                    <a href="index.php" class="btn btn-outline-secondary ms-1">ล้าง</a>
                <?php endif; ?>
            </form>
            <a href="form_repair.php" class="btn btn-success">+ เพิ่มรายการแจ้งซ่อม</a>
        </div>
    </div>

    <!-- Pagination Info -->
    <?php if ($total_records > 0): ?>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                แสดง <?= $offset + 1 ?> ถึง <?= min($offset + $records_per_page, $total_records) ?> จากทั้งหมด <?= $total_records ?> รายการ
            </div>
            <div>
                หน้า <?= $page ?> จาก <?= $total_pages ?>
            </div>
        </div>
    <?php endif; ?>

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

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Repair list pagination">
                <ul class="pagination justify-content-center">
                    <!-- Previous Button -->
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 . (!empty($search) ? '&search=' . urlencode($search) : '') ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">&laquo;</span>
                        </li>
                    <?php endif; ?>

                    <!-- Page Numbers -->
                    <?php
                    // แสดงปุ่มหน้า 1-2 หน้าก่อนหน้า และ 2-3 หน้าถัดไป
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);

                    if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i . (!empty($search) ? '&search=' . urlencode($search) : '') ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $total_pages . (!empty($search) ? '&search=' . urlencode($search) : '') ?>"><?= $total_pages ?></a>
                        </li>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 . (!empty($search) ? '&search=' . urlencode($search) : '') ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">&raquo;</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>

    <?php else: ?>
        <div class="alert alert-info">ยังไม่มีรายการแจ้งซ่อม</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>