<?php
require_once '../configs/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_technical'])) {
        try {
            $title = trim($_POST['title']);
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']);
            $phone = trim($_POST['phone']);
            $auth_id = intval($_POST['auth_id']);

            $stmt = $conn->prepare("
            INSERT INTO technical (title, firstname, lastname, phone, auth_id) VALUES (:title, :firstname, :lastname, :phone, :auth_id)");

            $stmt->execute([
                ':title' => $title,
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':phone' => $phone,
                ':auth_id' => $auth_id
            ]);

            header("Location: ../frontend/technical/index.php?status=" . urldecode("เพิ่มข้อมูลเสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "เพิ่มข้อมูลไม่สำเร็จ";

            if ($e->getCode() == 23000) {
                $errorMessage = "auth ซ้ำ / auth ไม่ถูกต้อง";
            }

            header("Location: ../frontend/technical/form_technical.php?error=" . urlencode($errorMessage));
            exit();
        }
    }

    if (isset($_POST['update_technical'])) {
        try {
            $stmt = $conn->prepare(
                "UPDATE technical SET
                title = :title,
                firstname = :firstname,
                lastname = :lastname,
                phone = :phone
                WHERE id = :id"
            );

            $stmt->execute([
                ':title' => $_POST['title'],
                ':firstname' => $_POST['firstname'],
                ':lastname' => $_POST['lastname'],
                ':phone' => $_POST['phone'],
                ':id' => $_POST['id']
            ]);

            header("Location: ../frontend/technical/index.php?status=" . urlencode("อัปเดตข้อมูลเสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "อัปเดตข้อมูลไม่สำเร็จ";

            if ($e->getCode() == 23000) {
                $errorMessage = "ข้อมูลนี้เชื่อมกับตารางอื่นอยู่";
            }

            header("Location: ../frontend/technical/form_technical.php?error=" . urlencode($errorMessage));
            exit();
        }
    }
}

if (isset($_GET['delete_technical'])) {
    try {
        $id = intval($_GET['delete_technical']);

        $stmt = $conn->prepare(
            "DELETE FROM technical WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        header("Location: ../frontend/technical/index.php?status=" . urldecode("ลบข้อมูลเสร็จสิ้น"));
    } catch (PDOException $e) {
        $errorMessage = "ไม่สามารถลบข้อมูลได้";

        if ($e->getCode() == 23000) {
            $errorMessage = "ไม่สามารถลบได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่น";
        }

        header("Location: ../frontend/technical/index.php?error=" . urlencode($errorMessage));
        exit();
    }
}
