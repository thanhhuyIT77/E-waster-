-- E-Waste Management System Database Schema

CREATE DATABASE IF NOT EXISTS ewaste_management;
USE ewaste_management;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    coins INT DEFAULT 0,
    role ENUM('user', 'shipper', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Shipper details table (bảng chi tiết cho đơn vị thu gom)
CREATE TABLE shipper_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_name VARCHAR(200),
    license_number VARCHAR(100),
    vehicle_info VARCHAR(200),
    area_covered VARCHAR(200),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Admin details table (bảng chi tiết cho cơ quan quản lý)
CREATE TABLE admin_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    department VARCHAR(200),
    position VARCHAR(100),
    office_address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Collection points table
CREATE TABLE collection_points (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    operating_hours VARCHAR(100),
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Device types table
CREATE TABLE device_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    coins_per_device INT NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    device_type VARCHAR(50) NOT NULL,
    device_count INT NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    notes TEXT,
    preferred_date DATE,
    preferred_time TIME,
    status ENUM('pending', 'on_the_way', 'collected', 'completed', 'cancelled') DEFAULT 'pending',
    shipper_id INT NULL,
    collection_point_id INT NULL,
    coins_earned INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (shipper_id) REFERENCES users(id),
    FOREIGN KEY (collection_point_id) REFERENCES collection_points(id)
);

-- Device tracking table
CREATE TABLE device_tracking (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    device_type VARCHAR(50) NOT NULL,
    device_count INT NOT NULL,
    status ENUM('collected', 'sorted', 'transported', 'recycled', 'completed') DEFAULT 'collected',
    location VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Rewards table
CREATE TABLE rewards (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    coins_required INT NOT NULL,
    reward_type ENUM('voucher', 'product', 'donation') NOT NULL,
    value VARCHAR(100),
    image_url VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reward redemptions table
CREATE TABLE reward_redemptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    reward_id INT NOT NULL,
    coins_used INT NOT NULL,
    status ENUM('pending', 'approved', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (reward_id) REFERENCES rewards(id)
);

-- Insert sample data

-- Insert admin user (password: 123456)
INSERT INTO users (name, email, password, role, phone, address) VALUES 
('Cơ quan Môi trường', 'admin@gmail.com', '$2y$10$nhKj3G7zjnJ.Q4Ffmf/Q/OoxFR49YtNiHEjzGMQ9iX2UYwJrWX8Dy', 'admin', '0987654321', 'TP.HCM');

-- Insert admin details
INSERT INTO admin_details (user_id, department, position, office_address, notes) VALUES 
(LAST_INSERT_ID(), 'Sở Tài nguyên và Môi trường', 'Trưởng phòng', 'Số 72 Nguyễn Trãi, Quận 1, TP.HCM', 'Cơ quan quản lý môi trường thành phố');

-- Insert shipper users (password: 123456)
INSERT INTO users (name, email, password, phone, address, role) VALUES 
('Đơn vị thu gom A', 'shipper@gmail.com', '$2y$10$nhKj3G7zjnJ.Q4Ffmf/Q/OoxFR49YtNiHEjzGMQ9iX2UYwJrWX8Dy', '0901234567', 'TP.HCM', 'shipper');

-- Insert shipper details
INSERT INTO shipper_details (user_id, company_name, license_number, vehicle_info, area_covered, notes) VALUES 
(LAST_INSERT_ID(), 'Công ty Môi trường Xanh', 'GP123456', 'Xe tải 5 tấn - 29A-12345', 'Toàn thành phố', 'Đơn vị thu gom rác thải điện tử chuyên nghiệp');

-- Insert thêm shipper và admin mẫu (có thể thêm nhiều)
-- Shipper 2
INSERT INTO users (name, email, password, phone, address, role) VALUES 
('Công ty Tái chế ABC', 'shipper2@gmail.com', '$2y$10$nhKj3G7zjnJ.Q4Ffmf/Q/OoxFR49YtNiHEjzGMQ9iX2UYwJrWX8Dy', '0912345678', 'TP.HCM', 'shipper');
INSERT INTO shipper_details (user_id, company_name, license_number, vehicle_info, area_covered, notes) VALUES 
(LAST_INSERT_ID(), 'Công ty Tái chế ABC', 'GP789012', 'Xe container - 51B-67890', 'Quận 1, 3, 7', 'Chuyên thu gom và tái chế rác thải điện tử');

-- Admin 2
INSERT INTO users (name, email, password, role, phone, address) VALUES 
('Phó Giám đốc', 'admin2@gmail.com', '$2y$10$nhKj3G7zjnJ.Q4Ffmf/Q/OoxFR49YtNiHEjzGMQ9iX2UYwJrWX8Dy', '0923456789', 'TP.HCM', 'admin');
INSERT INTO admin_details (user_id, department, position, office_address, notes) VALUES 
(LAST_INSERT_ID(), 'Sở Tài nguyên và Môi trường', 'Phó Giám đốc', 'Số 72 Nguyễn Trãi, Quận 1, TP.HCM', 'Phụ trách quản lý rác thải điện tử');

-- Insert sample collection points
INSERT INTO collection_points (name, address, latitude, longitude, phone, operating_hours, description) VALUES
('Điểm thu gom Quận 1', '123 Nguyễn Huệ, Quận 1, TP.HCM', 10.7769, 106.7009, '028-1234567', '8:00-17:00', 'Điểm thu gom rác thải điện tử tại trung tâm thành phố'),
('Điểm thu gom Quận 3', '456 Võ Văn Tần, Quận 3, TP.HCM', 10.7829, 106.6927, '028-2345678', '8:00-17:00', 'Điểm thu gom gần các trường học'),
('Điểm thu gom Quận 7', '789 Nguyễn Thị Thập, Quận 7, TP.HCM', 10.7374, 106.7224, '028-3456789', '8:00-17:00', 'Điểm thu gom tại khu dân cư mới'),
('Điểm thu gom Quận Bình Thạnh', '321 Xô Viết Nghệ Tĩnh, Quận Bình Thạnh, TP.HCM', 10.8103, 106.7091, '028-4567890', '8:00-17:00', 'Điểm thu gom tại khu công nghiệp');

-- Insert device types
INSERT INTO device_types (name, coins_per_device, description) VALUES
('Điện thoại', 10, 'Smartphone, điện thoại cũ'),
('Laptop', 20, 'Máy tính xách tay'),
('Pin', 2, 'Pin điện thoại, pin laptop'),
('TV', 15, 'Tivi các loại'),
('Máy tính bàn', 25, 'Desktop computer'),
('Máy in', 8, 'Máy in, máy photocopy');

-- Insert sample rewards
INSERT INTO rewards (name, description, coins_required, reward_type, value, status) VALUES
('Voucher Shopee 50k', 'Voucher mua sắm Shopee trị giá 50.000đ', 100, 'voucher', '50.000đ', 'active'),
('Voucher Grab 30k', 'Voucher giao hàng Grab trị giá 30.000đ', 60, 'voucher', '30.000đ', 'active'),
('Cây xanh', 'Tặng 1 cây xanh để trồng', 20, 'product', '1 cây', 'active'),
('Quyên góp môi trường', 'Quyên góp cho tổ chức bảo vệ môi trường', 50, 'donation', '50.000đ', 'active'),
('Voucher VinMart 100k', 'Voucher mua sắm VinMart trị giá 100.000đ', 200, 'voucher', '100.000đ', 'active');
