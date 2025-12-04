<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['add'])) {
        $stmt = $conn->prepare("INSERT INTO mamjors (name) VALUES (:name)");
        $stmt->bindParam(':name', $_POST['name']);

        $stmt->execute();

        header('Location: ../frontend/major.php');
        exit();
    }

    if (isset($_POST['update'])) {
        $stmt = $conn->prepare("UPDATE mamjors SET name = :name WHERE id = :id");
        $stmt->bindParam(':name', $_POST['name']);
        $stmt->bindParam(':id', $_POST['id']);

        $stmt->execute();

        header('Location: ../frontend/major.php');
        exit();
    }
}

if (isset($_GET['delete'])) {
    $stmt = $conn->prepare("DELETE FROM majors WHERE id = :id");
    $stmt->bindParam(':id', $_GET['delete']);

    $stmt->execute();

    header('Location: ../frontend/major.php');
    exit();
}
