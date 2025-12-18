<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_equiment'])) {
        try {
            $name = trim($_POST['name']);

            $stmt = $conn->prepare(
                "INSERT INTO equiment (name) VALUES (:name)"
            );

            $stmt->execute([
                ':name' => $name
            ]);

            header("Location: ../frontend/admin/equipment.php?status=" . urldecode("เพิ่มข้อมูลเสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            $error = "เพิ่มข้อมูลไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/admin/equipment.php?error=' . urlencode($error));
            exit();
        }
    }

    if (isset($_POST['update_equiment'])) {
        try {
            $id = intval($_POST['id']);
            $name = trim($_POST['name']);

            $stmt = $conn->prepare(
                "UPDATE equiment SET name = :name WHERE id = :id"
            );

            $stmt->execute([
                ':name' => $name
            ]);

            header("Location: ../frontend/admin/equipment.php?status=" . urldecode("แก้ไขข้อมูลเสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            $error = "แก้ไขข้อมูลไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/admin/equipment.php?error=' . urlencode($error));
            exit();
        }
    }
}

if (isset($_GET['delete_equiment'])) {
    try {
        $id = intval($_GET['delete_equiment']);

        if ($id <= 0) {
            throw new Exception("ID ไม่ถูกต้อง");
        }

        $stmt = $conn->prepare(
            "DELETE FROM equiment WHERE id = :id"
        );

        header("Location: ../frontend/admin/equipment.php?status=" . urldecode("ลบข้อมูลเสร็จสิ้น"));
        exit();
    } catch (PDOException $e) {
        $error = "ไม่สามารถลบข้อมูลได้: " . $e->getMessage();
        header('Location: ../frontend/admin/equipment.php?error=' . urlencode($error));
        exit();
    }
}
