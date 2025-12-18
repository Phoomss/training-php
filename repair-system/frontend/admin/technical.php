<?php
require_once('../../configs/connect.php');
// require_once('../../configs/admin_only.php');

try {
    $stmt = $conn->query("
        SELECT t.*, a.username, a.created_at as auth_created_at 
        FROM technical t
        LEFT JOIN auth a ON t.auth_id = a.id 
        ORDER BY t.firstname ASC
    ");
    $technicals = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Query Failed: " . $e->getMessage());
}
?>

<!doctype html>
<html lang="th">
<head>
    <title>จัดการข้อมูลช่าง</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body class="bg-light">
    <?php require_once '../layouts/navbar.php'?>

    <main class="container mt-4">
        <!-- Messages -->
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

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="h5 mb-0">รายชื่อช่างเทคนิคทั้งหมด</h2>
            <a href="form_technical.php" class="btn btn-primary">เพิ่มช่างเทคนิคใหม่</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ลำดับ</th>
                        <th>คำนำหน้า</th>
                        <th>ชื่อ-นามสกุล</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>ชื่อผู้ใช้งาน</th>
                        <th>วันที่ลงทะเบียน</th>
                        <th>ดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($technicals) > 0): ?>
                        <?php foreach ($technicals as $index => $technical): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($technical['title']) ?></td>
                                <td><?= htmlspecialchars($technical['firstname'] . ' ' . $technical['lastname']) ?></td>
                                <td><?= htmlspecialchars($technical['phone'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($technical['username'] ?? '-') ?></td>
                                <td><?= $technical['auth_created_at'] ? date('d/m/Y H:i', strtotime($technical['auth_created_at'])) : '-' ?></td>
                                <td>
                                    <a href="form_technical.php?id=<?= $technical['id'] ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                    <a href="../../backend/technical_action.php?delete_technical=<?= $technical['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('คุณต้องการลบช่างเทคนิคนี้หรือไม่?')">ลบ</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">ไม่มีข้อมูลช่างเทคนิค</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

  <?php require_once '../layouts/footer.php'?>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>