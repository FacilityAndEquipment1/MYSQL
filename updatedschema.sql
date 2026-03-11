CREATE DATABASE IF NOT EXISTS ict_reservation_db;
USE ict_reservation_db;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL
);

INSERT INTO users (username, password, role) VALUES 
('admin', 'admin123', 'admin'),
('student', 'user123', 'user');

CREATE TABLE reserve_facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    facility_name VARCHAR(100) NOT NULL,
    reserve_date DATETIME NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE reserve_equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    equipment_name VARCHAR(100) NOT NULL,
    borrow_date DATETIME NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);