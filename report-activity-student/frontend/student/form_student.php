<?php
require_once '../../configs/connect.php';

/* ดึง user จากตาราง auth (เฉพาะ role student ก็ได้) */
$stmt = $conn->prepare("SELECT id, username FROM auths WHERE role = 'STUDENT'");
$stmt->execute();
$authUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Form Student</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <main>
        <div class="container mt-5">
            <h3 class="mb-4">Add New Student</h3>

            <form action="../../backend/student_action.php" method="POST">

                <!-- Auth User (สำคัญมาก) -->
                <div class="mb-3">
                    <label class="form-label">Login Account</label>
                    <select name="auth_id" class="form-select" required>
                        <option value="">-- Select User --</option>
                        <?php foreach ($authUsers as $user): ?>
                            <option value="<?= $user['id']; ?>">
                                <?= htmlspecialchars($user['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <select name="title" class="form-select" required>
                        <option value="">-- Select Title --</option>
                        <option value="นาย">นาย</option>
                        <option value="นาง">นาง</option>
                        <option value="นางสาว">นางสาว</option>
                        <option value="Mr">Mr</option>
                        <option value="Ms">Ms</option>
                    </select>
                </div>

                <!-- First name -->
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="firstname" class="form-control" required>
                </div>

                <!-- Last name -->
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lastname" class="form-control" required>
                </div>

                <!-- Student ID -->
                <div class="mb-3">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="student_id" class="form-control" required>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-success" name="add_student">
                    Save Student
                </button>

                <a href="index.php" class="btn btn-secondary">Cancel</a>

            </form>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>