<?php
session_start();
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ================= REGISTER ================= */
    if (isset($_POST['register'])) {

        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $role = strtolower($_POST['role']); // admin / student

        if ($password !== $confirmPassword) {
            header('Location: ../register.php?error=' . urlencode('รหัสผ่านไม่ตรงกัน'));
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare("
                INSERT INTO auths (username, password, role)
                VALUES (:username, :password, :role)
            ");

            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':role' => $role
            ]);

            // Get the inserted auth ID
            $auth_id = $conn->lastInsertId();

            // If role is STUDENT, create a corresponding student record
            if ($role === 'STUDENT') {
                $stmt_student = $conn->prepare("
                    INSERT INTO students (auth_id, student_id, title, firstname, lastname)
                    VALUES (:auth_id, :student_id, :title, :firstname, :lastname)
                ");

                // Use username as student_id for now, can be updated later by admin
                $stmt_student->execute([
                    ':auth_id' => $auth_id,
                    ':student_id' => $username,
                    ':title' => '',
                    ':firstname' => $username, // Will be updated later
                    ':lastname' => '' // Will be updated later
                ]);
            }

            header("Location: ../index.php?status=" . urlencode("สมัครสมาชิกสำเร็จ"));
            exit();

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                header('Location: ../register.php?error=' . urlencode('ชื่อผู้ใช้งานนี้มีอยู่แล้ว'));
                exit();
            } else {
                die("Error: " . $e->getMessage());
            }
        }
    }

    /* ================= LOGIN ================= */
    if (isset($_POST['login'])) {
        try {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $stmt = $conn->prepare("
                SELECT id, username, password, role
                FROM auths
                WHERE username = :username
                LIMIT 1
            ");
            $stmt->execute([':username' => $username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {

                /* เก็บ auth_id ลง session (สำคัญมาก) */
                $_SESSION['auth_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'ADMIN') {
                    header("Location: ../frontend/admin/index.php");
                    exit();
                }

                if ($user['role'] === 'STUDENT') {
                    header("Location: ../frontend/student/index.php");
                    exit();
                }

                header("Location: ../index.php?error=" . urlencode("ไม่มีสิทธิ์เข้าใช้งาน"));
                exit();

            } else {
                header('Location: ../index.php?error=' . urlencode('ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง'));
                exit();
            }

        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
