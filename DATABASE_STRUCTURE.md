# Cấu trúc Database - E-Waste Management System

## 📊 Tổng quan

Database có các bảng chính với thông tin chi tiết cho từng vai trò.

## 🗂️ Các bảng trong database

### 1. **users** - Bảng người dùng cơ bản
Chứa thông tin cơ bản của tất cả người dùng (User, Shipper, Admin)

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| id | INT | ID người dùng |
| name | VARCHAR(100) | Tên người dùng |
| email | VARCHAR(100) | Email (unique) |
| password | VARCHAR(255) | Mật khẩu (hash) |
| phone | VARCHAR(20) | Số điện thoại |
| address | TEXT | Địa chỉ |
| coins | INT | Số coins (default: 0) |
| role | ENUM | Vai trò: user/shipper/admin |
| status | ENUM | Trạng thái: active/inactive |
| created_at | TIMESTAMP | Ngày tạo |
| updated_at | TIMESTAMP | Ngày cập nhật |

### 2. **shipper_details** - Chi tiết đơn vị thu gom
Chứa thông tin chi tiết của đơn vị thu gom (Shipper)

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| id | INT | ID chi tiết |
| user_id | INT | ID người dùng (FK users) |
| company_name | VARCHAR(200) | Tên công ty |
| license_number | VARCHAR(100) | Số giấy phép |
| vehicle_info | VARCHAR(200) | Thông tin xe |
| area_covered | VARCHAR(200) | Khu vực hoạt động |
| notes | TEXT | Ghi chú |
| created_at | TIMESTAMP | Ngày tạo |
| updated_at | TIMESTAMP | Ngày cập nhật |

### 3. **admin_details** - Chi tiết cơ quan quản lý
Chứa thông tin chi tiết của Admin/Cơ quan môi trường

| Cột | Kiểu | Mô tả |
|-----|------|-------|
| id | INT | ID chi tiết |
| user_id | INT | ID người dùng (FK users) |
| department | VARCHAR(200) | Phòng ban |
| position | VARCHAR(100) | Chức vụ |
| office_address | TEXT | Địa chỉ văn phòng |
| notes | TEXT | Ghi chú |
| created_at | TIMESTAMP | Ngày tạo |
| updated_at | TIMESTAMP | Ngày cập nhật |

### 4. **Các bảng khác**
- `collection_points` - Điểm thu gom
- `bookings` - Đơn hàng
- `device_tracking` - Theo dõi thiết bị
- `device_types` - Loại thiết bị
- `rewards` - Phần thưởng
- `reward_redemptions` - Lịch sử đổi thưởng

## 🔑 Tài khoản mẫu

### Admin:
- **admin@gmail.com** / 123456
- **admin2@gmail.com** / 123456

### Shipper:
- **shipper@gmail.com** / 123456
- **shipper2@gmail.com** / 123456

### User:
- Đăng ký mới tại trang register

## 📝 Cách thêm Admin/Shipper mới

### Qua phpMyAdmin hoặc MySQL:
```sql
-- Thêm Admin mới
INSERT INTO users (name, email, password, role, phone, address) VALUES 
('Tên Admin', 'email@example.com', '$2y$10$...', 'admin', '0123456789', 'Địa chỉ');

-- Thêm admin_details
INSERT INTO admin_details (user_id, department, position, office_address, notes) VALUES 
(LAST_INSERT_ID(), 'Tên phòng ban', 'Chức vụ', 'Địa chỉ văn phòng', 'Ghi chú');

-- Thêm Shipper mới
INSERT INTO users (name, email, password, phone, address, role) VALUES 
('Tên Shipper', 'email@example.com', '$2y$10$...', '0123456789', 'Địa chỉ', 'shipper');

-- Thêm shipper_details
INSERT INTO shipper_details (user_id, company_name, license_number, vehicle_info, area_covered, notes) VALUES 
(LAST_INSERT_ID(), 'Tên công ty', 'Số GP', 'Thông tin xe', 'Khu vực', 'Ghi chú');
```

### Tạo password hash:
```bash
php -r "echo password_hash('123456', PASSWORD_DEFAULT);"
```

## 🔄 Cập nhật Database

### Tạo lại toàn bộ:
```bash
mysql -u root -p --port=3307 < database.sql
```

### Thêm bảng chi tiết (nếu đã có database cũ):
```bash
mysql -u root -p --port=3307 ewaste_management < add_detail_tables.sql
```

### Chạy setup tài khoản:
```bash
php setup_accounts.php
```

## ✅ Kiểm tra database

```sql
-- Xem tất cả users
SELECT * FROM users WHERE role IN ('admin', 'shipper');

-- Xem chi tiết shipper
SELECT u.*, sd.* 
FROM users u 
LEFT JOIN shipper_details sd ON u.id = sd.user_id 
WHERE u.role = 'shipper';

-- Xem chi tiết admin
SELECT u.*, ad.* 
FROM users u 
LEFT JOIN admin_details ad ON u.id = ad.user_id 
WHERE u.role = 'admin';

-- Thống kê theo vai trò
SELECT role, COUNT(*) as count 
FROM users 
GROUP BY role;
```
