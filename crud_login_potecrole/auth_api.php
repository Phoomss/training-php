<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['register'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $role = $_POST['role'];

        try {
            $stmt = $conn->prepare("
            INSERT INTO auth (username,email,password, role)
            VALUES (:username, :email, :password, :role)
            ");

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':role', $role);

            $stmt->execute();

            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "Username or email already";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    }

    if (isset($_POST['login'])) {
        try {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $stmt = $conn->prepare("SELECT * FROM auth WHERE username=:username");
            $stmt->bindParam(':username', $username);

            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Username หรือ Password ไม่ถูกต้อง!";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "Username or email already";
            } else {
                echo "Error: " . $e->getMessage();
            }
        }
    }
}
