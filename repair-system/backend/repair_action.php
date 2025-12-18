<?php
require_once('../configs/connect.php');

function uploadImage($file)
{
    if ($file['error'] !== 0) {
        throw new Exception("กรุณาเลือกรูปภาพ");
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allow = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allow)) {
        throw new Exception("ไฟล์รูปไม่ถูกต้อง (jpg, jpeg, png เท่านั้น)");
    }

    $folder = __DIR__ . '/../upload/repair/';
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $newname = uniqid() . '.' . $ext;
    $filepath = 'upload/repair/' . $newname;

    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../' . $filepath)) {
        throw new Exception("ไม่สามารถอัปโหลดไฟล์รูปได้");
    }

    return $filepath;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_repair'])) {
        try {
            $student_id  = intval($_POST['student_id']);
            $equipment_id = intval($_POST['equipment_id']);
            $details     = trim($_POST['details']);
            $image       = uploadImage($_FILES['image']);

            $stmt = $conn->prepare("
            INSERT INTO repair (student_id, equipment_id, details, image)
            VALUES (:student_id, :equipment_id, :details, :image)
        ");

            $stmt->execute([
                ':student_id'  => $student_id,
                ':equipment_id' => $equipment_id,
                ':details'     => $details,
                ':image'       => $image
            ]);

            header("Location: ../frontend/student/repair.php?status=" . urlencode("เพิ่มข้อมูลเสร็จสิ้น"));
            exit();
        } catch (Exception $e) {
            header("Location: ../frontend/student/repair.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    }

    if (isset($_POST['update_repair'])) {
        try {
            $id           = intval($_POST['id']);
            $student_id   = intval($_POST['student_id']);
            $equipment_id  = intval($_POST['equipment_id']);
            $details      = trim($_POST['details']);
            // ดึงรูปเก่า
            $stmt_old = $conn->prepare("SELECT image FROM repair WHERE id = :id");
            $stmt_old->execute([':id' => $id]);
            $old = $stmt_old->fetch(PDO::FETCH_ASSOC);
            $old_image = $old['image'] ?? null;

            $sql = "
            UPDATE repair
            SET student_id=:student_id, equipment_id=:equipment_id, details=:details,
        ";

            $params = [
                ':student_id'    => $student_id,
                ':equipment_id'   => $equipment_id,
                ':details'       => $details,
                ':id'            => $id
            ];

            // ถ้ามีอัปโหลดรูปใหม่
            if ($_FILES['image']['error'] === 0) {
                $new_image = uploadImage($_FILES['image']);

                if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
                    unlink(__DIR__ . '/../' . $old_image);
                }

                $sql .= ", image=:image";
                $params[':image'] = $new_image;
            }

            $sql .= " WHERE id=:id";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            header("Location: ../frontend/admin/index.php?status=" . urlencode("แก้ไขข้อมูลเสร็จสิ้น"));
            exit();
        } catch (Exception $e) {
            header("Location: ../frontend/admin/form_repair.php?id=" . $id . "&error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}

if (isset($_GET['delete'])) {
    try {
        $id = intval($_GET['delete']);
        if ($id <= 0) {
            throw new Exception("ID ไม่ถูกต้อง");
        }

        $stmt_old = $conn->prepare("SELECT image FROM repair WHERE id = :id");
        $stmt_old->execute([':id' => $id]);
        $old = $stmt_old->fetch(PDO::FETCH_ASSOC);
        $old_image = $old['image'] ?? null;

        $stmt = $conn->prepare("DELETE FROM repair WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
            unlink(__DIR__ . '/../' . $old_image);
        }

        header("Location: ../frontend/admin/index.php?status=" . urlencode("ลบข้อมูลเสร็จสิ้น"));
        exit();
    } catch (Exception $e) {
        header("Location: ../frontend/admin/index.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
