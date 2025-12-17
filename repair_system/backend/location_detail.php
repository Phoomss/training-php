<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_location_detail'])) {
        try {
            $location_id = intval($_POST['location_id']);
            $room = trim($_POST['room']);
            $floor = intval($_POST['floor']);

            if ($location_id <= 0 || empty($room) || $floor < 0) {
                throw new Exception("กรุณากรอกข้อมูลให้ครบถ้วน");
            }

            $stmt = $conn->prepare("
                INSERT INTO location_detail (location_id, room, floor)
                VALUES (:location_id, :room, :floor)
            ");

            $stmt->execute([
                ':location_id' => $location_id,
                ':room' => $room,
                ':floor' => $floor
            ]);

            header('Location: ../frontend/location_detail.php?status=' . urlencode("เพิ่มตำแหน่งเรียบร้อยแล้ว"));
            exit();

        } catch (PDOException $e) {
            $error = "เพิ่มตำแหน่งไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/location_detail.php?error=' . urlencode($error));
            exit();

        } catch (Exception $e) {
            $error = $e->getMessage();
            header('Location: ../frontend/location_detail.php?error=' . urlencode($error));
            exit();
        }
    }

    if (isset($_POST['update_location_detail'])) {
        try {
            $id = intval($_POST['id']);
            $location_id = intval($_POST['location_id']);
            $room = trim($_POST['room']);
            $floor = intval($_POST['floor']);

            if ($id <= 0 || $location_id <= 0 || empty($room) || $floor < 0) {
                throw new Exception("ข้อมูลไม่ถูกต้อง");
            }

            $stmt = $conn->prepare("
                UPDATE location_detail
                SET location_id = :location_id, room = :room, floor = :floor
                WHERE id = :id
            ");

            $stmt->execute([
                ':location_id' => $location_id,
                ':room' => $room,
                ':floor' => $floor,
                ':id' => $id
            ]);

            header('Location: ../frontend/location_detail.php?status=' . urlencode("แก้ไขตำแหน่งเรียบร้อยแล้ว"));
            exit();

        } catch (PDOException $e) {
            $error = "แก้ไขตำแหน่งไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/location_detail.php?error=' . urlencode($error));
            exit();

        } catch (Exception $e) {
            $error = $e->getMessage();
            header('Location: ../frontend/location_detail.php?error=' . urlencode($error));
            exit();
        }
    }
}

if (isset($_GET['delete_location_detail'])) {
    try {
        $id = intval($_GET['delete_location_detail']);
        if ($id <= 0) {
            throw new Exception("ID ไม่ถูกต้อง");
        }

        $stmt = $conn->prepare("DELETE FROM location_detail WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header('Location: ../frontend/location_detail.php?status=' . urlencode("ลบตำแหน่งเรียบร้อยแล้ว"));
        exit();

    } catch (PDOException $e) {
        $error = "ไม่สามารถลบตำแหน่งได้: ข้อมูลอาจถูกใช้งานอยู่ (" . $e->getMessage() . ")";
        header('Location: ../frontend/location_detail.php?error=' . urlencode($error));
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
        header('Location: ../frontend/location_detail.php?error=' . urlencode($error));
        exit();
    }
}
?>