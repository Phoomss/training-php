<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM location ORDER BY id DESC");
$stmt->execute();
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM location_detail ORDER BY id DESC");
$stmt->execute();
$locationDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Manage Locations</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php require_once '../layouts/navbar.php'; ?>

    <main class="container mt-5">
        <!-- Main location management -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">อาคาร/ตึก</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>รายการอาคาร/ตึก</h5>
                    <a href="form_location.php" class="btn btn-success">+ เพิ่มอาคาร/ตึก</a>
                </div>

                <?php if (count($locations) === 0): ?>
                    <p class="text-center text-muted">ไม่พบข้อมูลอาคาร/ตึก</p>
                <?php else: ?>
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">#</th>
                                <th>ชื่ออาคาร/ตึก</th>
                                <th width="25%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($locations as $index => $location): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($location['name']) ?></td>
                                    <td>
                                        <a href="form_location.php?id=<?= $location['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>

                                        <a href="../../backend/location_action.php?delete_location=<?= $location['id'] ?>"
                                            class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ?')">
                                            ลบ
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Location detail management -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">ห้อง/ตำแหน่งย่อย</h4>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>รายการห้อง/ตำแหน่งย่อย</h5>
                    <a href="form_location_detail.php" class="btn btn-info">+ เพิ่มห้อง/ตำแหน่งย่อย</a>
                </div>

                <?php if (count($locationDetails) === 0): ?>
                    <p class="text-center text-muted">ไม่พบข้อมูลห้อง/ตำแหน่งย่อย</p>
                <?php else: ?>
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">#</th>
                                <th>ชื่อห้อง/ตำแหน่ง</th>
                                <th>ตึก</th>
                                <th width="25%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($locationDetails as $index => $detail): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($detail['name']) ?></td>
                                    <td>
                                        <?php
                                        $stmt = $conn->prepare("SELECT name FROM location WHERE id = :location_id");
                                        $stmt->execute([':location_id' => $detail['location_id']]);
                                        $loc = $stmt->fetch(PDO::FETCH_ASSOC);
                                        echo htmlspecialchars($loc['name'] ?? 'N/A');
                                        ?>
                                    </td>
                                    <td>
                                        <a href="form_location_detail.php?id=<?= $detail['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>

                                        <a href="../../backend/location_detail_action.php?delete_location_detail=<?= $detail['id'] ?>"
                                            class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ?')">
                                            ลบ
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>