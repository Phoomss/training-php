<?php
require_once('../../configs/connect.php');
// require_once('../../configs/admin_only.php');

$technical = null;
$isEdit = false;

// Check if editing an existing technical staff
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $conn->prepare("
            SELECT t.*, a.username, a.password 
            FROM technical t
            LEFT JOIN auth a ON t.auth_id = a.id 
            WHERE t.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $technical = $stmt->fetch();
        
        if (!$technical) {
            header("Location: technical.php?error=" . urlencode("ไม่พบข้อมูลช่างเทคนิคนี้"));
            exit();
        }
        
        $isEdit = true;
    } catch (PDOException $e) {
        die("Query Failed: " . $e->getMessage());
    }
}
?>

<!doctype html>
<html lang="th">
<head>
    <title><?= $isEdit ? 'แก้ไขข้อมูลช่างเทคนิค' : 'เพิ่มช่างเทคนิคใหม่' ?></title>
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

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><?= $isEdit ? 'แก้ไขข้อมูลช่างเทคนิค' : 'เพิ่มช่างเทคนิคใหม่' ?></h5>
                    </div>
                    <div class="card-body">
                        <form action="../../backend/technical_action.php" method="post">
                            <?php if ($isEdit): ?>
                                <input type="hidden" name="id" value="<?= $technical['id'] ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">คำนำหน้า *</label>
                                <select name="title" id="title" class="form-select" required>
                                    <option value="">เลือกคำนำหน้า</option>
                                    <option value="นาย" <?= ($isEdit && $technical['title'] === 'นาย') ? 'selected' : '' ?>>นาย</option>
                                    <option value="นาง" <?= ($isEdit && $technical['title'] === 'นาง') ? 'selected' : '' ?>>นาง</option>
                                    <option value="นางสาว" <?= ($isEdit && $technical['title'] === 'นางสาว') ? 'selected' : '' ?>>นางสาว</option>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">ชื่อ *</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname" 
                                               value="<?= $isEdit ? htmlspecialchars($technical['firstname']) : '' ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">นามสกุล *</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname" 
                                               value="<?= $isEdit ? htmlspecialchars($technical['lastname']) : '' ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?= $isEdit ? htmlspecialchars($technical['phone']) : '' ?>">
                            </div>

                            <div class="mb-3">
                                <label for="username" class="form-label">ชื่อผู้ใช้งาน *</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?= $isEdit ? htmlspecialchars($technical['username']) : '' ?>" required>
                            </div>

                            <?php if (!$isEdit): ?>
                                <div class="mb-3">
                                    <label for="password" class="form-label">รหัสผ่าน *</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">กรุณาตั้งรหัสผ่านสำหรับช่างเทคนิคนี้</div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน *</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <label for="password" class="form-label">เปลี่ยนรหัสผ่าน (ไม่จำเป็น)</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">ยืนยันรหัสผ่านใหม่ (ไม่จำเป็น)</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="เว้นว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน">
                                </div>
                            <?php endif; ?>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <?= $isEdit ? 'บันทึกการแก้ไข' : 'เพิ่มช่างเทคนิค' ?>
                                </button>
                                <a href="technical.php" class="btn btn-secondary">ยกเลิก</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once '../layouts/footer.php'?>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                // Check if passwords are being changed (not empty)
                if(password !== '') {
                    if(password !== confirmPassword) {
                        e.preventDefault();
                        alert('ยืนยันรหัสผ่านไม่ตรงกัน กรุณาลองใหม่อีกครั้ง');
                        return false;
                    }
                    
                    if(password.length < 6) {
                        e.preventDefault();
                        alert('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');
                        return false;
                    }
                }
            });
        });
    </script>
</body>
</html>