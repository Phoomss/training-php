<?php
require_once('../configs/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validate required fields
    if (empty($title) || empty($firstname) || empty($lastname) || empty($username)) {
        header("Location: ../frontend/admin/form_technical.php?error=" . urlencode("กรุณากรอกข้อมูลที่จำเป็นทั้งหมด"));
        exit();
    }

    // Validate password if provided
    if (!empty($password) && strlen($password) < 6) {
        header("Location: ../frontend/admin/form_technical.php?error=" . urlencode("รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร"));
        exit();
    }

    // Check if password and confirmation match
    if (!empty($password) && $password !== $confirmPassword) {
        header("Location: ../frontend/admin/form_technical.php?error=" . urlencode("ยืนยันรหัสผ่านไม่ตรงกัน"));
        exit();
    }

    if (isset($_POST['id'])) {
        // Update existing technical staff
        $id = intval($_POST['id']);
        
        // Get current technical info
        $stmt = $conn->prepare("SELECT auth_id FROM technical WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $currentTech = $stmt->fetch();
        
        if (!$currentTech) {
            header("Location: ../frontend/admin/form_technical.php?id=" . $id . "&error=" . urlencode("ไม่พบข้อมูลช่างเทคนิคนี้"));
            exit();
        }
        
        try {
            $conn->beginTransaction();
            
            // Check if username already exists for another user
            $stmt = $conn->prepare("SELECT id FROM auth WHERE username = :username AND id != :auth_id");
            $stmt->execute([':username' => $username, ':auth_id' => $currentTech['auth_id']]);
            if ($stmt->fetch()) {
                throw new Exception("ชื่อผู้ใช้งานนี้มีอยู่แล้ว");
            }
            
            // Update auth table if needed
            if (!empty($password)) {
                // Update with new password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE auth SET username = :username, password = :password WHERE id = :auth_id");
                $stmt->execute([
                    ':username' => $username,
                    ':password' => $hashedPassword,
                    ':auth_id' => $currentTech['auth_id']
                ]);
            } else {
                // Update without changing password
                $stmt = $conn->prepare("UPDATE auth SET username = :username WHERE id = :auth_id");
                $stmt->execute([
                    ':username' => $username,
                    ':auth_id' => $currentTech['auth_id']
                ]);
            }
            
            // Update technical table
            $stmt = $conn->prepare("
                UPDATE technical 
                SET title = :title, 
                    firstname = :firstname, 
                    lastname = :lastname, 
                    phone = :phone 
                WHERE id = :id
            ");
            $stmt->execute([
                ':title' => $title,
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':phone' => $phone,
                ':id' => $id
            ]);

            $conn->commit();
            
            header("Location: ../frontend/admin/technical.php?status=" . urlencode("แก้ไขข้อมูลช่างเทคนิคเรียบร้อย"));
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: ../frontend/admin/form_technical.php?id=" . $id . "&error=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        // Add new technical staff
        if (empty($password)) {
            header("Location: ../frontend/admin/form_technical.php?error=" . urlencode("กรุณากรอกรหัสผ่าน"));
            exit();
        }
        
        try {
            $conn->beginTransaction();
            
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM auth WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetch()) {
                throw new Exception("ชื่อผู้ใช้งานนี้มีอยู่แล้ว");
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert into auth table first
            $stmt = $conn->prepare("INSERT INTO auth (username, password, role) VALUES (:username, :password, 'technical')");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword
            ]);
            $authId = $conn->lastInsertId();
            
            // Insert into technical table
            $stmt = $conn->prepare("
                INSERT INTO technical (title, firstname, lastname, phone, auth_id) 
                VALUES (:title, :firstname, :lastname, :phone, :auth_id)
            ");
            $stmt->execute([
                ':title' => $title,
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':phone' => $phone,
                ':auth_id' => $authId
            ]);

            $conn->commit();
            
            header("Location: ../frontend/admin/technical.php?status=" . urlencode("เพิ่มช่างเทคนิคเรียบร้อย"));
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            header("Location: ../frontend/admin/form_technical.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    }
}

// Delete technical staff
if (isset($_GET['delete_technical'])) {
    $id = intval($_GET['delete_technical']);
    
    try {
        $conn->beginTransaction();
        
        // Get the auth_id before deleting the technical record
        $stmt = $conn->prepare("SELECT auth_id FROM technical WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $technical = $stmt->fetch();
        
        if (!$technical) {
            throw new Exception("ไม่พบข้อมูลช่างเทคนิคนี้");
        }
        
        // Check if the technical is associated with any repair details
        $stmt = $conn->prepare("SELECT COUNT(*) FROM repair_detail WHERE technical_id = :id");
        $stmt->execute([':id' => $id]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("ไม่สามารถลบช่างเทคนิคนี้ได้ เนื่องจากมีงานซ่อมที่เกี่ยวข้อง");
        }
        
        // Delete technical record (auth record will be deleted via foreign key CASCADE)
        $stmt = $conn->prepare("DELETE FROM technical WHERE id = :id");
        $stmt->execute([':id' => $id]);
        
        $conn->commit();
        
        header("Location: ../frontend/admin/technical.php?status=" . urlencode("ลบช่างเทคนิคเรียบร้อย"));
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: ../frontend/admin/technical.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}