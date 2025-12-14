<?php
require_once '../configs/connect.php';

/* ======================
   UPLOAD FILE
====================== */
if (isset($_POST['upload']) && isset($_FILES['file'])) {

    $file = $_FILES['file'];
    $filename = $file['name'];
    $tmp = $file['tmp_name'];
    $size = $file['size'];
    $type = $file['type'];

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $allowed_image = ['jpg', 'jpeg', 'png', 'gif'];
    $allowed_doc   = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

    if (in_array($ext, $allowed_image)) {
        $serverFolder = __DIR__ . '/../uploads/images/';
        $webFolder    = 'uploads/images/';
    } elseif (in_array($ext, $allowed_doc)) {
        $serverFolder = __DIR__ . '/../uploads/documents/';
        $webFolder    = 'uploads/documents/';
    } else {
        die("❌ ไม่รองรับไฟล์ประเภทนี้");
    }

    if (!is_dir($serverFolder)) {
        mkdir($serverFolder, 0777, true);
    }

    $newname = uniqid() . "." . $ext;

    if (move_uploaded_file($tmp, $serverFolder . $newname)) {
        $webPath = $webFolder . $newname;

        $stmt = $conn->prepare("
            INSERT INTO files (filename, filepath, filetype, filesize)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$newname, $webPath, $type, $size]);

        header('Location: ../index.php');
        exit();
    }

    die('อัปโหลดล้มเหลว');
}

/* ======================
   UPDATE FILE
====================== */
if (isset($_POST['update']) && isset($_POST['id'])) {

    $id = (int) $_POST['id'];

    // 1️⃣ ดึงข้อมูลไฟล์เก่า
    $stmt = $conn->prepare("SELECT filepath FROM files WHERE id = ?");
    $stmt->execute([$id]);
    $oldFile = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$oldFile) {
        die('ไม่พบข้อมูลไฟล์');
    }

    // ถ้ามีการเลือกไฟล์ใหม่
    if (!empty($_FILES['file']['name'])) {

        $file = $_FILES['file'];
        $filename = $file['name'];
        $tmp = $file['tmp_name'];
        $size = $file['size'];
        $type = $file['type'];

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $allowed_image = ['jpg', 'jpeg', 'png', 'gif'];
        $allowed_doc   = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

        if (in_array($ext, $allowed_image)) {
            $serverFolder = __DIR__ . '/../uploads/images/';
            $webFolder    = 'uploads/images/';
        } elseif (in_array($ext, $allowed_doc)) {
            $serverFolder = __DIR__ . '/../uploads/documents/';
            $webFolder    = 'uploads/documents/';
        } else {
            die("❌ ไม่รองรับไฟล์ประเภทนี้");
        }

        if (!is_dir($serverFolder)) {
            mkdir($serverFolder, 0777, true);
        }

        $newname = uniqid() . "." . $ext;

        // 2️⃣ อัปโหลดไฟล์ใหม่
        if (move_uploaded_file($tmp, $serverFolder . $newname)) {

            $newWebPath = $webFolder . $newname;

            // 3️⃣ ลบไฟล์เก่า
            $oldServerPath = __DIR__ . '/../' . $oldFile['filepath'];
            if (file_exists($oldServerPath)) {
                unlink($oldServerPath);
            }

            // 4️⃣ UPDATE DB
            $stmt = $conn->prepare("
                UPDATE files
                SET filename = ?, filepath = ?, filetype = ?, filesize = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $newname,
                $newWebPath,
                $type,
                $size,
                $id
            ]);

            header('Location: ../index.php');
            exit();
        }

        die('อัปโหลดไฟล์ใหม่ล้มเหลว');
    }

    // ❗ ถ้าไม่เลือกไฟล์ใหม่ (update อย่างอื่น)
    header('Location: ../index.php');
    exit();
}

/* ======================
   DELETE FILE
====================== */
if (isset($_POST['delete']) && isset($_POST['id'])) {

    $id = (int) $_POST['id'];

    $stmt = $conn->prepare("SELECT filepath FROM files WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$file) {
        die('ไม่พบข้อมูลไฟล์');
    }

    $serverPath = __DIR__ . '/../' . $file['filepath'];

    if (file_exists($serverPath)) {
        unlink($serverPath);
    }

    $stmt = $conn->prepare("DELETE FROM files WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: ../index.php');
    exit();
}

die('Invalid request');
