<?php
require_once '../../configs/technical_only.php';
require_once '../../configs/connect.php';

// ดึงข้อมูลช่างเทคนิคที่ล็อกอิน
$stmt_tech = $conn->prepare("SELECT * FROM technical WHERE auth_id = :auth_id");
$stmt_tech->execute([':auth_id' => $_SESSION['auth_id']]);
$technical = $stmt_tech->fetch(PDO::FETCH_ASSOC);

// กำหนดค่า pagination สำหรับงานที่ได้รับมอบหมาย
$assigned_records_per_page = 10;
$assigned_page = isset($_GET['assigned_page']) ? (int)$_GET['assigned_page'] : 1;
$assigned_offset = ($assigned_page - 1) * $assigned_records_per_page;

// รับค่า search สำหรับงานที่ได้รับมอบหมาย
$assigned_search = isset($_GET['assigned_search']) ? trim($_GET['assigned_search']) : '';

// กำหนดค่า pagination สำหรับงานที่รอดำเนินการ
$pending_records_per_page = 10;
$pending_page = isset($_GET['pending_page']) ? (int)$_GET['pending_page'] : 1;
$pending_offset = ($pending_page - 1) * $pending_records_per_page;

// รับค่า search สำหรับงานที่รอดำเนินการ
$pending_search = isset($_GET['pending_search']) ? trim($_GET['pending_search']) : '';

try {
    // ดึงข้อมูลงานซ่อมที่ได้รับมอบหมาย (with search and pagination)
    if (!empty($assigned_search)) {
        $countStmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            JOIN repair_detail rd ON r.id = rd.repair_id
            WHERE rd.technical_id = :technical_id
              AND (e.name LIKE :search OR s.firstname LIKE :search OR s.lastname LIKE :search OR r.details LIKE :search)
            GROUP BY r.id
        ");
        $countStmt->bindValue(':technical_id', $technical['id']);
        $countStmt->bindValue(':search', '%' . $assigned_search . '%');
        $countStmt->execute();
        $assigned_total_records = $countStmt->rowCount(); // Note: This is a simplification, we need a different approach
        
        // More complex query to count for the assigned repairs with search
        $countStmt = $conn->prepare("
            SELECT COUNT(DISTINCT r.id) 
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            JOIN repair_detail rd ON r.id = rd.repair_id
            WHERE rd.technical_id = :technical_id
              AND (e.name LIKE :search OR s.firstname LIKE :search OR s.lastname LIKE :search OR r.details LIKE :search)
        ");
        $countStmt->bindValue(':technical_id', $technical['id']);
        $countStmt->bindValue(':search', '%' . $assigned_search . '%');
        $countStmt->execute();
        $assigned_total_records = $countStmt->fetchColumn();
        
        $stmt_assigned = $conn->prepare("
            SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name,
                   (SELECT rd2.status FROM repair_detail rd2 WHERE rd2.repair_id = r.id ORDER BY rd2.created_at DESC LIMIT 1) as current_status
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            JOIN repair_detail rd ON r.id = rd.repair_id
            WHERE rd.technical_id = :technical_id
              AND (e.name LIKE :search OR s.firstname LIKE :search OR s.lastname LIKE :search OR r.details LIKE :search)
            GROUP BY r.id
            ORDER BY r.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt_assigned->bindValue(':technical_id', $technical['id']);
        $stmt_assigned->bindValue(':search', '%' . $assigned_search . '%');
        $stmt_assigned->bindValue(':limit', $assigned_records_per_page, PDO::PARAM_INT);
        $stmt_assigned->bindValue(':offset', $assigned_offset, PDO::PARAM_INT);
        $stmt_assigned->execute();
        $assigned_repairs = $stmt_assigned->fetchAll();
    } else {
        // Count for assigned repairs without search
        $countStmt = $conn->prepare("
            SELECT COUNT(DISTINCT r.id) 
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            JOIN repair_detail rd ON r.id = rd.repair_id
            WHERE rd.technical_id = :technical_id
        ");
        $countStmt->bindValue(':technical_id', $technical['id']);
        $countStmt->execute();
        $assigned_total_records = $countStmt->fetchColumn();
        
        $stmt_assigned = $conn->prepare("
            SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name,
                   (SELECT rd2.status FROM repair_detail rd2 WHERE rd2.repair_id = r.id ORDER BY rd2.created_at DESC LIMIT 1) as current_status
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            JOIN repair_detail rd ON r.id = rd.repair_id
            WHERE rd.technical_id = :technical_id
            GROUP BY r.id
            ORDER BY r.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt_assigned->bindValue(':technical_id', $technical['id']);
        $stmt_assigned->bindValue(':limit', $assigned_records_per_page, PDO::PARAM_INT);
        $stmt_assigned->bindValue(':offset', $assigned_offset, PDO::PARAM_INT);
        $stmt_assigned->execute();
        $assigned_repairs = $stmt_assigned->fetchAll();
    }
    
    $assigned_total_pages = ceil($assigned_total_records / $assigned_records_per_page);

    // ดึงข้อมูลงานซ่อมที่ยังไม่ได้มีการจัดการ (with search and pagination)
    if (!empty($pending_search)) {
        $countStmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            LEFT JOIN repair_detail rd ON r.id = rd.repair_id
            WHERE rd.repair_id IS NULL
              AND (e.name LIKE :search OR s.firstname LIKE :search OR s.lastname LIKE :search OR r.details LIKE :search)
        ");
        $countStmt->bindValue(':search', '%' . $pending_search . '%');
        $countStmt->execute();
        $pending_total_records = $countStmt->fetchColumn();
        
        $stmt_pending = $conn->prepare("
            SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            LEFT JOIN repair_detail rd ON r.id = rd.repair_id
            WHERE rd.repair_id IS NULL
              AND (e.name LIKE :search OR s.firstname LIKE :search OR s.lastname LIKE :search OR r.details LIKE :search)
            ORDER BY r.created_at ASC
            LIMIT :limit OFFSET :offset
        ");
        $stmt_pending->bindValue(':search', '%' . $pending_search . '%');
        $stmt_pending->bindValue(':limit', $pending_records_per_page, PDO::PARAM_INT);
        $stmt_pending->bindValue(':offset', $pending_offset, PDO::PARAM_INT);
        $stmt_pending->execute();
        $pending_repairs = $stmt_pending->fetchAll();
    } else {
        // Count for pending repairs without search
        $countStmt = $conn->prepare("
            SELECT COUNT(*) 
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            LEFT JOIN repair_detail rd ON r.id = rd.repair_id
            WHERE rd.repair_id IS NULL
        ");
        $countStmt->execute();
        $pending_total_records = $countStmt->fetchColumn();
        
        $stmt_pending = $conn->prepare("
            SELECT r.*, s.title, s.firstname, s.lastname, e.name as equipment_name
            FROM repair r
            JOIN student s ON r.student_id = s.id
            JOIN equipment e ON r.equipment_id = e.id
            LEFT JOIN repair_detail rd ON r.id = rd.repair_id
            WHERE rd.repair_id IS NULL
            ORDER BY r.created_at ASC
            LIMIT :limit OFFSET :offset
        ");
        $stmt_pending->bindValue(':limit', $pending_records_per_page, PDO::PARAM_INT);
        $stmt_pending->bindValue(':offset', $pending_offset, PDO::PARAM_INT);
        $stmt_pending->execute();
        $pending_repairs = $stmt_pending->fetchAll();
    }
    
    $pending_total_pages = ceil($pending_total_records / $pending_records_per_page);
} catch (PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}
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
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">งานซ่อมที่ได้รับมอบหมาย</h4>
                <!-- Search Form for assigned repairs -->
                <form method="GET" class="d-flex" style="min-width: 300px;">
                    <input type="hidden" name="pending_page" value="<?= $pending_page ?>">
                    <input type="hidden" name="pending_search" value="<?= htmlspecialchars($pending_search) ?>">
                    <input type="text" name="assigned_search" class="form-control me-2" placeholder="ค้นหาอุปกรณ์, นักศึกษา, รายละเอียด..." value="<?= htmlspecialchars($assigned_search) ?>">
                    <button type="submit" class="btn btn-outline-primary">ค้นหา</button>
                    <?php if (!empty($assigned_search)): ?>
                        <a href="?pending_page=<?= $pending_page . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) : '') ?>" class="btn btn-outline-secondary ms-1">ล้าง</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div class="card-body">
            <!-- Pagination Info for assigned repairs -->
            <?php if ($assigned_total_records > 0): ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        แสดง <?= $assigned_offset + 1 ?> ถึง <?= min($assigned_offset + $assigned_records_per_page, $assigned_total_records) ?> จากทั้งหมด <?= $assigned_total_records ?> รายการ
                    </div>
                    <div>
                        หน้า <?= $assigned_page ?> จาก <?= $assigned_total_pages ?>
                    </div>
                </div>
            <?php endif; ?>
            
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
                                    <td><?= $assigned_offset + $key + 1 ?></td>
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
                
                <!-- Pagination for assigned repairs -->
                <?php if ($assigned_total_pages > 1): ?>
                    <nav aria-label="Assigned repairs pagination">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Button -->
                            <?php if ($assigned_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?assigned_page=<?= $assigned_page - 1 . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) : '') . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) . '&pending_page=' . $pending_page : '') ?>" aria-label="Previous">
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
                            $start_page = max(1, $assigned_page - 2);
                            $end_page = min($assigned_total_pages, $assigned_page + 2);
                            ?>
                            
                            <?php if ($start_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?assigned_page=1<?= !empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) : '' . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) . '&pending_page=' . $pending_page : '') ?>">1</a>
                                </li>
                                <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?= ($i == $assigned_page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?assigned_page=<?= $i . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) : '') . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) . '&pending_page=' . $pending_page : '') ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($end_page < $assigned_total_pages): ?>
                                <?php if ($end_page < $assigned_total_pages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="?assigned_page=<?= $assigned_total_pages . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) : '') . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) . '&pending_page=' . $pending_page : '') ?>"><?= $assigned_total_pages ?></a>
                                </li>
                            <?php endif; ?>

                            <!-- Next Button -->
                            <?php if ($assigned_page < $assigned_total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?assigned_page=<?= $assigned_page + 1 . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) : '') . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) . '&pending_page=' . $pending_page : '') ?>" aria-label="Next">
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
                <p class="text-muted">ยังไม่มีงานซ่อมที่ได้รับมอบหมาย</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- งานซ่อมที่รอดำเนินการ -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">งานซ่อมที่รอดำเนินการ</h4>
                <!-- Search Form for pending repairs -->
                <form method="GET" class="d-flex" style="min-width: 300px;">
                    <input type="hidden" name="assigned_page" value="<?= $assigned_page ?>">
                    <input type="hidden" name="assigned_search" value="<?= htmlspecialchars($assigned_search) ?>">
                    <input type="text" name="pending_search" class="form-control me-2" placeholder="ค้นหาอุปกรณ์, นักศึกษา, รายละเอียด..." value="<?= htmlspecialchars($pending_search) ?>">
                    <button type="submit" class="btn btn-outline-primary">ค้นหา</button>
                    <?php if (!empty($pending_search)): ?>
                        <a href="?assigned_page=<?= $assigned_page . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) : '') ?>" class="btn btn-outline-secondary ms-1">ล้าง</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div class="card-body">
            <!-- Pagination Info for pending repairs -->
            <?php if ($pending_total_records > 0): ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        แสดง <?= $pending_offset + 1 ?> ถึง <?= min($pending_offset + $pending_records_per_page, $pending_total_records) ?> จากทั้งหมด <?= $pending_total_records ?> รายการ
                    </div>
                    <div>
                        หน้า <?= $pending_page ?> จาก <?= $pending_total_pages ?>
                    </div>
                </div>
            <?php endif; ?>
            
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
                                    <td><?= $pending_offset + $key + 1 ?></td>
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
                
                <!-- Pagination for pending repairs -->
                <?php if ($pending_total_pages > 1): ?>
                    <nav aria-label="Pending repairs pagination">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Button -->
                            <?php if ($pending_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pending_page=<?= $pending_page - 1 . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) : '') . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) . '&assigned_page=' . $assigned_page : '') ?>" aria-label="Previous">
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
                            $start_page = max(1, $pending_page - 2);
                            $end_page = min($pending_total_pages, $pending_page + 2);
                            ?>
                            
                            <?php if ($start_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pending_page=1<?= !empty($pending_search) ? '&pending_search=' . urlencode($pending_search) : '' . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) . '&assigned_page=' . $assigned_page : '') ?>">1</a>
                                </li>
                                <?php if ($start_page > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                <li class="page-item <?= ($i == $pending_page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?pending_page=<?= $i . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) : '') . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) . '&assigned_page=' . $assigned_page : '') ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($end_page < $pending_total_pages): ?>
                                <?php if ($end_page < $pending_total_pages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pending_page=<?= $pending_total_pages . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) : '') . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) . '&assigned_page=' . $assigned_page : '') ?>"><?= $pending_total_pages ?></a>
                                </li>
                            <?php endif; ?>

                            <!-- Next Button -->
                            <?php if ($pending_page < $pending_total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pending_page=<?= $pending_page + 1 . (!empty($pending_search) ? '&pending_search=' . urlencode($pending_search) : '') . (!empty($assigned_search) ? '&assigned_search=' . urlencode($assigned_search) . '&assigned_page=' . $assigned_page : '') ?>" aria-label="Next">
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
                <p class="text-muted">ไม่มีงานซ่อมใหม่ที่รอดำเนินการ</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>