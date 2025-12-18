<?php
require_once('../../configs/connect.php');
require_once('../../configs/admin_only.php');

$equipment = null;
$error_message = '';
$status_message = '';

// Check if editing an existing equipment
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $conn->prepare("SELECT * FROM equipment WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $equipment = $stmt->fetch();

        if (!$equipment) {
            header("Location: equipment.php?error=" . urlencode("ไม่พบอุปกรณ์ที่ระบุ"));
            exit();
        }
    } catch (PDOException $e) {
        $error_message = "เกิดข้อผิดพลาดในการดึงข้อมูลอุปกรณ์";
    }
}

// Check for error messages passed via URL params
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
}
?>

<!doctype html>
<html lang="th">
<head>
    <title><?= isset($equipment) ? 'แก้ไขอุปกรณ์' : 'เพิ่มอุปกรณ์ใหม่' ?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body class="bg-light">
    <header class="bg-white shadow-sm">
        <div class="container py-3">
            <h1 class="h4 mb-0"><?= isset($equipment) ? 'แก้ไขอุปกรณ์' : 'เพิ่มอุปกรณ์ใหม่' ?></h1>
        </div>
    </header>

    <main class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <!-- Messages -->
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($error_message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="../../backend/equipment_action.php" method="post">
                            <?php if (isset($equipment)): ?>
                                <input type="hidden" name="id" value="<?= $equipment['id'] ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label for="name" class="form-label">ชื่ออุปกรณ์ *</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    name="name"
                                    id="name"
                                    value="<?= isset($equipment) ? htmlspecialchars($equipment['name']) : '' ?>"
                                    required
                                >
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="equipment.php" class="btn btn-secondary">ยกเลิก</a>
                                <button type="submit" class="btn btn-primary">
                                    <?= isset($equipment) ? 'บันทึกการแก้ไข' : 'เพิ่มอุปกรณ์' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="mt-5 py-3 bg-white border-top">
        <div class="container text-center">
            <span>&copy; 2025 ระบบแจ้งซ่อมอุปกรณ์</span>
        </div>
    </footer>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>