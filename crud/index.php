<?php
require_once "connect.php";

$editData = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=:id");
    $stmt->bindParam(':id', $_GET['edit']);
    $stmt->execute();
    $editData = $stmt->fetch(PDO::FETCH_ASSOC);
}

$stmt = $conn->prepare("SELECT * FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User CRUD</title>
</head>
<body>

<h2>User List</h2>
<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th><th>NAME</th><th>EMAIL</th><th>PHONE</th><th>ACTION</th>
    </tr>
    <?php foreach($users as $row): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td>
                <a href="index.php?edit=<?= $row['id'] ?>">Edit</a> |
                <a href="user_api.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h2><?= $editData ? "Edit User" : "Add User" ?></h2>
<form action="user_api.php" method="post">
    <?php if($editData): ?>
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
    <?php endif; ?>

    <input type="text" name="name" placeholder="Name" value="<?= $editData['name'] ?? '' ?>" required>
    <input type="email" name="email" placeholder="Email" value="<?= $editData['email'] ?? '' ?>" required>
    <input type="text" name="phone" placeholder="Phone" value="<?= $editData['phone'] ?? '' ?>" required>
    
    <button type="submit" name="<?= $editData ? 'update' : 'add' ?>">
        <?= $editData ? 'Update' : 'Submit' ?>
    </button>
</form>

</body>
</html>
