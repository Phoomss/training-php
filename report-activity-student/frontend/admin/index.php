<?php
require_once '../../configs/connect.php';
require_once '../../configs/admin_only.php';

// Count total students
$stmt = $conn->prepare("SELECT COUNT(*) FROM students");
$stmt->execute();
$total_students = $stmt->fetchColumn();

// Count total activities
$stmt = $conn->prepare("SELECT COUNT(*) FROM activites");
$stmt->execute();
$total_activities = $stmt->fetchColumn();

// Count total registrations
$stmt = $conn->prepare("SELECT COUNT(*) FROM activity_details");
$stmt->execute();
$total_registrations = $stmt->fetchColumn();

// Get recent activities
$stmt = $conn->prepare("SELECT * FROM activites ORDER BY date DESC, time DESC LIMIT 5");
$stmt->execute();
$recent_activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent registrations
$stmt = $conn->prepare("
    SELECT ad.*, a.activity_name, s.firstname, s.lastname, s.student_id
    FROM activity_details ad
    JOIN activites a ON ad.activity_id = a.id
    JOIN students s ON ad.student_id = s.student_id
    ORDER BY ad.created_at DESC
    LIMIT 5
");
$stmt->execute();
$recent_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Admin Dashboard</title>
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
        <h1>Admin Dashboard</h1>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>นักศึกษา</h4>
                                <h2><?php echo $total_students; ?></h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-people" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>กิจกรรม</h4>
                                <h2><?php echo $total_activities; ?></h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-calendar-event" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>การลงทะเบียน</h4>
                                <h2><?php echo $total_registrations; ?></h2>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-journal-text" style="font-size: 3rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities and Registrations -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>กิจกรรมล่าสุด</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($recent_activities) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>ชื่อกิจกรรม</th>
                                            <th>วันที่</th>
                                            <th>เวลา</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_activities as $activity): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                                                <td><?php echo htmlspecialchars($activity['date']); ?></td>
                                                <td><?php echo htmlspecialchars($activity['time']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">ไม่มีกิจกรรมล่าสุด</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>การลงทะเบียนล่าสุด</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($recent_registrations) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>นักศึกษา</th>
                                            <th>กิจกรรม</th>
                                            <th>วันที่ลงทะเบียน</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_registrations as $reg): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($reg['firstname'] . ' ' . $reg['lastname']); ?></td>
                                                <td><?php echo htmlspecialchars($reg['activity_name']); ?></td>
                                                <td><?php echo htmlspecialchars($reg['created_at']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">ไม่มีการลงทะเบียนล่าสุด</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h5>การจัดการด่วน</h5>
                <div class="d-flex gap-2">
                    <a href="activites.php" class="btn btn-primary">จัดการกิจกรรม</a>
                    <a href="form_activity.php" class="btn btn-success">เพิ่มกิจกรรมใหม่</a>
                    <a href="../student/form_student.php" class="btn btn-info">จัดการนักศึกษา</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>