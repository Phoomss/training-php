<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

// Fetch repairs with related data
$stmt = $conn->prepare("
    SELECT
        r.id,
        u.title, u.firstname, u.lastname as user_name,
        e.name as equipment_name,
        ld.name as location_detail_name,
        l.name as location_name,
        r.image,
        r.created_at,
        rd.status as current_status
    FROM repair r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN location_detail ld ON r.locationD_id = ld.id
    LEFT JOIN location l ON ld.location_id = l.id
    LEFT JOIN repair_detail rd ON r.id = rd.repair_id
    ORDER BY r.id DESC
");
$stmt->execute();
$repairs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all users for filtering
$userStmt = $conn->prepare("SELECT id, username FROM users ORDER BY username ASC");
$userStmt->execute();
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all equipment for filtering
$equipStmt = $conn->prepare("SELECT id, name FROM equipment ORDER BY name ASC");
$equipStmt->execute();
$equipment = $equipStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status filter with validation
$filter_user = filter_input(INPUT_GET, 'filter_user', FILTER_VALIDATE_INT) ?: '';
$filter_equipment = filter_input(INPUT_GET, 'filter_equipment', FILTER_VALIDATE_INT) ?: '';
$filter_status = preg_replace('/[^A-Za-zก-๙\s]/', '', filter_input(INPUT_GET, 'filter_status', FILTER_SANITIZE_STRING)) ?: '';

// Apply filters to the main query
$whereClause = "";
$params = [];

if (!empty($filter_user)) {
    $whereClause .= " AND u.id = :filter_user";
    $params[':filter_user'] = $filter_user;
}

if (!empty($filter_equipment)) {
    $whereClause .= " AND e.id = :filter_equipment";
    $params[':filter_equipment'] = $filter_equipment;
}

if (!empty($filter_status)) {
    $whereClause .= " AND rd.status = :filter_status";
    $params[':filter_status'] = $filter_status;
}

$sql = "
    SELECT
        r.id,
        u.title, u.firstname, u.lastname as user_name,
        e.name as equipment_name,
        ld.name as location_detail_name,
        l.name as location_name,
        r.image,
        r.created_at,
        rd.status as current_status
    FROM repair r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN equipment e ON r.equipment_id = e.id
    LEFT JOIN location_detail ld ON r.locationD_id = ld.id
    LEFT JOIN location l ON ld.location_id = l.id
    LEFT JOIN repair_detail rd ON r.id = rd.repair_id
    WHERE 1=1
    $whereClause
    ORDER BY r.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$filtered_repairs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Repair Management</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .repair-image {
            max-height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
        }

        .status-waiting {
            background-color: #ffc107;
            color: #000;
        }

        .status-progress {
            background-color: #0d6efd;
            color: white;
        }

        .status-complete {
            background-color: #198754;
            color: white;
        }
    </style>
</head>

<body>
    <?php require_once '../layouts/navbar.php' ?>

    <main class="container mt-5">
        <div class="card shadow p-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3><i class="bi bi-tools"></i> จัดการแจ้งซ่อม</h3>
                <!-- Removed the "Add Repair" button since admin usually doesn't create repair requests -->
            </div>

            <!-- Filters -->
            <div class="row mb-4 g-3">
                <div class="col-md-4">
                    <label class="form-label">ค้นหาจากผู้แจ้ง</label>
                    <select class="form-select" id="filterUserSelect" onchange="applyFilters()">
                        <option value="">ทั้งหมด</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user['id'] ?>" <?= $filter_user == $user['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">ค้นหาจากอุปกรณ์</label>
                    <select class="form-select" id="filterEquipmentSelect" onchange="applyFilters()">
                        <option value="">ทั้งหมด</option>
                        <?php foreach ($equipment as $equip): ?>
                            <option value="<?= $equip['id'] ?>" <?= $filter_equipment == $equip['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($equip['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">สถานะ</label>
                    <select class="form-select" id="filterStatusSelect" onchange="applyFilters()">
                        <option value="">ทั้งหมด</option>
                        <option value="รอซ่อม" <?= $filter_status === 'รอซ่อม' ? 'selected' : '' ?>>รอซ่อม</option>
                        <option value="กำลังซ่อม" <?= $filter_status === 'กำลังซ่อม' ? 'selected' : '' ?>>กำลังซ่อม
                        </option>
                        <option value="เสร็จสิ้น" <?= $filter_status === 'เสร็จสิ้น' ? 'selected' : '' ?>>เสร็จสิ้น
                        </option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="50">#</th>
                            <th width="150">รูปภาพ</th>
                            <th>ผู้แจ้ง</th>
                            <th>อุปกรณ์</th>
                            <th>ตำแหน่ง</th>
                            <th width="120">วันที่แจ้ง</th>
                            <th width="120">สถานะปัจจุบัน</th>
                            <th width="150">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_repairs)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">ไม่มีข้อมูล</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($filtered_repairs as $i => $repair): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td>
                                        <?php if (!empty($repair['image']) && file_exists('../../' . $repair['image'])): ?>
                                            <img src="../../<?= $repair['image'] ?>" alt="Repair Image"
                                                class="repair-image img-thumbnail">
                                        <?php else: ?>
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                style="height: 100px;">
                                                <span class="text-muted">ไม่มีรูป</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($repair['user_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($repair['equipment_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <?= htmlspecialchars($repair['location_name'] ?? 'N/A') ?> -
                                        <?= htmlspecialchars($repair['location_detail_name'] ?? 'N/A') ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($repair['created_at'])) ?></td>
                                    <td>
                                        <?php if (!empty($repair['current_status'])): ?>
                                            <span class="status-badge 
                                                <?php
                                                if ($repair['current_status'] === 'รอซ่อม')
                                                    echo 'status-waiting';
                                                elseif ($repair['current_status'] === 'กำลังซ่อม')
                                                    echo 'status-progress';
                                                else
                                                    echo 'status-complete';
                                                ?>
                                            ">
                                                <?= htmlspecialchars($repair['current_status']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-waiting">รออนุมัติ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-grid gap-2">
                                            <a href="repair_detail.php?repair_id=<?= $repair['id'] ?>"
                                                class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> ดูรายละเอียด
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function applyFilters() {
            const userFilter = document.getElementById('filterUserSelect').value;
            const equipFilter = document.getElementById('filterEquipmentSelect').value;
            const statusFilter = document.getElementById('filterStatusSelect').value;

            let url = window.location.pathname;
            let params = [];

            if (userFilter) params.push('filter_user=' + encodeURIComponent(userFilter));
            if (equipFilter) params.push('filter_equipment=' + encodeURIComponent(equipFilter));
            if (statusFilter) params.push('filter_status=' + encodeURIComponent(statusFilter));

            const queryString = params.length > 0 ? '?' + params.join('&') : '';
            window.location.href = url + queryString;
        }
    </script>
</body>

</html>