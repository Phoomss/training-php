<?php
require_once '../configs/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_user'])) {
        try {
            $title = trim($_POST['title']);
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $student_id = trim($_POST['student_id']);
            $auth_id = intval($_POST['auth_id']); // FK

            $stmt = $conn->prepare("
                INSERT INTO users (auth_id, title, firstname, lastname, email, phone, student_id)
                VALUES (:auth_id, :title, :firstname, :lastname, :email, :phone, :student_id)
            ");

            $stmt->execute([
                ':auth_id' => $auth_id,
                ':title' => $title,
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':email' => $email,
                ':phone' => $phone,
                ':student_id' => $student_id
            ]);

            header("Location: ../frontend/student/index.php?status=" . urlencode("เพิ่มข้อมูลนักเรียนเรียบร้อยแล้ว"));
            exit();

        } catch (PDOException $e) {
            $errorMessage = "เพิ่มข้อมูลไม่สำเร็จ";

            if ($e->getCode() == 23000) {
                $errorMessage = "รหัสนักเรียนหรือ auth ซ้ำ / auth ไม่ถูกต้อง";
            }

            header("Location: ../frontend/student/form_student.php?error=" . urlencode($errorMessage));
            exit();
        }
    }

    if (isset($_POST['update_user'])) {
        try {
            $stmt = $conn->prepare("
                UPDATE users
                SET title = :title,
                    firstname = :firstname,
                    lastname = :lastname,
                    email = :email,
                    phone = :phone,
                    student_id = :student_id
                WHERE id = :id
            ");

            $stmt->execute([
                ':title' => $_POST['title'],
                ':firstname' => $_POST['firstname'],
                ':lastname' => $_POST['lastname'],
                ':email' => $_POST['email'],
                ':phone' => $_POST['phone'],
                ':student_id' => $_POST['student_id'],
                ':id' => $_POST['id']
            ]);

            header("Location: ../frontend/student/index.php?status=" . urlencode("อัปเดตข้อมูลนักเรียนเรียบร้อยแล้ว"));
            exit();

        } catch (PDOException $e) {
            $errorMessage = "อัปเดตข้อมูลไม่สำเร็จ";

            if ($e->getCode() == 23000) {
                $errorMessage = "ข้อมูลนี้เชื่อมกับตารางอื่นอยู่";
            }

            header("Location: ../frontend/student/form_student.php?error=" . urlencode($errorMessage));
            exit();
        }
    }
}

if (isset($_GET['delete_user'])) {
    try {
        $id = intval($_GET['delete_user']);

        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: ../frontend/student/index.php?status=" . urlencode("ลบข้อมูลนักเรียนเรียบร้อยแล้ว"));
        exit();

    } catch (PDOException $e) {
        $errorMessage = "ไม่สามารถลบข้อมูลได้";

        if ($e->getCode() == 23000) {
            $errorMessage = "ไม่สามารถลบได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่น";
        }

        header("Location: ../frontend/student/index.php?error=" . urlencode($errorMessage));
        exit();
    }
}
