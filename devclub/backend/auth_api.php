<?php
session_start();
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['register'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $role = $_POST['role'];

        // Validate password match
        if ($password !== $confirmPassword) {
            header('Location: ../register.php?error=' . urlencode('รหัสผ่านไม่ตรงกัน'));
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare(
                "INSERT INTO users (username, password, role)
                VALUES (:username, :password, :role)"
            );

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':role', $role);

            $stmt->execute();

            header('Location: ../index.php');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                header('Location: ../register.php?error=' . urlencode('ชื่อผู้ใช้งานนี้มีอยู่แล้ว'));
                exit();
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    }

    if (isset($_POST['login'])) {
        try {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);

            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'ADMIN') {
                    header("Location: ../frontend/index.php");
                    exit();
                }

                echo "ไม่มีสิทธิ์เข้าใช้งานระบบนี้";
                exit();
            } else {
                header('Location: ../index.php?error=' . urlencode('ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง'));
                exit();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    // Handle adding new user (admin function)
    if (isset($_POST['add_user'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $conn->prepare(
                "INSERT INTO users (username, password, role)
                VALUES (:username, :password, :role)"
            );

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':role', $role);

            $stmt->execute();

            header('Location: ../frontend/users.php?success=' . urlencode('เพิ่มผู้ใช้งานสำเร็จ'));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                header('Location: ../frontend/form_user.php?error=' . urlencode('ชื่อผู้ใช้งานนี้มีอยู่แล้ว'));
                exit();
            } else {
                header('Location: ../frontend/form_user.php?error=' . urlencode('เกิดข้อผิดพลาดในการบันทึกข้อมูล'));
                exit();
            }
        }
    }

    // Handle updating existing user
    if (isset($_POST['update_user'])) {
        $id = $_POST['id'];
        $username = trim($_POST['username']);
        $role = $_POST['role'];

        try {
            if (!empty($_POST['password'])) {
                // Update password as well
                $password = $_POST['password'];
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare(
                    "UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id"
                );
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $hashedPassword);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':id', $id);
            } else {
                // Update without changing password
                $stmt = $conn->prepare(
                    "UPDATE users SET username = :username, role = :role WHERE id = :id"
                );
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':id', $id);
            }

            $stmt->execute();

            header('Location: ../frontend/users.php?success=' . urlencode('แก้ไขข้อมูลผู้ใช้งานสำเร็จ'));
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                header('Location: ../frontend/form_user.php?id=' . $id . '&error=' . urlencode('ชื่อผู้ใช้งานนี้มีอยู่แล้ว'));
                exit();
            } else {
                header('Location: ../frontend/form_user.php?id=' . $id . '&error=' . urlencode('เกิดข้อผิดพลาดในการบันทึกข้อมูล'));
                exit();
            }
        }
    }
}

// Handle deleting user
if (isset($_GET['delete_user'])) {
    try {
        $id = $_GET['delete_user'];

        if (!is_numeric($id)) {
            header('Location: ../frontend/users.php?error=' . urlencode('ID ไม่ถูกต้อง'));
            exit();
        }

        // Don't allow deleting own account
        if ($id == $_SESSION['user_id']) {
            header('Location: ../frontend/users.php?error=' . urlencode('ไม่สามารถลบบัญชีของคุณเองได้'));
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        header('Location: ../frontend/users.php?success=' . urlencode('ลบผู้ใช้งานสำเร็จ'));
        exit();
    } catch (PDOException $e) {
        header('Location: ../frontend/users.php?error=' . urlencode('ไม่สามารถลบผู้ใช้งานได้'));
        exit();
    }
}
