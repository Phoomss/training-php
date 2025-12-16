<?php
require_once '../../configs/connect.php';
require_once '../../configs/admin_only.php';

if (!isset($_GET['id'])) {
    header("Location: activites.php?error=" . urlencode("ไม่พบ ID กิจกรรม"));
    exit();
}

$activity_id = $_GET['id'];

// Get activity info
$stmt = $conn->prepare("SELECT * FROM activites WHERE id = :id");
$stmt->execute([':id' => $activity_id]);
$activity = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$activity) {
    header("Location: activites.php?error=" . urlencode("ไม่พบกิจกรรม"));
    exit();
}

// Get students registered for this activity
$stmt = $conn->prepare("
    SELECT ad.*, s.title, s.firstname, s.lastname, s.student_id
    FROM activity_details ad
    JOIN students s ON ad.student_id = s.student_id
    WHERE ad.activity_id = :activity_id
");
$stmt->execute([':activity_id' => $activity_id]);
$registered_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Activity Details</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../../frontend/layouts/navbar.php'; ?>

    <main>
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-6 d-flex">
                    <h1>Activity Details</h1>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <a class="btn btn-secondary btn-lg px-4 py-2 mx-1" href="form_activity_detail.php?activity_id=<?php echo $activity_id; ?>">
                        <i class="bi bi-plus-circle me-2"></i>Add Student
                    </a>
                </div>
            </div>

            <!-- Activity Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4><?php echo htmlspecialchars($activity['activity_name']); ?></h4>
                </div>
                <div class="card-body">
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($activity['date']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($activity['time']); ?></p>
                </div>
            </div>

            <!-- Registered Students Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Student ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($registered_students) > 0): ?>
                            <?php foreach ($registered_students as $index => $student): ?>
                                <tr>
                                    <th scope="row"><?php echo $index + 1; ?></th>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['title'] . $student['firstname'] . ' ' . $student['lastname']); ?></td>
                                    <td>
                                        <a href="../../backend/activity_detail_action.php?delete_activity_detail=<?php echo $student['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบการลงทะเบียนของนักศึกษารายนี้?')">
                                            <i class="bi bi-trash"></i> Remove
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">ยังไม่มีนักศึกษาลงทะเบียนกิจกรรมนี้</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <a href="activites.php" class="btn btn-secondary">Back to Activities</a>
        </div>
    </main>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>