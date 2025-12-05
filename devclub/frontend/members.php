<?php
require_once "../configs/admin_only.php";
require_once "../configs/connect.php";

// ดึงข้อมูล members JOIN majors
$sql = "SELECT m.*, mj.name AS major_name 
        FROM members m 
        LEFT JOIN majors mj ON m.major = mj.id 
        ORDER BY m.id ASC";

$stmt = $conn->query($sql);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการสมาชิก</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php require_once "../layouts/navbar.php"; ?>

    <div class="container py-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>รายการสมาชิก</h2>
            <a href="form_member.php" class="btn btn-primary">+ เพิ่มสมาชิกใหม่</a>
        </div>

        <!-- Alerts -->
        <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">

                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>Email</th>
                            <th>สาขา</th>
                            <th>ชั้นปี</th>
                            <th width="140">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php $counter = 1;
                        foreach ($members as $m): ?>
                            <tr>
                                <td><?= $counter ?></td>
                                <td><?= htmlspecialchars($m['title'] . " " . $m['firstname'] . " " . $m['lastname']) ?></td>
                                <td><?= htmlspecialchars($m['email']) ?></td>
                                <td><?= htmlspecialchars($m['major_name'] ?? "-") ?></td>
                                <td><?= htmlspecialchars($m['year']) ?></td>

                                <td>
                                    <a href="form_member.php?id=<?= $m['id'] ?>" class="btn btn-warning btn-sm">แก้ไข</a>

                                    <a href="../backend/member_api.php?delete=<?= $m['id'] ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('ยืนยันการลบสมาชิกนี้?');">
                                        ลบ
                                    </a>
                                </td>
                            </tr>
                        <?php $counter++;
                        endforeach; ?>

                        <?php if (count($members) === 0): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    — ไม่มีข้อมูลสมาชิก —
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