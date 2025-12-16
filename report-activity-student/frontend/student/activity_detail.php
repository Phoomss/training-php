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

// Get all available activities (not already registered by this student)
$stmt = $conn->prepare("
    SELECT a.*
    FROM activites a
    WHERE a.id NOT IN (
        SELECT ad.activity_id
        FROM activity_details ad
        WHERE ad.student_id = :student_id
    )
    ORDER BY a.date DESC, a.time DESC
");
$stmt->execute([':student_id' => $student['student_id']]);
$available_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get activities the student is already registered for
$stmt = $conn->prepare("
    SELECT a.*, ad.created_at as registered_date
    FROM activites a
    JOIN activity_details ad ON a.id = ad.activity_id
    WHERE ad.student_id = :student_id
    ORDER BY a.date DESC, a.time DESC
");
$stmt->execute([':student_id' => $student['student_id']]);
$registered_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>My Activities</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../layouts/navbar.php'; ?>

    <main>
        <div class="container mt-5">
            <h3>กิจกรรมของฉัน</h3>

            <div class="row">
                <div class="col-md-12">
                    <h5>กิจกรรมที่ลงทะเบียนแล้ว</h5>
                    <?php if (count($registered_activities) > 0): ?>
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
                                    <?php foreach ($registered_activities as $index => $activity): ?>
                                        <tr>
                                            <th scope="row"><?php echo $index + 1; ?></th>
                                            <td><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['date']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['time']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['registered_date']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">ยังไม่ได้ลงทะเบียนกิจกรรมใดๆ</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <h5>กิจกรรมที่สามารถลงทะเบียนได้</h5>
                    <?php if (count($available_activities) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">ชื่อกิจกรรม</th>
                                        <th scope="col">วันที่</th>
                                        <th scope="col">เวลา</th>
                                        <th scope="col">การกระทำ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($available_activities as $index => $activity): ?>
                                        <tr>
                                            <th scope="row"><?php echo $index + 1; ?></th>
                                            <td><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['date']); ?></td>
                                            <td><?php echo htmlspecialchars($activity['time']); ?></td>
                                            <td>
                                                <a href="../../backend/activity_detail_action.php?student_id=<?php echo $student['student_id']; ?>&activity_id=<?php echo $activity['id']; ?>&register=1"
                                                   class="btn btn-success btn-sm"
                                                   onclick="return confirm('คุณต้องการลงทะเบียนกิจกรรมนี้หรือไม่?')">
                                                    <i class="bi bi-check-circle"></i> ลงทะเบียน
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">ไม่มีกิจกรรมที่สามารถลงทะเบียนได้</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>