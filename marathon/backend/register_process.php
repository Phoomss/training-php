<?php
require_once '../configs/conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

try {
    $conn->beginTransaction();

    // runner
    $stmt = $conn->prepare("
        INSERT INTO runner
        (first_name, last_name, date_of_birth, gender, phone)
        VALUES (?,?,?,?,?)
    ");
    $stmt->execute([
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['date_of_birth'],
        $_POST['gender'],
        $_POST['phone']
    ]);

    $runner_id = $conn->lastInsertId();

    // registration
    $stmt = $conn->prepare("
        INSERT INTO registration
        (runner_id, category_id, shipping_id, reg_date, shirt_size, status)
        VALUES (?,?,?,CURDATE(),?, 'Pending')
    ");
    $stmt->execute([
        $runner_id,
        $_POST['category_id'],
        $_POST['shipping_id'],
        $_POST['shirt_size']
    ]);

    $conn->commit();

    header('Location: ../index.php?success=1');
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    header('Location: ../index.php?error=1');
    exit;
}
