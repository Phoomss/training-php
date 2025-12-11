<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_activity_detail'])) {
        try {
            $student_id = $_POST['student_id'];
            $activity_id = $_POST['activity_id'];

            $stmt = $conn->prepare("INSERT INTO activity_detils (student_id, activity_id) VALUES (:student_id, :activity_id)");

            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':activity_id', $activity_id);

            $stmt->execute();

            header("Location: ../frontend/student/activity_detail.php?status=" . urldecode("add activity success"));
            exit();
        } catch (PDOException $e) {

            // error default
            $errorMessage = "มีข้อผิดพลาดในการบันทึกข้อมูล";

            header("Location: ../frontend/student/activity_detail.php?error=" . urlencode($errorMessage));
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

            $stmt->execute();

            header("Location: ../frontend/student/activity_detail.php?status=" . urldecode("update activity success"));
            exit();
        } catch (PDOException $e) {

            // error default
            $errorMessage = "มีข้อผิดพลาดในการอัพเดทข้อมูล";

            header("Location: ../frontend/student/activity_detail.php?error=" . urlencode($errorMessage));
            exit();
        }
    }
}

if (isset($_GET['delete_activity_detail'])) {
    try {
        $id = $_GET['delete_activity_detail'];
        if (is_numeric($id)) {
            header('Location: ../frontend/student/activtity_detail.php?error=' . urlencode('Invalid ID'));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM activity_details WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        header('Location: ../frontend/student/student.php?status=' . urldecode("delete activity details data success"));
        exit();
    } catch (PDOException $e) {
        $errorMessage = "ไม่สามารถลบข้อมูลได้";

        // ตรวจสอบ Foreign Key constraint
        if ($e->getCode() == 23000) {
            $errorMessage = "ไม่สามารถลบข้อมูลได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่นอยู่!";
        }

        header('Location: ../frontend/student/activity_detail.php?error=' . urlencode($errorMessage));
        exit();
    }
}
