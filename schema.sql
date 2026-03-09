CREATE DATABASE Facility_Equipment;
USE Facility_Equipment;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE Facility_Reservation (
    facility_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    facility_name VARCHAR(25) NOT NULL,
    reserve TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) 
    REFERENCES users(user_id)
    ON DELETE SET NULL
);

CREATE TABLE equipment(
    equipment_id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,git
    equipment_name VARCHAR(25) NOT NULL,
    reserve TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) 
    REFERENCES users(user_id)
    ON DELETE CASCADE
    
);

CREATE TABLE user_logs (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) 
    REFERENCES users(user_id)
    ON DELETE SET NULL
);