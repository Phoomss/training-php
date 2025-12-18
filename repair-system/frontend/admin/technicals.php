<?php
require_once('../../configs/admin_only.php');
require_once('../../configs/connect.php');

// Fetch all technical staff with related data
$stmt = $conn->prepare("
    SELECT t.*, a.username, a.role
    FROM technical t
    LEFT JOIN auth a ON t.auth_id = a.id
    ORDER BY t.firstname ASC
");
$stmt->execute();
$technicals = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการช่างเทคนิค - ระบบแจ้งซ่อม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include_once('../../frontend/layouts/navbar.php'); ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>จัดการช่างเทคนิค</h2>
        </div>

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

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>เบอร์ติดต่อ</th>
                                <th>ชื่อผู้ใช้</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($technicals) > 0): ?>
                                <?php foreach ($technicals as $index => $technical): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($technical['title'] . ' ' . $technical['firstname'] . ' ' . $technical['lastname']) ?></td>
                                        <td><?= htmlspecialchars($technical['phone']) ?></td>
                                        <td><?= htmlspecialchars($technical['username']) ?></td>
                                        <td>
                                            <a href="../backend/technical_action.php?delete_technical=<?= $technical['id'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('คุณต้องการลบข้อมูลช่างเทคนิคนี้หรือไม่?')">ลบ</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">ไม่มีข้อมูลช่างเทคนิค</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>