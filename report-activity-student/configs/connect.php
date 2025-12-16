<?php
$host = "sql300.infinityfree.com";
$username = "if0_40638783";
$password = "It7H0dr1jA";
$db_name = "if0_40638783_report_activity_student";
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