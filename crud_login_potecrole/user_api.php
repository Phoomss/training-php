<?php
require_once('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add'])) {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, phone, position_id)
                    VALUES (:name, :email, :phone, :position_id)"
            );

            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':phone', $_POST['phone']);
            $stmt->bindParam(':position_id', $_POST['position_id']);

            $stmt->execute();

            header("Location: user.php?status=success");
            exit();
        } catch (PDOException $e) {
            $errorMessage = "มีข้อผิดพลาดในการบันทึกข้อมูล";

            if ($e->getCode() == 23000) {
                $msg = $e->getMessage();

                if (strpos($msg, 'email') !== false) {
                    $errorMessage = "อีเมลนี้ถูกใช้แล้ว!";
                }

                if (strpos($msg, 'phone') !== false) {
                    $errorMessage = "เบอร์โทรนี้ถูกใช้แล้ว!";
                }
            }

            header("Location: user.php?error=" . urldecode($errorMessage));
            exit();
        }
    }

    if (isset($_POST['update'])) {
        try {
            $stmt = $conn->prepare("
                UPDATE users 
                SET name = :name, email = :email, phone = :phone, position_id = :position_id
                WHERE id = :id
            ");
            $stmt->bindParam(':name', $_POST['name']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':phone', $_POST['phone']);
            $stmt->bindParam(':position_id', $_POST['position_id']);
            $stmt->bindParam(':id', $_POST['id']);
            $stmt->execute();

            header('Location: user.php?status=updated');
            exit();
        } catch (PDOException $e) {
            $errorMessage = "มีข้อผิดพลาดในการอัปเดตข้อมูล";
            if ($e->getCode() == 23000) {
                $msg = $e->getMessage();
                if (strpos($msg, 'email') !== false) $errorMessage = "อีเมลนี้ถูกใช้งานแล้ว!";
                if (strpos($msg, 'phone') !== false) $errorMessage = "เบอร์โทรนี้ถูกใช้งานแล้ว!";
            }
            header('Location: user.php?update_error=' . urlencode($errorMessage));
            exit();
        }
    }
}

if (isset($_GET['delete'])) {
    try {
        $id = $_GET['delete'];
        if (!is_numeric($id)) {
            header('Location: user.php?error=' . urlencode('Invalid ID'));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: user.php?status=deleted');
        exit();
    } catch (PDOException $e) {
        $errorMessage = "ไม่สามารถลบข้อมูลได้";

        // ตรวจจับ Foreign Key constraint
        if ($e->getCode() == 23000) {
            $errorMessage = "ไม่สามารถลบข้อมูลได้ เพราะข้อมูลนี้เชื่อมกับตารางอื่นอยู่!";
        }

        header('Location: user.php?error=' . urlencode($errorMessage));
        exit();
    }
}
