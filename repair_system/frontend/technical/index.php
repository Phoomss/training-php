<?php
session_start();
require_once '../../configs/connect.php';

// Check if user is logged in and is technical
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'technical') {
    header('Location: ../../index.php');
    exit();
}

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE auth_id = :auth_id");
$stmt->execute([':auth_id' => $_SESSION['auth_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get repair requests assigned to this technician
$stmt = $conn->prepare("
    SELECT r.id, r.details, r.image, r.created_at,
           e.name as equipment_name,
           c.name as category_name,
           l.name as location_name,
           ld.room, ld.floor,
           u.firstname, u.lastname,
           rd.id as repair_detail_id,
           rd.status,
           rd.updated_at,
           staff.firstname as staff_firstname, staff.lastname as staff_lastname
    FROM repair r
    JOIN equipment e ON r.equipment_id = e.id
    JOIN categories c ON e.category_id = c.id
    JOIN location_detail ld ON r.location_id = ld.id
    JOIN location l ON ld.location_id = l.id
    JOIN users u ON r.user_id = u.id
    JOIN repair_detail rd ON r.id = rd.repair_id
    JOIN users staff ON rd.staff_id = staff.id
    WHERE rd.technician_id = :technician_id
    ORDER BY 
        CASE rd.status 
            WHEN 'กำลังซ่อม' THEN 1
            WHEN 'รอซ่อม' THEN 2
            WHEN 'เสร็จสิ้น' THEN 3
        END,
        r.created_at DESC
");
$stmt->execute([':technician_id' => $user['id']]);
$repairs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
    <head>
        <title>ระบบแจ้งซ่อม - ช่างเทคนิค</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />

        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>

    <body class="bg-info-subtle">
        <header>
            <nav class="navbar navbar-expand-lg bg-info">
                <div class="container-fluid">
                    <a class="navbar-brand text-white" href="#">ระบบแจ้งซ่อม</a>
                    <div class="navbar-nav ms-auto">
                        <span class="navbar-text text-white me-3">
                            สวัสดี, <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
                        </span>
                        <a class="btn btn-outline-light" href="../../backend/auth_action.php?logout=1">ออกจากระบบ</a>
                    </div>
                </div>
            </nav>
        </header>
        <main>
            <div class="container mt-4">
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

                <div class="card p-4">
                    <div class="mb-4">
                        <h1>ระบบแจ้งซ่อม</h1>
                        <p class="text-muted">สำหรับช่างเทคนิค - อัปเดตสถานะการซ่อม</p>
                        
                        <!-- Summary Cards -->
                        <div class="row mt-3">
                            <?php
                            $statusCounts = ['รอซ่อม' => 0, 'กำลังซ่อม' => 0, 'เสร็จสิ้น' => 0];
                            foreach ($repairs as $repair) {
                                $statusCounts[$repair['status']]++;
                            }
                            ?>
                            <div class="col-md-4">
                                <div class="card bg-warning text-dark">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">รอซ่อม</h5>
                                        <h2><?= $statusCounts['รอซ่อม'] ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">กำลังซ่อม</h5>
                                        <h2><?= $statusCounts['กำลังซ่อม'] ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">เสร็จสิ้น</h5>
                                        <h2><?= $statusCounts['เสร็จสิ้น'] ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">รูปภาพ</th>
                                    <th scope="col">ผู้แจ้ง</th>
                                    <th scope="col">หมวดหมู่</th>
                                    <th scope="col">อุปกรณ์</th>
                                    <th scope="col">สถานที่</th>
                                    <th scope="col">รายละเอียด</th>
                                    <th scope="col">สถานะ</th>
                                    <th scope="col">วันที่แจ้ง</th>
                                    <th scope="col">อัปเดตสถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($repairs)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">ยังไม่มีงานที่ได้รับมอบหมาย</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($repairs as $index => $repair): ?>
                                        <tr>
                                            <th scope="row"><?= $index + 1 ?></th>
                                            <td>
                                                <?php if ($repair['image']): ?>
                                                    <img src="../../<?= htmlspecialchars($repair['image']) ?>" 
                                                         alt="รูปซ่อม" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;"
                                                         data-bs-toggle="modal" data-bs-target="#imageModal<?= $repair['id'] ?>">
                                                <?php else: ?>
                                                    <span class="text-muted">ไม่มีรูป</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']) ?></td>
                                            <td><?= htmlspecialchars($repair['category_name']) ?></td>
                                            <td><?= htmlspecialchars($repair['equipment_name']) ?></td>
                                            <td><?= htmlspecialchars($repair['location_name']) ?> ชั้น <?= $repair['floor'] ?> ห้อง <?= htmlspecialchars($repair['room']) ?></td>
                                            <td>
                                                <span class="d-inline-block text-truncate" style="max-width: 150px;" title="<?= htmlspecialchars($repair['details']) ?>">
                                                    <?= htmlspecialchars($repair['details']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $statusClass = '';
                                                switch ($repair['status']) {
                                                    case 'รอซ่อม':
                                                        $statusClass = 'bg-warning text-dark';
                                                        break;
                                                    case 'กำลังซ่อม':
                                                        $statusClass = 'bg-info text-white';
                                                        break;
                                                    case 'เสร็จสิ้น':
                                                        $statusClass = 'bg-success text-white';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($repair['status']) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($repair['created_at'])) ?></td>
                                            <td>
                                                <form action="../../backend/repair_detail_action.php" method="post" class="d-flex gap-2">
                                                    <input type="hidden" name="update_repair_detail" value="1">
                                                    <input type="hidden" name="id" value="<?= $repair['repair_detail_id'] ?>">
                                                    <input type="hidden" name="technician_id" value="<?= $user['id'] ?>">
                                                    <input type="hidden" name="staff_id" value="<?= $repair['staff_id'] ?>">
                                                    
                                                    <select name="status" class="form-select form-select-sm" style="min-width: 120px;" onchange="this.form.submit()">
                                                        <option value="รอซ่อม" <?= $repair['status'] === 'รอซ่อม' ? 'selected' : '' ?>>รอซ่อม</option>
                                                        <option value="กำลังซ่อม" <?= $repair['status'] === 'กำลังซ่อม' ? 'selected' : '' ?>>กำลังซ่อม</option>
                                                        <option value="เสร็จสิ้น" <?= $repair['status'] === 'เสร็จสิ้น' ? 'selected' : '' ?>>เสร็จสิ้น</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>

                                        <!-- Image Modal -->
                                        <?php if ($repair['image']): ?>
                                            <div class="modal fade" id="imageModal<?= $repair['id'] ?>" tabindex="-1">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">รูปภาพการแจ้งซ่อม</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <img src="../../<?= htmlspecialchars($repair['image']) ?>" 
                                                                 alt="รูปซ่อม" class="img-fluid">
                                                            <div class="mt-3">
                                                                <p><strong>รายละเอียด:</strong> <?= htmlspecialchars($repair['details']) ?></p>
                                                                <p><strong>ผู้แจ้ง:</strong> <?= htmlspecialchars($repair['firstname'] . ' ' . $repair['lastname']) ?></p>
                                                                <p><strong>สถานที่:</strong> <?= htmlspecialchars($repair['location_name']) ?> ชั้น <?= $repair['floor'] ?> ห้อง <?= htmlspecialchars($repair['room']) ?></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
