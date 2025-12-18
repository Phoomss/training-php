<?php
require_once('../configs/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add new equipment
    if (isset($_POST['add_equipment'])) {
        $name = trim($_POST['name']);

        try {
            $stmt = $conn->prepare("INSERT INTO equipment (name) VALUES (:name)");
            $stmt->execute([':name' => $name]);

            header("Location: ../frontend/admin/equipment.php?status=" . urlencode("เพิ่มอุปกรณ์เสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                header("Location: ../frontend/admin/form_equipment.php?error=" . urlencode("ชื่ออุปกรณ์นี้มีอยู่แล้ว"));
            } else {
                header("Location: ../frontend/admin/form_equipment.php?error=" . urlencode("เกิดข้อผิดพลาดในการเพิ่มอุปกรณ์"));
            }
            exit();
        }
    }

    // Update existing equipment
    if (isset($_POST['update_equipment'])) {
        $id = intval($_POST['id']);
        $name = trim($_POST['name']);

        try {
            $stmt = $conn->prepare("UPDATE equipment SET name = :name WHERE id = :id");
            $stmt->execute([':name' => $name, ':id' => $id]);

            header("Location: ../frontend/admin/equipment.php?status=" . urlencode("แก้ไขอุปกรณ์เสร็จสิ้น"));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                header("Location: ../frontend/admin/form_equipment.php?id=" . $id . "&error=" . urlencode("ชื่ออุปกรณ์นี้มีอยู่แล้ว"));
            } else {
                header("Location: ../frontend/admin/form_equipment.php?id=" . $id . "&error=" . urlencode("เกิดข้อผิดพลาดในการแก้ไขอุปกรณ์"));
            }
            exit();
        }
    }
}

// Delete equipment
if (isset($_GET['delete_equipment'])) {
    $id = intval($_GET['delete_equipment']);

    try {
        $stmt = $conn->prepare("DELETE FROM equipment WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: ../frontend/admin/equipment.php?status=" . urlencode("ลบอุปกรณ์เสร็จสิ้น"));
        exit();
    } catch (PDOException $e) {
        header("Location: ../frontend/admin/equipment.php?error=" . urlencode("ไม่สามารถลบอุปกรณ์ได้ (อาจมีการใช้งานในคำร้องแจ้งซ่อม)"));
        exit();
    }
}