<?php
require_once '../../configs/connect.php';
require_once '../../configs/admin_only.php';

// Check if this is an update request
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM activites WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$activity) {
        header("Location: activites.php?error=" . urlencode("ไม่พบกิจกรรม"));
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <title><?php echo isset($activity) ? 'Edit Activity' : 'Add Activity'; ?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../../frontend/layouts/navbar.php'; ?>

    <main>
        <div class="container mt-5">
            <h3><?php echo isset($activity) ? 'Edit Activity' : 'Add Activity'; ?></h3>

            <form action="../../backend/activity_action.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $activity['id'] ?? ''; ?>">

                <div class="mb-3">
                    <label for="activity_name" class="form-label">Activity Name</label>
                    <input type="text" class="form-control" name="activity_name" id="activity_name"
                           value="<?php echo htmlspecialchars($activity['activity_name'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="time" class="form-label">Time (HH:MM)</label>
                    <input type="time" class="form-control" name="time" id="time"
                           value="<?php echo htmlspecialchars($activity['time'] ?? ''); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" name="date" id="date"
                           value="<?php echo htmlspecialchars($activity['date'] ?? ''); ?>" required>
                </div>

                <button type="submit" class="btn btn-success" name="<?php echo isset($activity) ? 'update_activity' : 'add_activity'; ?>">
                    <?php echo isset($activity) ? 'Update Activity' : 'Add Activity'; ?>
                </button>

                <a href="activites.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </main>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>