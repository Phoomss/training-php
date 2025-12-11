<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_student'])) {
        $title = trim($_POST['title']);
        $first_name = trim($_POST['firstname']);
        $last_name = trim($_POST['lastname']);
        $student_id = trim($_POST['student_id']);

        try {
            $stmt = $conn->prepare(
                "INSERT INTO students(title, firstname, lastname, student_id)
            VALUES (:title, :firstname, :lastname, :student_id)"
            );

            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':firstname', $first_name);
            $stmt->bindParam(':lastname', $last_name);
            $stmt->bindParam(':student_id', $student_id);

            $stmt->execute();
            header("Location: ../frontend/student/student.php?status=" . urlencode('เพิ่มข้อมูลนักเรียนเรียบร้อยแล้ว'));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "ไม่สามารถลบข้อมูลได้";

            // ตรวจสอบ Foreign Key constraint
            if ($e->getCode() == 23000) {
                $errorMessage = "ไม่สามารถลบข้อมูลได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่นอยู่!";
            }

            header('Location: ../frontend/student/form_student.php?error=' . urlencode($errorMessage));
            exit();
        }
    }
    if (isset($_POST['update_student'])) {
        try {
            $stmt = $conn->prepare("
            UPDATE students
            SET title = :title,
                firstname = :firstname,
                lastname = :lastname,
                student_id = :student_id
            WHERE id = :id");
            $stmt->bindParam(':title', $_POST['title']);
            $stmt->bindParam(':firstname', $_POST['firstname']);
            $stmt->bindParam(':lastname', $_POST['lastname']);
            $stmt->bindParam(':student_id', $_POST['student_id']);
            $stmt->bindParam(':id', $_POST['id']);

            $stmt->execute();
            header('Location: ../frontend/student/student.php?status=' . urlencode('อัปเดตข้อมูลนักเรียนเรียบร้อยแล้ว'));
            exit();
        } catch (PDOException $e) {
            $errorMessage = "ไม่สามารถลบข้อมูลได้";

            // ตรวจสอบ Foreign Key constraint
            if ($e->getCode() == 23000) {
                $errorMessage = "ไม่สามารถลบข้อมูลได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่นอยู่!";
            }

            header('Location: ../frontend/student/form_student.php?error=' . urlencode($errorMessage));
            exit();
        }
    }
}

if (isset($_GET['delete_student'])) {
    try {
        $id = $_GET['delete_student'];
        if (is_numeric($id)) {
            header('Location: ../frontend/student.php?error=' . urlencode('Invalid ID'));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM students WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        header('Location: ../frontend/student/student.php?status=' . urldecode("delete student data success"));
        exit();
    } catch (PDOException $e) {
        $errorMessage = "ไม่สามารถลบข้อมูลได้";

        // ตรวจสอบ Foreign Key constraint
        if ($e->getCode() == 23000) {
            $errorMessage = "ไม่สามารถลบข้อมูลได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่นอยู่!";
        }

        header('Location: ../frontend/student/form_student.php?error=' . urlencode($errorMessage));
        exit();
    }
}
