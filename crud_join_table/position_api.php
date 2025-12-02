<?php
require_once "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO positions (position_name) VALUES (:position_name)");
    $stmt->bindParam(':position_name', $_POST['position_name']);
    $stmt->execute();
    
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $stmt = $conn->prepare("UPDATE positions SET position_name=:position_name WHERE id=:id");
    $stmt->bindParam(':position_name', $_POST['position_name']);
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->execute();

    header('Location: index.php');
    exit();
}

if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM positions WHERE id=:id");
    $stmt->bindParam(':id', $_GET['delete']);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
