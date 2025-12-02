<?php
$host = "localhost";
$user = "root";
$password = "";
$db_name = "training";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
