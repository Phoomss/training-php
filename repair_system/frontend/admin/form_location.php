<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$name = '';

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM location WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $location = $stmt->fetch(PDO::FETCH_ASSOC);
    $name = $location['name'] ?? '';
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Location Form</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php require_once '../layouts/navbar.php'; ?>

    <main class="container mt-5">
        <div class="card shadow p-4 mx-auto" style="max-width: 500px;">
            <h3 class="mb-4">
                <?= $id ? '✏️ แก้ไขหมวดหมู่' : '➕ เพิ่มหมวดหมู่' ?>
            </h3>

            <form action="../../backend/location_action.php" method="post">
                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?= $id ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">ชื่ออาคาร/ตึก</label>
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($name) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary" name="<?= $id ? 'update_location' : 'add_location' ?>">
                    <?= $id ? 'อัปเดต' : 'บันทึก' ?>
                </button>

                <a href="location.php" class="btn btn-secondary ms-2">กลับ</a>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>