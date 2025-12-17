<?php
session_start();
require_once '../../configs/connect.php';

// Check if user is logged in and is a student
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'student') {
    header('Location: ../../index.php');
    exit();
}

// Get user info
$stmt = $conn->prepare("SELECT * FROM users WHERE auth_id = :auth_id");
$stmt->execute([':auth_id' => $_SESSION['auth_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get repair requests for this user
$stmt = $conn->prepare("
    SELECT r.id, r.details, r.image, r.created_at,
           e.name as equipment_name,
           c.name as category_name,
           l.name as location_name,
           ld.room, ld.floor,
           COALESCE(rd.status, 'รอดำเนินการ') as status
    FROM repair r
    JOIN equipment e ON r.equipment_id = e.id
    JOIN categories c ON e.category_id = c.id
    JOIN location_detail ld ON r.location_id = ld.id
    JOIN location l ON ld.location_id = l.id
    LEFT JOIN repair_detail rd ON r.id = rd.repair_id
    WHERE r.user_id = :user_id
    ORDER BY r.created_at DESC
");
$stmt->execute([':user_id' => $user['id']]);
$repairs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
    <head>
        <title>ระบบแจ้งซ่อม - นักศึกษา</title>
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

    <body class="bg-danger-subtle">
        <header>
            <nav class="navbar navbar-expand-lg bg-danger">
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1>ระบบแจ้งซ่อม</h1>
                            <p class="text-muted">สำหรับนักศึกษา</p>
                        </div>
                        <div>
                            <a href="form_repair.php" class="btn btn-primary btn-lg">
                                <i class="bi bi-plus-circle"></i> แจ้งซ่อมใหม่
                            </a>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">รูปภาพ</th>
                                    <th scope="col">หมวดหมู่</th>
                                    <th scope="col">อุปกรณ์</th>
                                    <th scope="col">สถานที่</th>
                                    <th scope="col">รายละเอียด</th>
                                    <th scope="col">สถานะ</th>
                                    <th scope="col">วันที่แจ้ง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($repairs)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">ยังไม่มีรายการแจ้งซ่อม</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($repairs as $index => $repair): ?>
                                        <tr>
                                            <th scope="row"><?= $index + 1 ?></th>
                                            <td>
                                                <?php if ($repair['image']): ?>
                                                    <img src="../../<?= htmlspecialchars($repair['image']) ?>" 
                                                         alt="รูปซ่อม" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                                <?php else: ?>
                                                    <span class="text-muted">ไม่มีรูป</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($repair['category_name']) ?></td>
                                            <td><?= htmlspecialchars($repair['equipment_name']) ?></td>
                                            <td><?= htmlspecialchars($repair['location_name']) ?> ชั้น <?= $repair['floor'] ?> ห้อง <?= htmlspecialchars($repair['room']) ?></td>
                                            <td><?= htmlspecialchars($repair['details']) ?></td>
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
                                                    default:
                                                        $statusClass = 'bg-secondary text-white';
                                                }
                                                ?>
                                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($repair['status']) ?></span>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($repair['created_at'])) ?></td>
                                        </tr>
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
