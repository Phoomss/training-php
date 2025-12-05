<?php
require_once "../configs/admin_only.php";
require_once "../configs/connect.php";

$stmt = $conn->query("SELECT * FROM majors ORDER BY id ASC");
$majors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการสาขา</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php require_once "../layouts/navbar.php"; ?>

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>รายการสาขา</h2>
            <a href="form_major.php" class="btn btn-primary">+ เพิ่มสาขาใหม่</a>
        </div>

        <!-- Alerts -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">

                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th width="80">ID</th>
                            <th>ชื่อสาขา</th>
                            <th width="150">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php $counter = 1;
                        foreach ($majors as $major): ?>
                            <tr>
                                <td><?= $counter ?></td>
                                <td><?= htmlspecialchars($major['name']) ?></td>
                                <td>
                                    <a href="form_major.php?id=<?= $major['id'] ?>"
                                        class="btn btn-warning btn-sm">แก้ไข</a>

                                    <a href="../backend/major_api.php?delete=<?= $major['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('ยืนยันการลบสาขานี้?');">
                                        ลบ
                                    </a>
                                </td>
                            </tr>
                        <?php $counter++;
                        endforeach; ?>

                        <?php if (count($majors) === 0): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">
                                    — ไม่มีข้อมูลสาขา —
                                </td>
                            </tr>
                        <?php endif; ?>

                    </tbody>
                </table>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>