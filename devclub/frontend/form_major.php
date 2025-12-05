<?php
require_once "../configs/admin_only.php";
require_once "../configs/connect.php";

// ตรวจสอบว่าเป็นโหมดแก้ไขหรือไม่
$isEdit = false;
$major = null;

if (isset($_GET['id'])) {
    $isEdit = true;
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM majors WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $major = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$major) {
        header("Location: majors.php?error=Major Not Found");
        exit();
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $isEdit ? "Edit Major" : "Create Major" ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php require_once "../layouts/navbar.php"; ?>

<div class="container py-5">

    <div class="card shadow mx-auto" style="max-width: 550px;">
        <div class="card-body">

            <h3 class="mb-4">
                <?= $isEdit ? "แก้ไขสาขา" : "เพิ่มสาขาใหม่" ?>
            </h3>

            <!-- Error -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <!-- Success -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">ดำเนินการสำเร็จ!</div>
            <?php endif; ?>

            <form action="../backend/major_api.php" method="POST">

                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?= $major['id'] ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">ชื่อสาขา</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="name"
                        value="<?= $isEdit ? htmlspecialchars($major['name']) : "" ?>"
                        placeholder="เช่น วิศวกรรมซอฟต์แวร์"
                        required
                    >
                </div>

                <div class="d-grid">
                    <button 
                        type="submit" 
                        class="btn btn-<?= $isEdit ? "warning" : "primary" ?>" 
                        name="<?= $isEdit ? "update" : "add" ?>"
                    >
                        <?= $isEdit ? "อัปเดต" : "บันทึก" ?>
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
