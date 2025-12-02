<?php
require_once "connect.php";

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM positions WHERE id=:id");
    $stmt->bindParam(":id", $_GET['edit']);
    $stmt->execute();
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $conn->prepare("SELECT * FROM positions");
$stmt->execute();
$positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
// var_dump($positions);

$stmt = $conn->prepare("SELECT * FROM users JOIN positions ON users.position_id = positions.id");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
// print_r($users);
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
    <h2><?= $editData ? "Edit Position" : "Add Position" ?>?</h2>
    <form action="position_api.php" method="post">
        <?php if ($editData): ?>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>
        <input type="text" name="position_name" placeholder="position name" value="<?= $editData['position_name'] ?? '' ?>" required>

        <button type="submit" class="btn btn-primary" name="<?= $editData ? 'update' : 'add' ?>">
            <?= $editData ? 'Update' : 'Submit' ?>
        </button>
    </form>
    <div class="table-responsive">
        <table
            class="table table-primary">
            <thead>
                <tr>
                    <th scope="col">id</th>
                    <th scope="col">position name</th>
                    <th scope="col">actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($positions as $row): ?>
                    <tr class="">
                        <td scope="row"><?= $row['id'] ?></td>
                        <td><?= $row['position_name'] ?></td>
                        <td>
                            <a href="index.php?edit=<?= $row['id'] ?>">Edit</a> |
                            <a href="position_api.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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