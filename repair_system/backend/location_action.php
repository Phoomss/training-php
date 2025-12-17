<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (isset($_POST['add_location'])) {
        try {
            $stmt = $conn->prepare("INSERT INTO location (name) VALUES (:name)");
            $stmt->execute([':name' => trim($_POST['name'])]);

            header('Location: ../frontend/admin/location.php');
            exit();

        } catch (PDOException $e) {
            $error = "เพิ่มตำแหน่งไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/admin/location.php?error=' . urlencode($error));
            exit();
        }
    }

    if (isset($_POST['update_location'])) {
        try {
            $stmt = $conn->prepare("UPDATE location SET name = :name WHERE id = :id");
            $stmt->execute([
                ':name' => trim($_POST['name']),
                ':id' => intval($_POST['id'])
            ]);

            header('Location: ../frontend/admin/location.php');
            exit();

        } catch (PDOException $e) {
            $error = "แก้ไขตำแหน่งไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/admin/location.php?error=' . urlencode($error));
            exit();
        }
    }
}

if (isset($_GET['delete_location'])) {
    try {
        $id = intval($_GET['delete_location']);
        $stmt = $conn->prepare("DELETE FROM location WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header('Location: ../frontend/admin/location.php');
        exit();

    } catch (PDOException $e) {
        $error = "ไม่สามารถลบตำแหน่งนี้ได้: ข้อมูลอาจถูกใช้งานอยู่ (" . $e->getMessage() . ")";
        header('Location: ../frontend/admin/location.php?error=' . urlencode($error));
        exit();
    }
}
?>