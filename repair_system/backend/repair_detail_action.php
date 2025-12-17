<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_repair_detail'])) {
        try {
            $repair_id = intval($_POST['repair_id']);
            $technical_id = intval($_POST['technical_id']);
            $staff_id = intval($_POST['staff_id']);
            $status = $_POST['status'];

            $allowed_status = ["รอซ่อม", "กำลังซ่อม", "เสร็จสิ้น"];
            if (!in_array($status, $allowed_status))
                throw new Exception("สถานะไม่ถูกต้อง");

            if ($repair_id <= 0 || $technical_id <= 0 || $staff_id <= 0)
                throw new Exception("ข้อมูลไม่ถูกต้อง");

            $stmt = $conn->prepare("
                INSERT INTO repair_detail (repair_id, technical_id, staff_id, status, created_at, updated_at)
                VALUES (:repair_id, :technical_id, :staff_id, :status, NOW(), NOW())
            ");
            $stmt->execute([
                ':repair_id' => $repair_id,
                ':technical_id' => $technical_id,
                ':staff_id' => $staff_id,
                ':status' => $status
            ]);

            // Redirect based on user role
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                $repair_id = intval($_POST['repair_id']);
                header('Location: ../frontend/admin/repair_detail.php?repair_id=' . $repair_id . '&status=' . urlencode("เพิ่มรายละเอียดซ่อมเรียบร้อยแล้ว"));
            } else {
                header('Location: ../frontend/repair_detail.php?status=' . urlencode("เพิ่มรายละเอียดซ่อมเรียบร้อยแล้ว"));
            }
            exit();

        } catch (PDOException $e) {
            // Redirect based on user role for error
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair_detail.php?error=' . urlencode("ไม่สามารถเพิ่มรายละเอียดได้: " . $e->getMessage()));
            } else {
                header('Location: ../frontend/repair_detail.php?error=' . urlencode("ไม่สามารถเพิ่มรายละเอียดได้: " . $e->getMessage()));
            }
            exit();
        } catch (Exception $e) {
            // Redirect based on user role for error
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair_detail.php?error=' . urlencode($e->getMessage()));
            } else {
                header('Location: ../frontend/repair_detail.php?error=' . urlencode($e->getMessage()));
            }
            exit();
        }
    }

    if (isset($_POST['update_repair_detail'])) {
        try {
            $id = intval($_POST['id']);
            $technical_id = intval($_POST['technical_id']);
            $staff_id = intval($_POST['staff_id']);
            $status = $_POST['status'];

            $allowed_status = ["รอซ่อม", "กำลังซ่อม", "เสร็จสิ้น"];
            if (!in_array($status, $allowed_status))
                throw new Exception("สถานะไม่ถูกต้อง");

            if ($id <= 0 || $technical_id <= 0 || $staff_id <= 0)
                throw new Exception("ข้อมูลไม่ถูกต้อง");

            $stmt = $conn->prepare("
                UPDATE repair_detail
                SET technical_id = :technical_id,
                    staff_id = :staff_id,
                    status = :status,
                    updated_at = NOW()
                WHERE id = :id
            ");
            $stmt->execute([
                ':technical_id' => $technical_id,
                ':staff_id' => $staff_id,
                ':status' => $status,
                ':id' => $id
            ]);

            // Redirect based on user role
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                // Need to get the repair_id to redirect properly
                $stmt = $conn->prepare("SELECT repair_id FROM repair_detail WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $repair_detail = $stmt->fetch(PDO::FETCH_ASSOC);
                $repair_id = $repair_detail['repair_id'] ?? 0;

                header('Location: ../frontend/admin/repair_detail.php?repair_id=' . $repair_id . '&status=' . urlencode("แก้ไขรายละเอียดซ่อมเรียบร้อยแล้ว"));
            } else {
                header('Location: ../frontend/repair_detail.php?status=' . urlencode("แก้ไขรายละเอียดซ่อมเรียบร้อยแล้ว"));
            }
            exit();

        } catch (PDOException $e) {
            // Redirect based on user role for error
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair_detail.php?error=' . urlencode("ไม่สามารถแก้ไขรายละเอียดได้: " . $e->getMessage()));
            } else {
                header('Location: ../frontend/repair_detail.php?error=' . urlencode("ไม่สามารถแก้ไขรายละเอียดได้: " . $e->getMessage()));
            }
            exit();
        } catch (Exception $e) {
            // Redirect based on user role for error
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair_detail.php?error=' . urlencode($e->getMessage()));
            } else {
                header('Location: ../frontend/repair_detail.php?error=' . urlencode($e->getMessage()));
            }
            exit();
        }
    }
}

if (isset($_GET['delete_repair_detail'])) {
    try {
        $id = intval($_GET['delete_repair_detail']);
        if ($id <= 0)
            throw new Exception("ID ไม่ถูกต้อง");

        // Need to get repair_id before deletion to redirect properly
        $stmt = $conn->prepare("SELECT repair_id FROM repair_detail WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $repair_detail = $stmt->fetch(PDO::FETCH_ASSOC);
        $repair_id = $repair_detail['repair_id'] ?? 0;

        $stmt = $conn->prepare("DELETE FROM repair_detail WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Redirect based on referer
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referrer, 'admin') !== false && $repair_id > 0) {
            header('Location: ../frontend/admin/repair_detail.php?repair_id=' . $repair_id . '&status=' . urlencode("ลบรายละเอียดซ่อมเรียบร้อยแล้ว"));
        } else {
            header('Location: ../frontend/repair_detail.php?status=' . urlencode("ลบรายละเอียดซ่อมเรียบร้อยแล้ว"));
        }
        exit();

    } catch (PDOException $e) {
        // Redirect based on user role for error
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referrer, 'admin') !== false) {
            header('Location: ../frontend/admin/repair_detail.php?error=' . urlencode("ไม่สามารถลบรายละเอียดได้: " . $e->getMessage()));
        } else {
            header('Location: ../frontend/repair_detail.php?error=' . urlencode("ไม่สามารถลบรายละเอียดได้: " . $e->getMessage()));
        }
        exit();
    } catch (Exception $e) {
        // Redirect based on user role for error
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referrer, 'admin') !== false) {
            header('Location: ../frontend/admin/repair_detail.php?error=' . urlencode($e->getMessage()));
        } else {
            header('Location: ../frontend/repair_detail.php?error=' . urlencode($e->getMessage()));
        }
        exit();
    }
}
?>