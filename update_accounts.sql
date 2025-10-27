-- Script để cập nhật tài khoản vào database
-- Chạy lệnh: mysql -u root -p --port=3307 ewaste_management < update_accounts.sql

-- Xóa các tài khoản cũ nếu có
DELETE FROM users WHERE email IN ('admin@gmail.com', 'shipper@gmail.com');

-- Insert admin user (password: 123456)
INSERT INTO users (name, email, password, role, phone, address) VALUES 
('Cơ quan Môi trường', 'admin@gmail.com', '$2y$10$nhKj3G7zjnJ.Q4Ffmf/Q/OoxFR49YtNiHEjzGMQ9iX2UYwJrWX8Dy', 'admin', '0987654321', 'TP.HCM');

-- Insert shipper user (password: 123456)  
INSERT INTO users (name, email, password, phone, address, role) VALUES 
('Đơn vị thu gom A', 'shipper@gmail.com', '$2y$10$nhKj3G7zjnJ.Q4Ffmf/Q/OoxFR49YtNiHEjzGMQ9iX2UYwJrWX8Dy', '0901234567', 'TP.HCM', 'shipper');

-- Hoặc nếu muốn update password cho tài khoản đã có:
-- UPDATE users SET password = '$2y$10$nhKj3G7zjnJ.Q4Ffmf/Q/OoxFR49YtNiHEjzGMQ9iX2UYwJrWX8Dy' WHERE email = 'admin@gmail.com';
-- UPDATE users SET password = '$2y$10$nhKj3G7zjnJ.Q4Ffmf/Q/OoxFR49YtNiHEjzGMQ9iX2UYwJrWX8Dy' WHERE email = 'shipper@gmail.com';
