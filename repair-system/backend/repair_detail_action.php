<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_repair_detail'])) {
        try {
            $repair_id = intval($_POST['repair_id']);
            $technical_id = intval($_POST['technical_id']);
            $status = $_POST['status'] ?? 'รอซ่อม';

            $allowed_status = ["รอซ่อม", "กำลังซ่อม", "เสร็จสิ้น"];

            $stmt = $conn->prepare(
                "INSERT INTO repair_detail (repair_id, technical_id, status) 
                VALUES (:repair_id, :technical_id, :status)"
            );

            $stmt->execute([
                ':repair_id' => $repair_id,
                ':technical_id' => $technical_id,
                ':status' => $status
            ]);

            header("Location: ../frontend/admin/repair_detail.php?status=" . urlencode("เพิ่มข้อมูลเสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            $error = "เพิ่มข้อมูลไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/admin/repair_detail.php?error=' . urlencode($error));
            exit();
        }
    }

    if (isset($_POST['update_repair_detail'])) {
        try {
            $id = intval($_POST['id']);
            $repair_id = intval($_POST['repair_id']);
            $technical_id = intval($_POST['technical_id']);
            $status = $_POST['status'];

            $allowed_status = ["รอซ่อม", "กำลังซ่อม", "เสร็จสิ้น"];

            $stmt = $conn->prepare(
                "UPDATE repair_detail SET repair_id =:repair_id, technical_id =:technical_id, status =:status WHERE id =:id"
            );

            $stmt->execute([
                ':repair_id' => $repair_id,
                ':technical_id' => $technical_id,
                ':status' => $status,
                ':id' => $id
            ]);

            header("Location: ../frontend/admin/repair_detail.php?status=" . urlencode("แก้ไขข้อมูลเสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            $error = "แก้ไขข้อมูลไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/admin/repair_detail.php?error=' . urlencode($error));
            exit();
        }
    }
}

if (isset($_GET['delete_repair_detail'])) {
    try {
        $id = intval($_GET['delete_repair_detail']);

        if ($id <= 0)
            throw new Exception("ID ไม่ถูกต้อง");

        $stmt = $conn->prepare("SELECT repair_id FROM repair_detail WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $repair_detail = $stmt->fetch(PDO::FETCH_ASSOC);
        $repair_id = $repair_detail['repair_id'] ?? 0;

        $stmt = $conn->prepare("DELETE FROM repair_detail WHERE id = :id");
        $stmt->execute([":id" => $id]);

        header("Location: ../frontend/admin/repair_detail.php?status=" . urlencode("ลบข้อมูลเสร็จสิ้น"));
        exit();
    } catch (PDOException $e) {
        $error = "ลบข้อมูลไม่สำเร็จ: " . $e->getMessage();
        header('Location: ../frontend/admin/repair_detail.php?error=' . urlencode($error));
        exit();
    }
}
