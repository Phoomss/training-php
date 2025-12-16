<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_activity'])) {
        $activity_name = trim($_POST['activity_name']);
        $activity_date = $_POST['date'];
        $activity_time = $_POST['time'];

        try {
            $stmt = $conn->prepare(
                "INSERT INTO activites(activity_name, time, date)
                VALUES (:activity_name, :time, :date)
                "
            );

            $stmt->bindParam(':activity_name', $activity_name);
            $stmt->bindParam(':time', $activity_time);
            $stmt->bindParam(':date', $activity_date);

            $stmt->execute();
            header('Location: ../frontend/admin/activites.php=' . urlencode("success"));
            exit();
        } catch (PDOException $e) {

            // error default
            $errorMessage = "มีข้อผิดพลาดในการบันทึกข้อมูล";

            // 3) ตรวจสอบ Duplicate key (23000)
            if ($e->getCode() == 23000) {
                $msg = $e->getMessage();

                if (strpos($msg, 'activity_name') !== false) {
                    $errorMessage = "ชื่อกิจกรรมนี้ถูกใช้แล้ว!";
                }
            }

            header("Location: ../frontend/admin/activites.php?error=" . urlencode($errorMessage));
            exit();
        }
    }
    if (isset($_POST['update_activity'])) {
        try {
            $stmt = $conn->prepare(
                "UPDATE activites
                SET activity_name = :activity_name,
                time = :time,
                date = :date
            WHERE id = :id"
            );

            $stmt->bindParam(':activity_name', $_POST['activity_name']);
            $stmt->bindParam(':time', $_POST['time']);
            $stmt->bindParam(':date', $_POST['date']);
            $stmt->bindParam(':id', $_POST['id']);

            $stmt->execute();

            header("Location: ../frontend/admin/activites.php?status=success");
            exit();
        } catch (PDOException $e) {

            $errorMessage = "มีข้อผิดพลาดในการบันทึกข้อมูล";

            if ($e->getCode() == 23000) {
                $msg = $e->getMessage();

                if (strpos($msg, 'activity_name') !== false) {
                    $errorMessage = "ชื่อกิจกรรมนี้ถูกใช้แล้ว!";
                }
            }

            header("Location: ../frontend/admin/activites.php?error=" . urlencode($errorMessage));
            exit();
        }
    }
}

if (isset($_GET['delete_activity'])) {
    try {
        $id = $_GET['delete_activity'];

        if (!is_numeric($id)) {
            header("Location: ../frontend/admin/activites.php?error=" . urlencode("รหัสกิจกรรมไม่ถูกต้อง"));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM activites WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        header("Location: ../frontend/admin/activites.php?status=" . urlencode("success"));
        exit();
    } catch (PDOException $e) {
        $errorMessage = "ไม่สามารถลบข้อมูลได้";

        // ตรวจสอบ Foreign Key constraint
        if ($e->getCode() == 23000) {
            $errorMessage = "ไม่สามารถลบข้อมูลได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่นอยู่!";
        }

        header('Location: ../frontend/admin/activites.php?error=' . urlencode($errorMessage));
        exit();
    }
}