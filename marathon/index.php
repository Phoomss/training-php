<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ลงทะเบียนวิ่ง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>🏃‍♂️ ลงทะเบียนวิ่งมาราธอน</h4>
        </div>

        <div class="card-body">

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">สมัครเรียบร้อย 🎉</div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">เกิดข้อผิดพลาด ❌</div>
            <?php endif; ?>

            <form method="post" action="backend/register_process.php">

                <h5>ข้อมูลผู้สมัคร</h5>
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" name="first_name" class="form-control" placeholder="ชื่อ" required>
                    </div>
                    <div class="col">
                        <input type="text" name="last_name" class="form-control" placeholder="นามสกุล" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <input type="date" name="date_of_birth" class="form-control" required>
                    </div>
                    <div class="col">
                        <select name="gender" class="form-select" required>
                            <option value="">เพศ</option>
                            <option value="Male">ชาย</option>
                            <option value="Female">หญิง</option>
                        </select>
                    </div>
                    <div class="col">
                        <input type="text" name="phone" class="form-control" placeholder="เบอร์โทร">
                    </div>
                </div>

                <hr>

                <h5>การแข่งขัน</h5>
                <select name="category_id" class="form-select mb-3" required>
                    <option value="">เลือกระยะ</option>
                    <option value="1">Mini Marathon</option>
                    <option value="2">Half Marathon</option>
                    <option value="3">Marathon</option>
                </select>

                <div class="mb-3">
                    <label>ขนาดเสื้อ</label><br>
                    <?php foreach (['S','M','L','XL'] as $s): ?>
                        <input type="radio" name="shirt_size" value="<?= $s ?>" required> <?= $s ?>
                    <?php endforeach; ?>
                </div>

                <hr>

                <h5>การรับอุปกรณ์</h5>
                <select name="shipping_id" class="form-select mb-3">
                    <option value="1">รับหน้างาน (ฟรี)</option>
                    <option value="2">จัดส่ง EMS (+90)</option>
                </select>

                <button class="btn btn-success">ยืนยันการสมัคร</button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
