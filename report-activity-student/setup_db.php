<?php
// Create database and tables for the student activity reporting system
require_once 'configs/connect.php';

echo "Checking and creating database tables...\n";

try {
    // Create tables with proper definitions to match the application needs

    // Auths table
    $sql = "CREATE TABLE IF NOT EXISTS auths (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        role ENUM('ADMIN', 'STUDENT') NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Created auths table\n";

    // Students table (keeping student_id as VARCHAR to match the existing code)
    $sql = "CREATE TABLE IF NOT EXISTS students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        auth_id INT UNIQUE NOT NULL,
        student_id VARCHAR(255) UNIQUE NOT NULL,
        title VARCHAR(255),
        firstname VARCHAR(255) NOT NULL,
        lastname VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Created students table\n";

    // Activities table (use the correct spelling to match the code)
    $sql = "CREATE TABLE IF NOT EXISTS activites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        activity_name VARCHAR(255) NOT NULL,
        date DATE NOT NULL,
        time TIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Created activites table\n";

    // Activity details table (student_id should match the students table type - VARCHAR)
    $sql = "CREATE TABLE IF NOT EXISTS activity_details (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id VARCHAR(255) NOT NULL,
        activity_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql);
    echo "Created activity_details table\n";

    // Add foreign key constraints separately to avoid issues
    try {
        $sql = "ALTER TABLE students ADD CONSTRAINT fk_student_auth_id FOREIGN KEY (auth_id) REFERENCES auths(id)";
        $conn->exec($sql);
    } catch (Exception $e) {
        // Constraint might already exist
        echo "Foreign key constraint for auth_id might already exist\n";
    }

    try {
        $sql = "ALTER TABLE activity_details ADD CONSTRAINT fk_activity_detail_student_id FOREIGN KEY (student_id) REFERENCES students(student_id)";
        $conn->exec($sql);
    } catch (Exception $e) {
        // Constraint might already exist
        echo "Foreign key constraint for student_id might already exist\n";
    }

    try {
        $sql = "ALTER TABLE activity_details ADD CONSTRAINT fk_activity_detail_activity_id FOREIGN KEY (activity_id) REFERENCES activites(id)";
        $conn->exec($sql);
    } catch (Exception $e) {
        // Constraint might already exist
        echo "Foreign key constraint for activity_id might already exist\n";
    }

    // Insert sample data if not exists
    $sql = "INSERT IGNORE INTO auths (username, password, role) VALUES
            ('admin', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'ADMIN'),
            ('student1', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'STUDENT')";
    $conn->exec($sql);
    echo "Added sample users\n";

    // Insert sample student with a reference to auth_id
    // First, get the auth_id for student1
    $stmt = $conn->prepare("SELECT id FROM auths WHERE username = 'student1'");
    $stmt->execute();
    $student_auth = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student_auth) {
        $sql = "INSERT IGNORE INTO students (auth_id, student_id, title, firstname, lastname, email) VALUES
                ('{$student_auth['id']}', '6012345678', 'นาย', 'สมชาย', 'ใจดี', 'student1@example.com')";
        $conn->exec($sql);
        echo "Added sample student\n";
    }

    echo "Database setup completed successfully!\n";
} catch (PDOException $e) {
    echo "Error during setup: " . $e->getMessage() . "\n";
}