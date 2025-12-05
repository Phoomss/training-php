<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Login</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd, #6f42c1);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-card {
            width: 100%;
            max-width: 430px;
            background: #ffffffdd;
            backdrop-filter: blur(6px);
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.15);
        }

        .login-title {
            font-weight: 700;
        }
    </style>
</head>

<body>

    <div class="login-card">

        <h3 class="text-center mb-4 login-title">🔐 Login Account</h3>

        <form action="./backend/auth_api.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" name="username" placeholder="ชื่อผู้ใช้" required minlength="4">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" name="password" placeholder="รหัสผ่าน" required minlength="6">
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100 mt-3 py-2">
                เข้าสู่ระบบ
            </button>

            <!-- <a href="register.php" class="btn btn-outline-light w-100 mt-3 py-2 text-dark bg-white">
                สมัครสมาชิก
            </a> -->

        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
