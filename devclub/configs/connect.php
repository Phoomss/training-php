<?php
$host = "localhost";
$username = "root";
$password = "";
$db_name = "devclub";
$charset = "utf8mb4"; 

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
    // echo "Connected successfully";
} catch (PDOException $e) {
    dir("Connection failed: " . $e->getMessage());
}
