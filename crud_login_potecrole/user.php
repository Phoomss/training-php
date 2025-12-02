<?php
require_once "connect.php";
require_once "admin_only.php";
// ดึงข้อมูลสำหรับ edit
$editData = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=:id");
    $stmt->bindParam(":id", $_GET['edit'], PDO::PARAM_INT);
    $stmt->execute();
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no user found with the given ID, redirect with error
    if (!$editData) {
        header("Location: user.php?error=" . urlencode("User not found"));
        exit();
    }
} elseif (isset($_GET['edit'])) {
    // Invalid ID provided
    header("Location: user.php?error=" . urlencode("Invalid user ID"));
    exit();
}

// ดึง positions และ users สำหรับ display
$positions = $conn->query("SELECT * FROM positions")->fetchAll(PDO::FETCH_ASSOC);
$users = $conn->query("
    SELECT users.id AS user_id, users.name, users.email, users.phone, positions.position_name
    FROM users
    JOIN positions ON users.position_id = positions.id
")->fetchAll(PDO::FETCH_ASSOC);
?>


<!doctype html>
<html lang="en">

<head>
    <title>Title</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
</head>

<body>
    <h2><?= $editData ? "Edit User" : "Add User" ?></h2>
    <form action="user_api.php" method="post" class="mb-4">

        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= htmlspecialchars($editData['id']) ?>">
        <?php endif; ?>

        <div class="mb-3">
            <input type="text" name="name" placeholder="Name" class="form-control"
                value="<?= htmlspecialchars(isset($editData['name']) ? $editData['name'] : '') ?>" required>
        </div>

        <div class="mb-3">
            <input type="email" name="email" placeholder="Email" class="form-control"
                value="<?= htmlspecialchars(isset($editData['email']) ? $editData['email'] : '') ?>" required>
        </div>

        <div class="mb-3">
            <input type="tel" name="phone" placeholder="Phone" class="form-control"
                value="<?= htmlspecialchars(isset($editData['phone']) ? $editData['phone'] : '') ?>" required>
        </div>

        <div class="mb-3">
            <select class="form-select" name="position_id" required>
                <option value="">-- Select Position --</option>
                <?php foreach ($positions as $row) : ?>
                    <option value="<?= $row['id'] ?>"
                        <?= ($editData && $editData['position_id'] == $row['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['position_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary"
            name="<?= $editData ? 'update' : 'add' ?>">
            <?= $editData ? 'Update' : 'Submit' ?>
        </button>
    </form>
    <div class="table-responsive">
        <table
            class="table table-primary">
            <thead>
                <tr>
                    <th scope="col">id</th>
                    <th scope="col">name</th>
                    <th scope="col">email</th>
                    <th scope="col">phone</th>
                    <th scope="col">position</th>
                    <th scope="col">actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1;
                foreach ($users as $row): ?>
                    <tr class="">
                        <td scope="row"><?= $counter ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['position_name']) ?></td>
                        <td>
                            <a href="user.php?edit=<?= $row['user_id'] ?>">Edit</a> |
                            <a href="user_api.php?delete=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php $counter++;
                endforeach; ?>
            </tbody>
        </table>
        <form action="logout.php" method="POST">
            <button type="submit" class="btn btn-info">Logout</button>
        </form>
    </div>




    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>