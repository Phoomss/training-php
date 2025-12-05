<?php
require_once "../configs/connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {

        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "รูปแบบอีเมลไม่ถูกต้อง!";
            header("Location: user.php?error=" . urlencode($errorMessage));
            exit();
        }

        try {
            $stmt = $conn->prepare(
                "INSERT INTO members (title, firstname, lastname, email, major, year)
                VALUES (:title, :firstname, :lastname, :email, :major, :year)"
            );

            $stmt->bindParam(':title', $_POST['title']);
            $stmt->bindParam(':firstname', $_POST['firstname']);
            $stmt->bindParam(':lastname', $_POST['lastname']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':major', $_POST['major']);
            $stmt->bindParam(':year', $_POST['year']);

            $stmt->execute();

            header("Location: user.php?status=success");
            exit();
        } catch (PDOException $e) {

            // error default
            $errorMessage = "มีข้อผิดพลาดในการบันทึกข้อมูล";

            // 3) ตรวจสอบ Duplicate key (23000)
            if ($e->getCode() == 23000) {
                $msg = $e->getMessage();

                if (strpos($msg, 'email') !== false) {
                    $errorMessage = "อีเมลนี้ถูกใช้แล้ว!";
                }

                if (strpos($msg, 'phone') !== false) {
                    $errorMessage = "เบอร์โทรนี้ถูกใช้แล้ว!";
                }
            }

            header("Location: user.php?error=" . urlencode($errorMessage));
            exit();
        }
    }

    if (isset($_POST['update'])) {

        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "รูปแบบอีเมลไม่ถูกต้อง!";
            header("Location: user.php?error=" . urlencode($errorMessage));
            exit();
        }

        try {
            $stmt = $conn->prepare(
                "UPDATE members 
            SET title = :title, firstname = :firstnanme, lastname = :lastname, email = :email, major = :major, year = :year
            WHERE id = :id"
            );

            $stmt->bindParam(':title', $_POST['title']);
            $stmt->bindParam(':firstname', $_POST['firstname']);
            $stmt->bindParam(':lastname', $_POST['lastname']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':major', $_POST['major']);
            $stmt->bindParam(':year', $_POST['year']);

            $stmt->execute();

            header("Location: user.php?status=success");
            exit();
        } catch (PDOException $e) {

            // error default
            $errorMessage = "มีข้อผิดพลาดในการบันทึกข้อมูล";

            // 3) ตรวจสอบ Duplicate key (23000)
            if ($e->getCode() == 23000) {
                $msg = $e->getMessage();

                if (strpos($msg, 'email') !== false) {
                    $errorMessage = "อีเมลนี้ถูกใช้แล้ว!";
                }

                if (strpos($msg, 'phone') !== false) {
                    $errorMessage = "เบอร์โทรนี้ถูกใช้แล้ว!";
                }
            }

            header("Location: user.php?error=" . urlencode($errorMessage));
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
        };

        $stmt = $conn->prepare("DELETE FROM members WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

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
