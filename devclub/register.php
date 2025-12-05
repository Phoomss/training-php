<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Register</title>

    <!-- Bootstrap -->
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

        .register-card {
            width: 100%;
            max-width: 430px;
            background: #ffffffdd;
            backdrop-filter: blur(6px);
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.15);
        }

        .title {
            font-weight: 700;
        }
    </style>
</head>

<body>

    <div class="register-card">

        <h3 class="text-center mb-4 title">📝 Create Account</h3>

        <form action="./backend/auth_api.php" method="POST">

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="username" 
                        placeholder="ชื่อผู้ใช้" 
                        required minlength="4">
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input 
                        type="password" 
                        class="form-control" 
                        name="password" 
                        placeholder="รหัสผ่าน" 
                        required minlength="6">
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                    <input 
                        type="password" 
                        class="form-control" 
                        name="confirm_password" 
                        placeholder="ยืนยันรหัสผ่าน" 
                        required>
                </div>
            </div>

            <!-- Role -->
            <div class="mb-3">
                <label class="form-label">Role</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-people"></i></span>
                    <select class="form-select" name="role" required>
                        <option value="">-- เลือกบทบาท --</option>
                        <option value="USER">User</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" name="register" class="btn btn-primary w-100 mt-2 py-2">
                สมัครสมาชิก
            </button>

            <!-- Back to login -->
            <a href="index.php" class="btn btn-outline-light text-dark bg-white w-100 mt-3 py-2">
                กลับไปหน้าเข้าสู่ระบบ
            </a>

        </form>
    </div>

    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            const pass = document.querySelector('input[name="password"]').value;
            const confirm = document.querySelector('input[name="confirm_password"]').value;

            if (pass !== confirm) {
                e.preventDefault();
                alert("รหัสผ่านไม่ตรงกัน!");
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
