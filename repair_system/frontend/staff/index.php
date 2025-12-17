<?php
session_start();
require_once '../../configs/connect.php';

// Check if user is logged in and is staff
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'staff') {
    header('Location: ../../index.php');
    exit();
}

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE auth_id = :auth_id");
$stmt->execute([':auth_id' => $_SESSION['auth_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all repair requests that don't have repair_detail yet (unassigned)
$stmt = $conn->prepare("
    SELECT r.id, r.details, r.image, r.created_at,
           e.name as equipment_name,
           c.name as category_name,
           l.name as location_name,
           ld.room, ld.floor,
           u.firstname, u.lastname,
           rd.id as repair_detail_id,
           rd.status,
           tech.firstname as tech_firstname, tech.lastname as tech_lastname
    FROM repair r
    JOIN equipment e ON r.equipment_id = e.id
    JOIN categories c ON e.category_id = c.id
    JOIN location_detail ld ON r.location_id = ld.id
    JOIN location l ON ld.location_id = l.id
    JOIN users u ON r.user_id = u.id
    LEFT JOIN repair_detail rd ON r.id = rd.repair_id
    LEFT JOIN users tech ON rd.technician_id = tech.id
    ORDER BY r.created_at DESC
");
$stmt->execute();
$repairs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all technicians
$stmt = $conn->prepare("
    SELECT u.id, u.firstname, u.lastname 
    FROM users u 
    JOIN auth a ON u.auth_id = a.id 
    WHERE a.role = 'technical'
    ORDER BY u.firstname, u.lastname
");
$stmt->execute();
$technicians = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
    <head>
        <title>ระบบแจ้งซ่อม - เจ้าหน้าที่</title>
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

    <body class="bg-warning-subtle">
        <header>
            <nav class="navbar navbar-expand-lg bg-warning">
                <div class="container-fluid">
                    <a class="navbar-brand text-dark" href="#">ระบบแจ้งซ่อม</a>
                    <div class="navbar-nav ms-auto">
                        <span class="navbar-text text-dark me-3">
                            สวัสดี, <?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']) ?>
                        </span>
                        <a class="btn btn-outline-dark" href="../../backend/auth_action.php?logout=1">ออกจากระบบ</a>
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
                        <p class="text-muted">สำหรับเจ้าหน้าที่ - จัดการและมอบหมายงานซ่อม</p>
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
                                    <th scope="col">ช่างที่รับผิดชอบ</th>
                                    <th scope="col">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($repairs)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">ยังไม่มีรายการแจ้งซ่อม</td>
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
                                                <?php if ($repair['repair_detail_id']): ?>
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
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">ยังไม่มอบหมาย</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($repair['repair_detail_id']): ?>
                                                    <?= htmlspecialchars($repair['tech_firstname'] . ' ' . $repair['tech_lastname']) ?>
                                                <?php else: ?>
                                                    <span class="text-muted">ยังไม่มอบหมาย</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!$repair['repair_detail_id']): ?>
                                                    <form action="../../backend/repair_detail_action.php" method="post" class="d-flex gap-2">
                                                        <input type="hidden" name="add_repair_detail" value="1">
                                                        <input type="hidden" name="repair_id" value="<?= $repair['id'] ?>">
                                                        <input type="hidden" name="staff_id" value="<?= $user['id'] ?>">
                                                        <input type="hidden" name="status" value="รอซ่อม">
                                                        
                                                        <select name="technician_id" class="form-select form-select-sm" required style="min-width: 120px;">
                                                            <option value="">เลือกช่าง</option>
                                                            <?php foreach ($technicians as $tech): ?>
                                                                <option value="<?= $tech['id'] ?>">
                                                                    <?= htmlspecialchars($tech['firstname'] . ' ' . $tech['lastname']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        
                                                        <button type="submit" class="btn btn-success btn-sm">มอบหมาย</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span class="badge bg-info">มอบหมายแล้ว</span>
                                                <?php endif; ?>
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
