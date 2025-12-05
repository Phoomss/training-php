<?php
session_start();
require_once '../configs/connect.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['register'])) {

        // Log ค่าที่รับจากฟอร์มก่อน
        echo "<pre>";
        echo "=== DEBUG INPUT ===\n";
        print_r($_POST);
        echo "====================\n";
        echo "</pre>";

        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = $_POST['role'];

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
            if ($e->getCode() === 2300) {
                echo "Username alredy exit";
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
                $error = "Username หรือ Password ไม่ถูกต้อง!";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
