<?php
require_once '../configs/connect.php';
session_start();

/*
    สมมติว่า:
    - admin เพิ่ม student
    - auth_id มาจากตาราง auth ที่สร้าง user ไว้แล้ว
*/

/* ================= ADD / UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ================= ADD STUDENT ================= */
    if (isset($_POST['add_student'])) {
        try {
            $title = trim($_POST['title']);
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']);
            $student_id = trim($_POST['student_id']);
            $auth_id = intval($_POST['auth_id']); // 🔥 FK

            $stmt = $conn->prepare("
                INSERT INTO students (auth_id, title, firstname, lastname, student_id)
                VALUES (:auth_id, :title, :firstname, :lastname, :student_id)
            ");

            $stmt->execute([
                ':auth_id' => $auth_id,
                ':title' => $title,
                ':firstname' => $firstname,
                ':lastname' => $lastname,
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

    /* ================= UPDATE STUDENT ================= */
    if (isset($_POST['update_student'])) {
        try {
            $stmt = $conn->prepare("
                UPDATE students
                SET title = :title,
                    firstname = :firstname,
                    lastname = :lastname,
                    student_id = :student_id
                WHERE id = :id
            ");

            $stmt->execute([
                ':title' => $_POST['title'],
                ':firstname' => $_POST['firstname'],
                ':lastname' => $_POST['lastname'],
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

/* ================= DELETE STUDENT ================= */
if (isset($_GET['delete_student'])) {
    try {
        $id = $_GET['delete_student'];

        if (!is_numeric($id)) {
            header("Location: ../frontend/student/index.php?error=" . urlencode("ID ไม่ถูกต้อง"));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM students WHERE id = :id");
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
