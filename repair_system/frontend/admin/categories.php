<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM categories ORDER BY id DESC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Categories</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../layouts/navbar.php' ?>

    <main class="container mt-5">
        <div class="card shadow p-4">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3>🏷️ หมวดหมู่</h3>
                <a href="form_category.php" class="btn btn-primary">➕ เพิ่มหมวดหมู่</a>
            </div>

            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>ชื่อหมวดหมู่</th>
                        <th>วันที่เพิ่ม</th>
                        <th width="150">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">ไม่มีข้อมูล</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($categories as $i => $category): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= $category['created_at'] ?></td>
                                <td>
                                    <a href="form_category.php?id=<?= $category['id'] ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                                    <a href="../../backend/category_action.php?delete_category=<?= $category['id'] ?>"
                                        class="btn btn-sm btn-danger" onclick="return confirm('ยืนยันการลบ?')">
                                        ลบ
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>