<?php
session_start();
require_once '../configs/connect.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['register'])){
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try{
            $stmt = $conn->prepare("INSERT INTO auths (username, password, role) 
            VALUES (:username, :password, :role) ");

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $role->bindParam(':role', $role);

            $stmt->execute();

            header("Location: ../index.php");
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

    if(isset($_POST['login'])){
        try {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            $stmt = $conn->prepare("
            SELECT * FROM auths 
            WHERE username = :username");
            $stmt->bindParam(':uusername', $username);

            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if($user && password_verify($password, $user['password'])){
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                if($user['role'] === 'ADMIN'){
                    header("Location: ../frontend/admin/index.php");
                    exit();
                }else if($user['role'] === 'STUDENT'){
                    header("Location: ../frontend/student/index.php");
                    exit();
                }

                echo "ไม่มีสิทธิ์เข้าใช้งานระบบนี้";
                exit();
            }else{
                header('Location: ../index.php?error=' . urldecode('ชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง'));;
                exit();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

?>