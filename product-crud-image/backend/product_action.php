<?php
require_once '../configs/connect.php';

/* =====================
   CREATE
===================== */
if (isset($_POST['create'])) {

    $name  = $_POST['name'];
    $price = $_POST['price'];

    $img = $_FILES['image'];
    $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
    $allow = ['jpg','jpeg','png'];

    if (!in_array($ext, $allow)) {
        die('Invalid image');
    }

    $folder = __DIR__ . '/../uploads/products/';
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $newname = uniqid() . "." . $ext;
    move_uploaded_file($img['tmp_name'], $folder . $newname);

    $stmt = $conn->prepare(
        "INSERT INTO products (name, price, image) VALUES (?, ?, ?)"
    );
    $stmt->execute([$name, $price, 'uploads/products/' . $newname]);

    header('Location: ../index.php');
    exit();
}

/* =====================
   UPDATE
===================== */
if (isset($_POST['update'])) {

    $id    = $_POST['id'];
    $name  = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$id]);
    $old = $stmt->fetch();

    $imgPath = $old['image'];

    if (!empty($_FILES['image']['name'])) {

        if (file_exists('../' . $imgPath)) unlink('../' . $imgPath);

        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $newname = uniqid() . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $newname);
        $imgPath = 'uploads/products/' . $newname;
    }

    $stmt = $conn->prepare(
        "UPDATE products SET name=?, price=?, image=? WHERE id=?"
    );
    $stmt->execute([$name, $price, $imgPath, $id]);

    header('Location: ../index.php');
    exit();
}

/* =====================
   DELETE
===================== */
if (isset($_POST['delete'])) {

    $id = $_POST['id'];

    $stmt = $conn->prepare("SELECT image FROM products WHERE id=?");
    $stmt->execute([$id]);
    $p = $stmt->fetch();

    if (file_exists('../' . $p['image'])) {
        unlink('../' . $p['image']);
    }

    $conn->prepare("DELETE FROM products WHERE id=?")->execute([$id]);

    header('Location: ../index.php');
    exit();
}
