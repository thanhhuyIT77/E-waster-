-- Script để thêm bảng chi tiết cho shipper và admin
-- Chạy: mysql -u root -p --port=3307 ewaste_management < add_detail_tables.sql

-- Shipper details table (bảng chi tiết cho đơn vị thu gom)
CREATE TABLE IF NOT EXISTS shipper_details (
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
CREATE TABLE IF NOT EXISTS admin_details (
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

-- Insert dữ liệu chi tiết cho các tài khoản đã có
INSERT INTO admin_details (user_id, department, position, office_address, notes) 
SELECT u.id, 'Sở Tài nguyên và Môi trường', 'Trưởng phòng', 'Số 72 Nguyễn Trãi, Quận 1, TP.HCM', 'Cơ quan quản lý môi trường thành phố'
FROM users u 
WHERE u.email = 'admin@gmail.com' AND u.role = 'admin'
AND NOT EXISTS (SELECT 1 FROM admin_details ad WHERE ad.user_id = u.id);

INSERT INTO shipper_details (user_id, company_name, license_number, vehicle_info, area_covered, notes)
SELECT u.id, 'Công ty Môi trường Xanh', 'GP123456', 'Xe tải 5 tấn - 29A-12345', 'Toàn thành phố', 'Đơn vị thu gom rác thải điện tử chuyên nghiệp'
FROM users u 
WHERE u.email = 'shipper@gmail.com' AND u.role = 'shipper'
AND NOT EXISTS (SELECT 1 FROM shipper_details sd WHERE sd.user_id = u.id);

SELECT '✓ Bảng chi tiết đã được tạo và dữ liệu đã được thêm!';
