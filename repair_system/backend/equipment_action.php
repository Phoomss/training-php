<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_equipment'])) {
        try {
            $name = trim($_POST['name']);
            $category_id = intval($_POST['category_id']);

            if (empty($name) || $category_id <= 0) {
                throw new Exception("กรุณากรอกชื่ออุปกรณ์และเลือกหมวดหมู่ให้ถูกต้อง");
            }

            $stmt = $conn->prepare("
                INSERT INTO equipment (name, category_id, created_at)
                VALUES (:name, :category_id, NOW())
            ");

            $stmt->execute([
                ':name' => $name,
                ':category_id' => $category_id
            ]);

            header('Location: ../frontend/admin/equipment.php?status=' . urlencode("เพิ่มอุปกรณ์เรียบร้อยแล้ว"));
            exit();

        } catch (PDOException $e) {
            $error = "เพิ่มอุปกรณ์ไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/admin/equipment.php?error=' . urlencode($error));
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
            header('Location: ../frontend/admin/equipment.php?error=' . urlencode($error));
            exit();
        }
    }

    if (isset($_POST['update_equipment'])) {
        try {
            $id = intval($_POST['id']);
            $name = trim($_POST['name']);
            $category_id = intval($_POST['category_id']);

            if ($id <= 0 || empty($name) || $category_id <= 0) {
                throw new Exception("ข้อมูลไม่ถูกต้อง");
            }

            $stmt = $conn->prepare("
                UPDATE equipment
                SET name = :name, category_id = :category_id
                WHERE id = :id
            ");

            $stmt->execute([
                ':name' => $name,
                ':category_id' => $category_id,
                ':id' => $id
            ]);

            header('Location: ../frontend/admin/equipment.php?status=' . urlencode("แก้ไขอุปกรณ์เรียบร้อยแล้ว"));
            exit();

        } catch (PDOException $e) {
            $error = "แก้ไขอุปกรณ์ไม่สำเร็จ: " . $e->getMessage();
            header('Location: ../frontend/admin/equipment.php?error=' . urlencode($error));
            exit();
        } catch (Exception $e) {
            $error = $e->getMessage();
            header('Location: ../frontend/admin/equipment.php?error=' . urlencode($error));
            exit();
        }
    }
}

if (isset($_GET['delete_equipment'])) {
    try {
        $id = intval($_GET['delete_equipment']);

        if ($id <= 0) {
            throw new Exception("ID ไม่ถูกต้อง");
        }

        $stmt = $conn->prepare("DELETE FROM equipment WHERE id = :id");
        $stmt->execute([':id' => $id]);

        header('Location: ../frontend/admin/equipment.php?status=' . urlencode("ลบอุปกรณ์เรียบร้อยแล้ว"));
        exit();

    } catch (PDOException $e) {
        $error = "ไม่สามารถลบอุปกรณ์ได้: อาจมีข้อมูลเชื่อมโยงอยู่ (" . $e->getMessage() . ")";
        header('Location: ../frontend/admin/equipment.php?error=' . urlencode($error));
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
        header('Location: ../frontend/admin/equipment.php?error=' . urlencode($error));
        exit();
    }
}
?>