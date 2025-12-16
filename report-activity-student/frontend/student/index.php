<?php
session_start();
require_once '../../configs/connect.php';
require_once '../../configs/student_only.php';

/*
    ตอน login ต้องมี:
    $_SESSION['auth_id']
*/

$auth_id = $_SESSION['auth_id'] ?? null;

if (!$auth_id) {
    header("Location: ../login.php");
    exit();
}

/* ดึงข้อมูล student ของตัวเองจาก auth_id */
$sql = "
    SELECT s.*
    FROM students s
    WHERE s.auth_id = :auth_id
    LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':auth_id' => $auth_id
]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

// If student doesn't exist, redirect to profile to create it
if (!$student) {
    // Create a minimal student entry first
    $stmt_create = $conn->prepare("
        INSERT INTO students (auth_id, student_id, title, firstname, lastname)
        VALUES (:auth_id, :student_id, :title, :firstname, :lastname)
    ");

    $stmt_create->execute([
        ':auth_id' => $auth_id,
        ':student_id' => $_SESSION['username'], // Use username as initial student_id
        ':title' => '',
        ':firstname' => $_SESSION['username'], // Use username as initial first name
        ':lastname' => ''
    ]);

    // Get the newly created student record
    $new_student_id = $conn->lastInsertId();

    $stmt->execute([
        ':auth_id' => $auth_id
    ]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get activities the student is registered for (limit 5 most recent)
$stmt = $conn->prepare("
    SELECT a.*, ad.created_at as registered_date
    FROM activites a
    JOIN activity_details ad ON a.id = ad.activity_id
    WHERE ad.student_id = :student_id
    ORDER BY a.date DESC, a.time DESC
    LIMIT 5
");
$stmt->execute([':student_id' => $student['student_id']]);
$recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>My Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../layouts/navbar.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h3>ข้อมูลของฉัน</h3>
                <table class="table table-bordered mt-3">
                    <tr>
                        <th>ชื่อ-สกุล</th>
                        <td><?= htmlspecialchars($student['title'] . $student['firstname'] . ' ' . $student['lastname']); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Student ID</th>
                        <td><?= htmlspecialchars($student['student_id']); ?></td>
                    </tr>
                </table>
                <a href="profile.php" class="btn btn-primary">แก้ไขข้อมูลส่วนตัว</a>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h4>กิจกรรมล่าสุดของฉัน</h4>
                <?php if (count($recent_activities) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">ชื่อกิจกรรม</th>
                                    <th scope="col">วันที่</th>
                                    <th scope="col">เวลา</th>
                                    <th scope="col">ลงทะเบียนเมื่อ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_activities as $index => $activity): ?>
                                    <tr>
                                        <th scope="row"><?php echo $index + 1; ?></th>
                                        <td><?= htmlspecialchars($activity['activity_name']); ?></td>
                                        <td><?= htmlspecialchars($activity['date']); ?></td>
                                        <td><?= htmlspecialchars($activity['time']); ?></td>
                                        <td><?= htmlspecialchars($activity['registered_date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="activity_detail.php" class="btn btn-primary">ดูกิจกรรมทั้งหมด</a>
                <?php else: ?>
                    <p class="text-muted">ยังไม่ได้ลงทะเบียนกิจกรรมใดๆ</p>
                    <a href="activity_detail.php" class="btn btn-primary">ดูกิจกรรมที่เปิดให้ลงทะเบียน</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>