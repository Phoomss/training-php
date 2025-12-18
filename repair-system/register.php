<!doctype html>
<html lang="th">
<head>
    <title>ลงทะเบียน | ระบบแจ้งซ่อม</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-info-subtle">

<main class="d-flex justify-content-center align-items-center vh-100">
    <div class="card p-5 col-12 col-md-6 col-lg-4 shadow">

        <div class="text-center mb-3">
            <h2>ลงทะเบียน</h2>
            <p class="text-muted">ระบบแจ้งซ่อมอุปกรณ์</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_GET['error']) ?>
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="backend/auth_action.php" method="post">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" minlength="4" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" minlength="4" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="student">Student</option>
                </select>
            </div>

            <button type="submit" name="register" class="btn btn-primary w-100">
                ลงทะเบียน
            </button>

        </form>

        <div class="text-center mt-3">
            <a href="index.php">เข้าสู่ระบบ</a>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
