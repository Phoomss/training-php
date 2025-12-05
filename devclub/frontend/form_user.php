<?php
require_once "../configs/admin_only.php";
require_once "../configs/connect.php";

$editing = false;
$user = null;

if (isset($_GET['id'])) {
    $editing = true;
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: users.php?error=ไม่พบข้อมูลผู้ใช้งาน");
        exit;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $editing ? "แก้ไขผู้ใช้งาน" : "เพิ่มผู้ใช้งานใหม่" ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php require_once "../layouts/navbar.php"; ?>

<div class="container py-5">

    <div class="card shadow mx-auto" style="max-width: 650px;">
        <div class="card-body">

            <h3 class="mb-4">
                <?= $editing ? "แก้ไขผู้ใช้งาน" : "เพิ่มผู้ใช้งานใหม่" ?>
            </h3>

            <!-- Alert Error -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">⚠ <?= htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <form action="../backend/auth_api.php" method="POST">

                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                <?php endif; ?>

                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" class="form-control" name="username"
                           value="<?= $editing ? htmlspecialchars($user['username']) : "" ?>"
                           required minlength="4">
                </div>

                <!-- Password (only for new users or when changing) -->
                <div class="mb-3">
                    <label class="form-label">รหัสผ่าน</label>
                    <input type="<?= $editing ? 'password' : 'text' ?>" class="form-control" name="password"
                           <?= $editing ? '' : 'required' ?> minlength="6"
                           placeholder="<?= $editing ? 'ปล่อยว่างไว้ถ้าไม่ต้องการเปลี่ยน' : 'กรุณาระบุรหัสผ่าน' ?>">
                </div>

                <!-- Confirm Password (only for new users or when changing) -->
                <?php if (!$editing): ?>
                <div class="mb-3">
                    <label class="form-label">ยืนยันรหัสผ่าน</label>
                    <input
                        type="password"
                        class="form-control"
                        name="confirm_password"
                        required
                        minlength="6"
                        placeholder="ยืนยันรหัสผ่าน">
                </div>
                <?php endif; ?>

                <!-- Role -->
                <div class="mb-3">
                    <label class="form-label">บทบาท</label>
                    <select class="form-select" name="role" required>
                        <option value="USER" <?= ($editing && $user['role'] === 'USER') ? 'selected' : '' ?>>USER</option>
                        <option value="ADMIN" <?= ($editing && $user['role'] === 'ADMIN') ? 'selected' : '' ?>>ADMIN</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit"
                        class="btn btn-<?= $editing ? "warning" : "primary" ?>"
                        name="<?= $editing ? "update_user" : "add_user" ?>">
                        <?= $editing ? "บันทึกการแก้ไข" : "เพิ่มผู้ใช้งาน" ?>
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>