<?php
require_once '../../configs/connect.php';
require_once '../../configs/admin_only.php';

// Get activity if passed
$activity_id = $_GET['activity_id'] ?? null;

// Get all students for the dropdown
$stmt = $conn->prepare("SELECT * FROM students ORDER BY student_id");
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get activities for dropdown (in case we need to select different activity)
$stmt = $conn->prepare("SELECT * FROM activites ORDER BY activity_name");
$stmt->execute();
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <title>Add Student to Activity</title>
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
            <h3>Add Student to Activity</h3>

            <form action="../../backend/activity_detail_action.php" method="POST">
                <div class="mb-3">
                    <label for="activity_id" class="form-label">Select Activity</label>
                    <select name="activity_id" id="activity_id" class="form-select" required>
                        <option value="">-- Select Activity --</option>
                        <?php foreach ($activities as $act): ?>
                            <option value="<?php echo $act['id']; ?>"
                                <?php echo ($activity_id && $act['id'] == $activity_id) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($act['activity_name']); ?> (<?php echo $act['date']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="student_id" class="form-label">Select Student</label>
                    <select name="student_id" id="student_id" class="form-select" required>
                        <option value="">-- Select Student --</option>
                        <?php foreach ($students as $student): ?>
                            <option value="<?php echo $student['student_id']; ?>">
                                <?php echo htmlspecialchars($student['student_id'] . ' - ' . $student['title'] . $student['firstname'] . ' ' . $student['lastname']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-success" name="add_activity_detail">Add to Activity</button>
                <a href="activites.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </main>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>

</html>