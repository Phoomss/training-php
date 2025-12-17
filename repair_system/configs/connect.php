<?php
$host = "localhost";
$username = 'root';
$password = '';
$db_name = 'repair_system';
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

?>