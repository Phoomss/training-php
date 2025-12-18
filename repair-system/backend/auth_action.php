<?php
require_once '../configs/connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    /* ================= REGISTER ================= */
    if (isset($_POST['register'])) {

        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $role = strtolower($_POST['role']);

        if ($password !== $confirmPassword) {
            header('Location: ../register.php?error=' . urlencode('รหัสผ่านไม่ตรงกัน'));
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $conn->prepare("
                INSERT INTO auth (username, password, role)
                VALUES (:username, :password, :role)
            ");

            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':role' => $role
            ]);

            $auth_id = $conn->lastInsertId();

            /* ===== student profile ===== */
            if ($role === 'student') {

                $title = $_POST['title'];
                $firstname = $_POST['firstname'];
                $lastname = $_POST['lastname'];
                $student_id = $_POST['student_id'];

                $stmt_student = $conn->prepare("
                    INSERT INTO student (title, firstname, lastname, student_id, auth_id)
                    VALUES (:title, :firstname, :lastname, :student_id, :auth_id)
                ");

                $stmt_student->execute([
                    ':title' => $title,
                    ':firstname' => $firstname,
                    ':lastname' => $lastname,
                    ':student_id' => $student_id,
                    ':auth_id' => $auth_id
                ]);
            }

            if ($role === 'technical') {

                $title = $_POST['title'];
                $firstname = $_POST['firstname'];
                $lastname = $_POST['lastname'];
                $phone = $_POST['phone'];

                $stmt_student = $conn->prepare("
                    INSERT INTO student (title, firstname, lastname, phone, auth_id)
                    VALUES (:title, :firstname, :lastname, :phone, :auth_id)
                ");

                $stmt_student->execute([
                    ':title' => $title,
                    ':firstname' => $firstname,
                    ':lastname' => $lastname,
                    ':phone' => $phone,
                    ':auth_id' => $auth_id
                ]);
            }
            header("Location: ../index.php?status=" . urlencode('สมัครสมาชิกสำเร็จ'));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                header('Location: ../register.php?error=' . urlencode('ชื่อผู้ใช้งานนี้มีอยู่แล้ว'));
                exit();
            }
            die("Error: " . $e->getMessage());
        }
    }

    /* ================= LOGIN ================= */
    if (isset($_POST['login'])) {

        $username = trim($_POST['username']);
        $password = $_POST['password'];

        try {
            $stmt = $conn->prepare("
                SELECT id, username, password, role
                FROM auth
                WHERE username = :username
                LIMIT 1
            ");
            $stmt->execute([':username' => $username]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {

                $_SESSION['auth_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                switch ($user['role']) {
                    case 'admin':
                        header('Location: ../frontend/admin/index.php');
                        break;
                    case 'student':
                        header('Location: ../frontend/student/index.php');
                        break;
                    case 'technical':
                        header('Location: ../frontend/technical/index.php');
                        break;
                    default:
                        header('Location: ../index.php?error=' . urlencode('ไม่มีสิทธิ์เข้าใช้งาน'));
                }
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
