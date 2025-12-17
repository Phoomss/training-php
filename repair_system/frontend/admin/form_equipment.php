<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$equipment = null;

/* ---------- category ---------- */
$stmt = $conn->prepare("SELECT * FROM categories ORDER BY id DESC");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- edit mode ---------- */
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM equipment WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$equipment) {
        header("Location: index.php");
        exit;
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title><?= $id ? 'Edit Equipment' : 'Add Equipment' ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-danger-subtle">

    <?php require_once '../layouts/navbar.php'; ?>

    <main class="container mt-5">
        <div class="card shadow p-4">

            <h3 class="mb-4">
                <?= $id ? '✏️ แก้ไขอุปกรณ์' : '➕ เพิ่มอุปกรณ์' ?>
            </h3>

            <form action="../../backend/equipment_action.php" method="POST">

                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?= $equipment['id']; ?>">
                <?php endif; ?>

                <!-- Category -->
                <div class="mb-3">
                    <label class="form-label">หมวดหมู่อุปกรณ์</label>
                    <select class="form-select" name="category_id" required>
                        <option value="">-- เลือกหมวดหมู่ --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id']; ?>" <?= ($equipment && $equipment['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Name -->
                <div class="mb-3">
                    <label class="form-label">ชื่ออุปกรณ์</label>
                    <input type="text" class="form-control" name="name"
                        value="<?= htmlspecialchars($equipment['name'] ?? '') ?>" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" name="<?= $id ? 'update_equipment' : 'add_equipment' ?>"
                        class="btn btn-primary">
                        <?= $id ? 'อัปเดต' : 'บันทึก' ?>
                    </button>
                    <a href="index.php" class="btn btn-secondary">ยกเลิก</a>
                </div>

            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>