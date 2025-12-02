<?php
$host = "localhost";
$user = "root";
$password = "";
$db_name = "training";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db_name;charset$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $conn = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
