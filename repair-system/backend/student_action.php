<?php
require_once '../configs/connect.php';
session_start();

// Admin only access
if (!isset($_SESSION['auth_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php?error=' . urlencode('คุณไม่มีสิทธิ์ในการเข้าถึงหน้านี้'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_student'])) {
        try {
            $title = trim($_POST['title']);
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']);
            $student_id = trim($_POST['student_id']);
            $auth_id = intval($_POST['auth_id']);

            $stmt = $conn->prepare("
            INSERT INTO student (title, firstname, lastname, student_id, auth_id) VALUES (:title, :firstname, :lastname, :student_id, :auth_id)");

            $stmt->execute([
                ':title' => $title,
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':student_id' => $student_id,
                ':auth_id' => $auth_id
            ]);

            header("Location: ../frontend/admin/index.php?status=" . urlencode("เพิ่มข้อมูลเสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "เพิ่มข้อมูลไม่สำเร็จ";

            if ($e->getCode() == 23000) {
                $errorMessage = "รหัสนักเรียนหรือ auth ซ้ำ / auth ไม่ถูกต้อง";
            }

            header("Location: ../frontend/admin/students.php?error=" . urlencode($errorMessage));
            exit();
        }
    }

    if (isset($_POST['update_student'])) {
        try {
            $stmt = $conn->prepare(
                "UPDATE student SET
                title = :title,
                firstname = :firstname,
                lastname = :lastname,
                student_id = :student_id
                WHERE id = :id"
            );

            $stmt->execute([
                ':title' => $_POST['title'],
                ':firstname' => $_POST['firstname'],
                ':lastname' => $_POST['lastname'],
                ':student_id' => $_POST['student_id'],
                ':id' => $_POST['id']
            ]);

            header("Location: ../frontend/student/index.php?status=" . urlencode("อัปเดตข้อมูลเสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "อัปเดตข้อมูลไม่สำเร็จ";

            if ($e->getCode() == 23000) {
                $errorMessage = "ข้อมูลนี้เชื่อมกับตารางอื่นอยู่";
            }

            header("Location: ../frontend/admin/students.php?error=" . urlencode($errorMessage));
            exit();
        }
    }
}

if (isset($_GET['delete_student'])) {
    try {
        $id = intval($_GET['delete_student']);

        $stmt = $conn->prepare(
            "DELETE FROM student WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        header("Location: ../frontend/admin/index.php?status=" . urlencode("ลบข้อมูลเสร็จสิ้น"));
    } catch (PDOException $e) {
        $errorMessage = "ไม่สามารถลบข้อมูลได้";

        if ($e->getCode() == 23000) {
            $errorMessage = "ไม่สามารถลบได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่น";
        }

        header("Location: ../frontend/admin/index.php?error=" . urlencode($errorMessage));
        exit();
    }
}
