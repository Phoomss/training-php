<?php
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['add_repair'])) {
        try {
            $user_id = intval($_POST['user_id']);
            $equipment_id = intval($_POST['equipment_id']);
            $locationD_id = intval($_POST['locationD_id']);
            $img = $_FILES['image'];

            if ($user_id <= 0 || $equipment_id <= 0 || $locationD_id <= 0) {
                throw new Exception("ข้อมูลไม่ถูกต้อง");
            }

            if ($img['error'] !== 0)
                throw new Exception("เกิดข้อผิดพลาดกับไฟล์รูป");

            $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
            $allow = ['jpg', 'jpeg', 'png'];
            if (!in_array($ext, $allow))
                throw new Exception("ไฟล์รูปไม่ถูกต้อง (jpg, jpeg, png เท่านั้น)");

            $folder = __DIR__ . '/../uploads/repair/';
            if (!is_dir($folder))
                mkdir($folder, 0777, true);

            $newname = uniqid() . "." . $ext;
            $filepath = 'uploads/repair/' . $newname;
            if (!move_uploaded_file($img['tmp_name'], __DIR__ . '/../' . $filepath)) {
                throw new Exception("ไม่สามารถอัปโหลดไฟล์รูปได้");
            }

            $stmt = $conn->prepare("
                INSERT INTO repair (user_id, equipment_id, image, locationD_id, created_at)
                VALUES (:user_id, :equipment_id, :image, :locationD_id, NOW())
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':equipment_id' => $equipment_id,
                ':image' => $filepath,
                ':locationD_id' => $locationD_id
            ]);

            // Redirect based on user role
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair.php?status=' . urlencode("เพิ่มรายการซ่อมเรียบร้อยแล้ว"));
            } else {
                header('Location: ../frontend/student/repair.php?status=' . urlencode("เพิ่มรายการซ่อมเรียบร้อยแล้ว"));
            }
            exit();

        } catch (PDOException $e) {
            // Redirect based on user role for error
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair.php?error=' . urlencode("เพิ่มรายการซ่อมไม่สำเร็จ: " . $e->getMessage()));
            } else {
                header('Location: ../frontend/student/repair.php?error=' . urlencode("เพิ่มรายการซ่อมไม่สำเร็จ: " . $e->getMessage()));
            }
            exit();
        } catch (Exception $e) {
            // Redirect based on user role for error
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair.php?error=' . urlencode($e->getMessage()));
            } else {
                header('Location: ../frontend/student/repair.php?error=' . urlencode($e->getMessage()));
            }
            exit();
        }
    }

    if (isset($_POST['update_repair'])) {
        try {
            $id = intval($_POST['id']);
            $user_id = intval($_POST['user_id']);
            $equipment_id = intval($_POST['equipment_id']);
            $locationD_id = intval($_POST['locationD_id']);
            $img = $_FILES['image'];

            if ($id <= 0 || $user_id <= 0 || $equipment_id <= 0 || $locationD_id <= 0) {
                throw new Exception("ข้อมูลไม่ถูกต้อง");
            }

            // ดึงข้อมูลเก่าจาก DB
            $stmt_old = $conn->prepare("SELECT image FROM repair WHERE id=:id");
            $stmt_old->execute([':id' => $id]);
            $old = $stmt_old->fetch(PDO::FETCH_ASSOC);
            $old_image = $old['image'] ?? null;

            // เตรียม SQL และ params
            $params = [
                ':user_id' => $user_id,
                ':equipment_id' => $equipment_id,
                ':locationD_id' => $locationD_id,
                ':id' => $id
            ];
            $sql = "UPDATE repair SET user_id=:user_id, equipment_id=:equipment_id, locationD_id=:locationD_id";

            if ($img['error'] === 0) {
                $ext = strtolower(pathinfo($img['name'], PATHINFO_EXTENSION));
                $allow = ['jpg', 'jpeg', 'png'];
                if (!in_array($ext, $allow))
                    throw new Exception("ไฟล์รูปไม่ถูกต้อง (jpg, jpeg, png เท่านั้น)");

                $folder = __DIR__ . '/../uploads/repair/';
                if (!is_dir($folder))
                    mkdir($folder, 0777, true);

                $newname = uniqid() . "." . $ext;
                $filepath = 'uploads/repair/' . $newname;
                if (!move_uploaded_file($img['tmp_name'], __DIR__ . '/../' . $filepath)) {
                    throw new Exception("ไม่สามารถอัปโหลดไฟล์รูปได้");
                }

                // ลบไฟล์เก่า
                if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
                    unlink(__DIR__ . '/../' . $old_image);
                }

                $sql .= ", image=:image";
                $params[':image'] = $filepath;
            }

            $sql .= " WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            // Redirect based on user role
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair.php?status=' . urlencode("แก้ไขรายการซ่อมเรียบร้อยแล้ว"));
            } else {
                header('Location: ../frontend/student/repair.php?status=' . urlencode("แก้ไขรายการซ่อมเรียบร้อยแล้ว"));
            }
            exit();

        } catch (PDOException $e) {
            // Redirect based on user role for error
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair.php?error=' . urlencode("แก้ไขรายการซ่อมไม่สำเร็จ: " . $e->getMessage()));
            } else {
                header('Location: ../frontend/student/repair.php?error=' . urlencode("แก้ไขรายการซ่อมไม่สำเร็จ: " . $e->getMessage()));
            }
            exit();
        } catch (Exception $e) {
            // Redirect based on user role for error
            $referrer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referrer, 'admin') !== false) {
                header('Location: ../frontend/admin/repair.php?error=' . urlencode($e->getMessage()));
            } else {
                header('Location: ../frontend/student/repair.php?error=' . urlencode($e->getMessage()));
            }
            exit();
        }
    }
}


if (isset($_GET['delete_repair'])) {
    try {
        $id = intval($_GET['delete_repair']);
        if ($id <= 0)
            throw new Exception("ID ไม่ถูกต้อง");

        // ดึงข้อมูลรูปเก่า
        $stmt_old = $conn->prepare("SELECT image FROM repair WHERE id=:id");
        $stmt_old->execute([':id' => $id]);
        $old = $stmt_old->fetch(PDO::FETCH_ASSOC);
        $old_image = $old['image'] ?? null;

        // ลบจาก DB
        $stmt = $conn->prepare("DELETE FROM repair WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // ลบไฟล์รูปเก่า
        if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
            unlink(__DIR__ . '/../' . $old_image);
        }

        // Redirect based on user role
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referrer, 'admin') !== false) {
            header('Location: ../frontend/admin/repair.php?status=' . urlencode("ลบรายการซ่อมเรียบร้อยแล้ว"));
        } else {
            header('Location: ../frontend/student/repair.php?status=' . urlencode("ลบรายการซ่อมเรียบร้อยแล้ว"));
        }
        exit();

    } catch (PDOException $e) {
        // Redirect based on user role for error
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referrer, 'admin') !== false) {
            header('Location: ../frontend/admin/repair.php?error=' . urlencode("ไม่สามารถลบรายการซ่อมได้: " . $e->getMessage()));
        } else {
            header('Location: ../frontend/student/repair.php?error=' . urlencode("ไม่สามารถลบรายการซ่อมได้: " . $e->getMessage()));
        }
        exit();
    } catch (Exception $e) {
        // Redirect based on user role for error
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referrer, 'admin') !== false) {
            header('Location: ../frontend/admin/repair.php?error=' . urlencode($e->getMessage()));
        } else {
            header('Location: ../frontend/student/repair.php?error=' . urlencode($e->getMessage()));
        }
        exit();
    }
}
?>