<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_activity_detail'])) {
        try {
            $student_id = $_POST['student_id'];
            $activity_id = $_POST['activity_id'];

            $stmt = $conn->prepare(
                "INSERT INTO activity_details (student_id, activity_id)
                 VALUES (:student_id, :activity_id)"
            );

            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':activity_id', $activity_id);
            $stmt->execute();

            header("Location: ../frontend/student/activity_detail.php?status=" . urlencode("add activity success"));
            exit();

        } catch (PDOException $e) {
            header("Location: ../frontend/student/activity_detail.php?error=" . urlencode("มีข้อผิดพลาดในการบันทึกข้อมูล"));
            exit();
        }
    }

    if (isset($_POST['update_activity_detail'])) {
        try {
            $stmt = $conn->prepare(
                "UPDATE activity_details
                 SET student_id = :student_id,
                     activity_id = :activity_id
                 WHERE id = :id"
            );

            $stmt->bindParam(':student_id', $_POST['student_id']);
            $stmt->bindParam(':activity_id', $_POST['activity_id']);
            $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);

            $stmt->execute();

            header("Location: ../frontend/student/activity_detail.php?status=" . urlencode("update activity success"));
            exit();

        } catch (PDOException $e) {
            header("Location: ../frontend/student/activity_detail.php?error=" . urlencode("มีข้อผิดพลาดในการอัพเดทข้อมูล"));
            exit();
        }
    }
}

// Handle student registration via GET request
if (isset($_GET['student_id']) && isset($_GET['activity_id']) && isset($_GET['register'])) {
    try {
        $student_id = $_GET['student_id'];
        $activity_id = $_GET['activity_id'];

        $stmt = $conn->prepare(
            "INSERT INTO activity_details (student_id, activity_id)
             VALUES (:student_id, :activity_id)"
        );

        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':activity_id', $activity_id);
        $stmt->execute();

        header("Location: ../frontend/student/activity_detail.php?status=" . urlencode("ลงทะเบียนกิจกรรมสำเร็จ"));
        exit();

    } catch (PDOException $e) {
        header("Location: ../frontend/student/activity_detail.php?error=" . urlencode("มีข้อผิดพลาดในการลงทะเบียนกิจกรรม"));
        exit();
    }
}

if (isset($_GET['delete_activity_detail'])) {
    try {
        $id = $_GET['delete_activity_detail'];

        if (!is_numeric($id)) {
            header('Location: ../frontend/student/activity_detail.php?error=' . urlencode('Invalid ID'));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM activity_details WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: ../frontend/student/activity_detail.php?status=' . urlencode("ลบการลงทะเบียนกิจกรรมสำเร็จ"));
        exit();

    } catch (PDOException $e) {
        $errorMessage = ($e->getCode() == 23000)
            ? "ไม่สามารถลบข้อมูลได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่นอยู่!"
            : "ไม่สามารถลบข้อมูลได้";

        header('Location: ../frontend/student/activity_detail.php?error=' . urlencode($errorMessage));
        exit();
    }
}
