<?php
require_once '../../configs/connect.php';
require_once '../../configs/admin_only.php';

// Fetch activities from database
$stmt = $conn->prepare("SELECT * FROM activites ORDER BY date DESC, time DESC");
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Activities Dashboard</title>
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
                    <h1 class="bi bi-people-fill">Dashboard</h1>
                </div>
                <div class="col-md-6 d-flex justify-content-end align-items-center">
                    <a class="btn btn-primary btn-lg px-4 py-2 mx-1" href="form_activity.php">
                        <i class="bi bi-plus-circle me-2"></i>Add Activity
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Name</th>
                            <th scope="col">Time</th>
                            <th scope="col">Date</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (count($activities) > 0): ?>
                            <?php foreach ($activities as $index => $activity): ?>
                                <tr>
                                    <th scope="row"><?php echo $index + 1; ?></th>
                                    <td><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['time']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['date']); ?></td>
                                    <td>
                                        <a href="activity_detail.php?id=<?php echo $activity['id']; ?>"
                                           class="btn btn-info btn-sm">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="form_activity.php?id=<?php echo $activity['id']; ?>"
                                           class="btn btn-warning btn-sm">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="../../backend/activity_action.php?delete_activity=<?php echo $activity['id']; ?>"
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบกิจกรรมนี้?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">ไม่พบข้อมูลกิจกรรม</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer></footer>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>