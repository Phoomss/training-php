<?php
require_once "../configs/admin_only.php";
require_once "../configs/connect.php";

$editing = false;
$member = null;

if (isset($_GET['id'])) {
    $editing = true;
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM members WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        header("Location: members.php?error=ไม่พบข้อมูลสมาชิก");
        exit;
    }
}

// ดึงรายการสาขา
$majorStmt = $conn->query("SELECT id, name FROM majors ORDER BY name ASC");
$majors = $majorStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $editing ? "แก้ไขสมาชิก" : "เพิ่มสมาชิกใหม่" ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php require_once "../layouts/navbar.php"; ?>

<div class="container py-5">

    <div class="card shadow mx-auto" style="max-width: 650px;">
        <div class="card-body">

            <h3 class="mb-4">
                <?= $editing ? "แก้ไขสมาชิก" : "เพิ่มสมาชิกใหม่" ?>
            </h3>

            <!-- Alert Error -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">⚠ <?= htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>

            <form action="../backend/member_api.php" method="POST">

                <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?= $member['id'] ?>">
                <?php endif; ?>

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label">คำนำหน้า</label>
                    <select name="title" class="form-select" required>
                        <?php
                        $titles = ['นาย', 'นางสาว', 'นาง', 'Mr', 'Ms'];
                        foreach ($titles as $t):
                        ?>
                            <option value="<?= $t ?>"
                                <?= ($editing && $member['title'] === $t) ? "selected" : "" ?>>
                                <?= $t ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Firstname -->
                <div class="mb-3">
                    <label class="form-label">ชื่อ</label>
                    <input type="text" class="form-control" name="firstname"
                           value="<?= $editing ? htmlspecialchars($member['firstname']) : "" ?>"
                           required>
                </div>

                <!-- Lastname -->
                <div class="mb-3">
                    <label class="form-label">นามสกุล</label>
                    <input type="text" class="form-control" name="lastname"
                           value="<?= $editing ? htmlspecialchars($member['lastname']) : "" ?>"
                           required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">อีเมล</label>
                    <input type="email" class="form-control" name="email"
                           value="<?= $editing ? htmlspecialchars($member['email']) : "" ?>"
                           required>
                </div>

                <!-- Major -->
                <div class="mb-3">
                    <label class="form-label">สาขา</label>
                    <select name="major" class="form-select" required>
                        <option value="">-- เลือกสาขา --</option>
                        <?php foreach ($majors as $mj): ?>
                            <option value="<?= $mj['id'] ?>"
                                <?= ($editing && $member['major'] == $mj['id']) ? "selected" : "" ?>>
                                <?= htmlspecialchars($mj['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Year -->
                <div class="mb-3">
                    <label class="form-label">ชั้นปี</label>
                    <select name="year" class="form-select" required>
                        <?php for ($i = 1; $i <= 4; $i++): ?>
                            <option value="<?= $i ?>"
                                <?= ($editing && $member['year'] == $i) ? "selected" : "" ?>>
                                ปี <?= $i ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" 
                        class="btn btn-<?= $editing ? "warning" : "primary" ?>"
                        name="<?= $editing ? "update" : "add" ?>">
                        <?= $editing ? "บันทึกการแก้ไข" : "เพิ่มสมาชิก" ?>
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
