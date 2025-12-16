<?php
session_start();
require_once '../../configs/connect.php';
require_once '../../configs/student_only.php';

// Get current student info
$auth_id = $_SESSION['auth_id'];

$sql = "
    SELECT s.*
    FROM students s
    WHERE s.auth_id = :auth_id
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->execute([':auth_id' => $auth_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("ไม่พบข้อมูลนักศึกษา");
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>My Profile</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <main>
        <div class="container mt-5">
            <h3 class="mb-4">My Profile</h3>

            <form action="../../backend/student_action.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <select name="title" class="form-select" required>
                        <option value="">-- Select Title --</option>
                        <option value="นาย" <?php echo ($student['title'] == 'นาย') ? 'selected' : ''; ?>>นาย</option>
                        <option value="นาง" <?php echo ($student['title'] == 'นาง') ? 'selected' : ''; ?>>นาง</option>
                        <option value="นางสาว" <?php echo ($student['title'] == 'นางสาว') ? 'selected' : ''; ?>>นางสาว</option>
                        <option value="Mr" <?php echo ($student['title'] == 'Mr') ? 'selected' : ''; ?>>Mr</option>
                        <option value="Ms" <?php echo ($student['title'] == 'Ms') ? 'selected' : ''; ?>>Ms</option>
                    </select>
                </div>

                <!-- First name -->
                <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="firstname" class="form-control" value="<?php echo htmlspecialchars($student['firstname']); ?>" required>
                </div>

                <!-- Last name -->
                <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($student['lastname']); ?>" required>
                </div>

                <!-- Student ID -->
                <div class="mb-3">
                    <label class="form-label">Student ID</label>
                    <input type="text" name="student_id" class="form-control" value="<?php echo htmlspecialchars($student['student_id']); ?>" required>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-success" name="update_student">
                    Update Profile
                </button>

                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>