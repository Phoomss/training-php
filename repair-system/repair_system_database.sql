-- Database: repair-system
-- This SQL file creates the database schema for the repair system

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `repair-system` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `repair-system`;

-- Table for user authentication
CREATE TABLE auth (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student', 'technical') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for student profiles
CREATE TABLE student (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(10) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    student_id VARCHAR(50) NOT NULL UNIQUE,
    auth_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auth_id) REFERENCES auth(id) ON DELETE CASCADE
);

-- Table for technical staff profiles
CREATE TABLE technical (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(10) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    auth_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auth_id) REFERENCES auth(id) ON DELETE CASCADE
);

-- Table for equipment
CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for repair requests
CREATE TABLE repair (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    equipment_id INT NOT NULL,
    details TEXT NOT NULL,
    image VARCHAR(255),
    status ENUM('pending', 'in_progress', 'completed', 'rejected') DEFAULT 'pending',
    technical_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES student(id) ON DELETE RESTRICT,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE RESTRICT,
    FOREIGN KEY (technical_id) REFERENCES technical(id) ON DELETE SET NULL
);

-- Table for repair process details
CREATE TABLE repair_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    repair_id INT NOT NULL,
    technical_id INT NOT NULL,
    status VARCHAR(50) NOT NULL, -- Values: "รอซ่อม", "กำลังซ่อม", "เสร็จสิ้น"
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (repair_id) REFERENCES repair(id) ON DELETE CASCADE,
    FOREIGN KEY (technical_id) REFERENCES technical(id) ON DELETE RESTRICT
);

-- Indexes for better performance
CREATE INDEX idx_repair_status ON repair(status);
CREATE INDEX idx_repair_student ON repair(student_id);
CREATE INDEX idx_repair_equipment ON repair(equipment_id);
CREATE INDEX idx_repair_technical ON repair(technical_id);
CREATE INDEX idx_repair_detail_repair ON repair_detail(repair_id);
CREATE INDEX idx_repair_detail_technical ON repair_detail(technical_id);

-- Insert sample data

-- Insert sample users into auth table
INSERT INTO auth (username, password, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'), -- password: password
('student1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'), -- password: password
('student2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'), -- password: password
('tech1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technical'), -- password: password
('tech2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technical'); -- password: password

-- Insert sample students
INSERT INTO student (title, firstname, lastname, student_id, auth_id) VALUES
('นาย', 'สมชาย', 'ใจดี', '63010001', 2),
('นางสาว', 'สมหญิง', 'งามสง่า', '63010002', 3);

-- Insert sample technical staff
INSERT INTO technical (title, firstname, lastname, phone, auth_id) VALUES
('นาย', 'ช่างห่วย', 'ซ่อมเก่ง', '0812345678', 4),
('นาง', 'ช่างเก่ง', 'ซ่อมไว', '0823456789', 5);

-- Insert sample equipment
INSERT INTO equipment (name) VALUES
('โปรเจคเตอร์'),
('เครื่องคอมพิวเตอร์'),
('เครื่องพิมพ์'),
('เครื่องเสียง'),
('ลำโพง'),
('ไมค์'),
('กล้องวงจรปิด'),
('เครื่องปรับอากาศ'),
('เครื่องทำน้ำอุ่น'),
('กล้องถ่ายรูป');

-- Insert sample repair requests
INSERT INTO repair (student_id, equipment_id, details, image, status, technical_id) VALUES
(1, 1, 'โปรเจคเตอร์ไม่ติด ไฟไม่เขียว', 'upload/repair/proj1.jpg', 'pending', NULL),
(1, 3, 'เครื่องพิมพ์พิมพ์ไม่ออก สีจาง', 'upload/repair/printer1.jpg', 'in_progress', 1),
(2, 2, 'เครื่องคอมพิวเตอร์ค้างตลอดเวลา', 'upload/repair/pc1.jpg', 'completed', 2),
(2, 4, 'เครื่องเสียงไม่มีเสียงตอนเช้า', 'upload/repair/sound1.jpg', 'pending', NULL);

-- Insert sample repair details
INSERT INTO repair_detail (repair_id, technical_id, status) VALUES
(2, 1, 'รอซ่อม'),
(2, 1, 'กำลังซ่อม'),
(3, 2, 'รอซ่อม'),
(3, 2, 'กำลังซ่อม'),
(3, 2, 'เสร็จสิ้น');