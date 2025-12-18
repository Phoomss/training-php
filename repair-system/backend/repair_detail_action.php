<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle status update from technical staff
    if (isset($_POST['update_status'])) {
        try {
            $repair_id = intval($_POST['repair_id']);
            $technical_id = intval($_POST['technical_id']);
            $status = $_POST['status'];

            // Validate status
            $allowed_status = ["รอซ่อม", "กำลังซ่อม", "เสร็จสิ้น"];
            if (!in_array($status, $allowed_status)) {
                throw new Exception("สถานะไม่ถูกต้อง");
            }

            // Note: The repair table doesn't have a status column
            // Status is tracked in the repair_detail table only

            // Insert repair detail record
            $stmt = $conn->prepare("INSERT INTO repair_detail (repair_id, technical_id, status) VALUES (?, ?, ?)");
            $stmt->execute([$repair_id, $technical_id, $status]);

            // Redirect back to the repair view
            if (isset($_POST['from_technical']) && $_POST['from_technical'] == 'true') {
                header("Location: ../frontend/technical/view_repair.php?id=" . $repair_id . "&status=" . urlencode("อัปเดตสถานะเรียบร้อยแล้ว"));
            } else {
                header("Location: ../frontend/technical/view_repair.php?id=" . $repair_id . "&status=" . urlencode("อัปเดตสถานะเรียบร้อยแล้ว"));
            }
            exit();
        } catch (PDOException $e) {
            $error = "อัปเดตสถานะไม่สำเร็จ: " . $e->getMessage();
            if (isset($_POST['from_technical']) && $_POST['from_technical'] == 'true') {
                header('Location: ../frontend/technical/view_repair.php?id=' . $repair_id . '&error=' . urlencode($error));
            } else {
                header('Location: ../frontend/technical/view_repair.php?id=' . $repair_id . '&error=' . urlencode($error));
            }
            exit();
        } catch (Exception $e) {
            if (isset($_POST['from_technical']) && $_POST['from_technical'] == 'true') {
                header('Location: ../frontend/technical/view_repair.php?id=' . $repair_id . '&error=' . urlencode($e->getMessage()));
            } else {
                header('Location: ../frontend/technical/view_repair.php?id=' . $repair_id . '&error=' . urlencode($e->getMessage()));
            }
            exit();
        }
    }

    // Handle admin functions
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
