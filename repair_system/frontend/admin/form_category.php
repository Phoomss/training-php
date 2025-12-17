<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$category = null;

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$category) {
        header("Location: categories.php");
        exit;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title><?= $id ? 'Edit Category' : 'Add Category' ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php require_once '../layouts/navbar.php'; ?>

    <main class="container mt-5">
        <div class="card shadow p-4">

            <h3 class="mb-4">
                <?= $id ? '✏️ แก้ไขหมวดหมู่' : '➕ เพิ่มหมวดหมู่' ?>
            </h3>

            <form action="../../backend/category_action.php" method="POST">

                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?= $category['id']; ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">ชื่อหมวดหมู่</label>
                    <input type="text" class="form-control" name="name"
                        value="<?= htmlspecialchars($category['name'] ?? '') ?>" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="<?= $id ? 'update_category' : 'add_category' ?>"
                        class="btn btn-primary">
                        <?= $id ? 'อัปเดต' : 'บันทึก' ?>
                    </button>
                    <a href="categories.php" class="btn btn-secondary">ยกเลิก</a>
                </div>

            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>