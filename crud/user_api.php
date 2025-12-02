<?php
require_once "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO users (name,email,phone) VALUES (:name,:email,:phone)");
    $stmt->bindParam(':name', $_POST['name']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':phone', $_POST['phone']);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE users SET name=:name,email=:email,phone=:phone WHERE id=:id");
    $stmt->bindParam(':name', $_POST['name']);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':phone', $_POST['phone']);
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->execute();
    header("Location: index.php");
    exit();
}

if(isset($_GET['delete'])){
    $stmt = $conn->prepare("DELETE FROM users WHERE id=:id");
    $stmt->bindParam(':id', $_GET['delete']);
    $stmt->execute();
    header("Location: index.php");
    exit();
}
?>
