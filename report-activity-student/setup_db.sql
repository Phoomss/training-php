-- Database setup for Student Activity Reporting System

-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS report_activity_student;
USE report_activity_student;

-- Create auths table
CREATE TABLE IF NOT EXISTS auths (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'STUDENT') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create students table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auth_id INT UNIQUE NOT NULL,
    student_id VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255),
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auth_id) REFERENCES auths(id)
);

-- Create activites table
CREATE TABLE IF NOT EXISTS activites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_name VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create activity_details table
CREATE TABLE IF NOT EXISTS activity_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(255) NOT NULL,
    activity_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (activity_id) REFERENCES activites(id)
);

-- Insert sample data
INSERT IGNORE INTO auths (username, password, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN'), -- password: password
('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'STUDENT'); -- password: password

-- Insert sample student
INSERT IGNORE INTO students (auth_id, student_id, title, firstname, lastname, email) VALUES
(2, '6012345678', 'นาย', 'สมชาย', 'ใจดี', 'student1@example.com');

-- Insert sample activities
INSERT IGNORE INTO activites (activity_name, date, time) VALUES
('กิจกรรมจิตอาสา', '2024-01-15', '09:00:00'),
('กิจกรรมกีฬาสี', '2024-02-20', '08:00:00'),
('กิจกรรมวันไหว้ครู', '2024-03-10', '10:00:00');

-- Insert sample activity registrations
INSERT IGNORE INTO activity_details (student_id, activity_id) VALUES
('6012345678', 1),
('6012345678', 2);