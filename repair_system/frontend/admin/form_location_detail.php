<?php
session_start(); // Ensure session is started
require_once '../../configs/connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../frontend/login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$locationDetail = null;

/* ---------- location ---------- */
$stmt = $conn->prepare("SELECT * FROM location ORDER BY id DESC");
$stmt->execute();
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------- edit mode ---------- */
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM location_detail WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $locationDetail = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$locationDetail) {
        header("Location: location.php");
        exit;
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <title><?= $id ? 'Edit Location Detail' : 'Add Location Detail' ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-danger-subtle">

<?php require_once '../layouts/navbar.php'; ?>

<main class="container mt-5">
    <div class="card shadow p-4">

        <h3 class="mb-4">
            <?= $id ? '✏️ แก้ไขสถานที่' : '➕ เพิ่มสถานที่' ?>
        </h3>

        <form action="../../backend/location_detail_action.php" method="POST">

            <?php if ($id): ?>
                <input type="hidden" name="id" value="<?= $locationDetail['id']; ?>">
            <?php endif; ?>

            <!-- Location -->
            <div class="mb-3">
                <label class="form-label">เลือกตึก</label>
                <select class="form-select" name="location_id" required>
                    <option value="">-- เลือกตึก --</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= $loc['id']; ?>"
                            <?= ($locationDetail && $locationDetail['location_id'] == $loc['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($loc['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Floor -->
            <div class="mb-3">
                <label class="form-label">ชั้น</label>
                <input type="number" class="form-control" name="floor"
                       value="<?= htmlspecialchars($locationDetail['floor'] ?? '') ?>" required>
            </div>

            <!-- Room -->
            <div class="mb-3">
                <label class="form-label">เลขห้อง</label>
                <input type="number" class="form-control" name="room"
                       value="<?= htmlspecialchars($locationDetail['room'] ?? '') ?>" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit"
                        name="<?= $id ? 'update_location_detail' : 'add_location_detail' ?>"
                        class="btn btn-primary">
                    <?= $id ? 'อัปเดต' : 'บันทึก' ?>
                </button>
                <a href="location.php" class="btn btn-secondary">ยกเลิก</a>
            </div>

        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
