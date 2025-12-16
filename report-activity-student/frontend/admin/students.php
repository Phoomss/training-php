<?php
require_once '../../configs/connect.php';
require_once '../../configs/admin_only.php';

// Get all students
$stmt = $conn->prepare("SELECT s.*, a.username FROM students s JOIN auths a ON s.auth_id = a.id ORDER BY s.student_id");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Manage Students</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../layouts/navbar.php'; ?>

    <main class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h3>จัดการนักศึกษา</h3>
            </div>
            <div class="col-md-6 d-flex justify-content-end">
                <a href="form_student.php" class="btn btn-primary">เพิ่มนักศึกษา</a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Student ID</th>
                        <th scope="col">ชื่อ-สกุล</th>
                        <th scope="col">Username</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <th scope="row"><?php echo $index + 1; ?></th>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['title'] . $student['firstname'] . ' ' . $student['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td>
                                    <a href="../../backend/student_action.php?delete_student=<?php echo $student['id']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('คุณต้องการลบข้อมูลนักศึกษานี้หรือไม่?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">ยังไม่มีข้อมูลนักศึกษา</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>