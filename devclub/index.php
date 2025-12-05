<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="card shadow mx-auto" style="max-width: 450px;">
        <div class="card-body">

            <h3 class="text-center mb-4 fw-bold">Create Account</h3>

            <form action="./backend/auth_api.php" method="POST">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="username" 
                        placeholder="ชื่อผู้ใช้" 
                        required minlength="4">
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        name="password" 
                        placeholder="รหัสผ่าน" 
                        required minlength="6">
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        name="confirm_password" 
                        placeholder="ยืนยันรหัสผ่าน" 
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role" required>
                        <option value="">-- เลือกบทบาท --</option>
                        <option value="USER">User</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" name="register" class="btn btn-primary">สมัครสมาชิก</button>
                </div>

            </form>
        </div>
    </div>

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
