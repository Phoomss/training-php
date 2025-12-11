<?php
$host="sql300.infinityfree.com";
$username="if0_40638783";
$password="It7H0dr1jA";
$db_name="if0_40638783_report_activity_student";

$dsn = "mysql:host=$host;dbname=$db_name;charset=utf8md4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try {
    $conn = new PDO($dsn, $username, $password, $options);
    echo "Database Connection Successfully";
} catch (PDOException $e) {
    dir("Database Connection Failed: " . $e->getMessage());
}
?>