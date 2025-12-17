<!doctype html>
<html lang="en">

<head>
    <title>สมัครสมาชิก - ระบบแจ้งซ่อม</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body>
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card shadow p-4" style="width: 450px;">
                <div class="mb-4">
                    <h1>สมัครสมาชิก</h1>
                    <p class="text-muted">ระบบแจ้งซ่อม</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="backend/auth_action.php" method="post">
                    <input type="hidden" name="register" value="1">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">บทบาท *</label>
                        <select class="form-select" name="role" id="role" required>
                            <option value="">-- เลือกบทบาท --</option>
                            <option value="student">นักศึกษา</option>
                            <option value="technical">ช่างเทคนิค</option>
                            <option value="staff">เจ้าหน้าที่</option>
                            <option value="admin">ผู้ดูแลระบบ</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        สมัครสมาชิก
                    </button>
                </form>
                
                <div class="text-center">
                    <a href="index.php">กลับไปหน้าเข้าสู่ระบบ</a>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>