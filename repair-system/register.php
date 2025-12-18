<?php

?>

<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
</head>

<body class="bg-info-subtle">
    <header>
        <!-- place navbar here -->
    </header>
    <main>
        <div class="d-flex p-5 mt-5 justify-content-center align-items-center vh-100">
            <div class="card container p-5" style="width:30%;">
                <div class="d-flex justify-content-center mb-3">
                    <h1>ลงทะเบียน</h1>
                </div>
                <div class="d-flex justify-content-center mb-3">
                    <h2>ระบบแจ้งซ่อมอุปกรณ์</h2>
                </div>


                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form action="backend/auth_action.php" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input
                            type="text"
                            class="form-control"
                            name="username"
                            id="username"
                            required
                            aria-describedby=""
                            placeholder="ชื่อผู้ใช้" />
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            class="form-control"
                            min="4"
                            name="password"
                            id="password"
                            required
                            placeholder="รหัสผ่าน" />
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Confirm Password</label>
                        <input
                            type="password"
                            class="form-control"
                            min="4"
                            name="confirm_password"
                            required
                            placeholder="ยืนยันรหัสผ่าน" />
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select
                            class="form-select form-select-lg"
                            name="role"
                            id="role"
                            required
                            onchange="toggleProfileFields()">
                            <option value="">-- เลือกบทบาท --</option>
                            <option value="student">student</option>
                        </select>
                    </div>

                    <!-- Student Profile Fields -->
                    <div id="student-fields" class="d-none">
                        <div class="mb-3">
                            <label for="student_title" class="form-label">คำนำหน้าชื่อ</label>
                            <select class="form-select" name="title" id="student_title">
                                <option value="นาย">นาย</option>
                                <option value="นาง">นาง</option>
                                <option value="นางสาว">นางสาว</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="student_firstname" class="form-label">ชื่อ</label>
                            <input type="text" class="form-control" name="firstname" id="student_firstname" placeholder="กรุณากรอกชื่อ">
                        </div>
                        <div class="mb-3">
                            <label for="student_lastname" class="form-label">นามสกุล</label>
                            <input type="text" class="form-control" name="lastname" id="student_lastname" placeholder="กรุณากรอกนามสกุล">
                        </div>
                        <div class="mb-3">
                            <label for="student_id" class="form-label">รหัสนักศึกษา</label>
                            <input type="text" class="form-control" name="student_id" id="student_id" placeholder="กรุณากรอกรหัสนักศึกษา">
                        </div>
                    </div>


                    <div>
                        <button
                            type="submit"
                            class="btn btn-primary mb-3" name="register">
                            ลงทะเบียน
                        </button>
                    </div>
                </form>

                <script>
                    function toggleProfileFields() {
                        const role = document.getElementById('role').value;
                        const studentFields = document.getElementById('student-fields');

                        // Hide student fields initially
                        studentFields.classList.add('d-none');

                        // Show fields based on selected role
                        if (role === 'student') {
                            studentFields.classList.remove('d-none');
                        }
                    }

                    // Form validation
                    document.querySelector('form').addEventListener('submit', function(e) {
                        const role = document.getElementById('role').value;

                        if (role === 'student') {
                            // Validate student fields
                            const student_title = document.getElementById('student_title').value;
                            const student_firstname = document.getElementById('student_firstname').value;
                            const student_lastname = document.getElementById('student_lastname').value;
                            const student_id = document.getElementById('student_id').value;

                            if (!student_title || !student_firstname || !student_lastname || !student_id) {
                                e.preventDefault();
                                alert('กรุณากรอกข้อมูลนักศึกษาให้ครบถ้วน');
                                return false;
                            }
                        }
                    });
                </script>
                <a href="index.php">เข้าสู่ระบบ</a>
            </div>
        </div>
    </main>
    <footer>
        <!-- place footer here -->
    </footer>
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>