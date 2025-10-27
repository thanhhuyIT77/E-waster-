# E-Waste Management System - Hệ thống Quản lý Rác Thải Điện Tử

## 🎯 Tổng quan

Hệ thống quản lý rác thải điện tử thông minh giúp người dân tìm điểm thu gom, đặt lịch thu gom tại nhà, tích điểm đổi thưởng và theo dõi quá trình tái chế một cách minh bạch.

## 👥 Các vai trò trong hệ thống

### 1. **Người dân (User)**
- Tìm điểm thu gom gần nhất qua GPS
- Đặt lịch thu gom rác thải điện tử tại nhà
- Tích điểm nhận coins
- Đổi thưởng bằng coins
- Theo dõi quá trình tái chế thiết bị

### 2. **Đơn vị thu gom / Shipper**
- Nhận đơn hàng được admin gán
- Xem danh sách đơn hàng chờ nhận
- Cập nhật trạng thái đơn hàng
- Xác nhận đã thu gom
- Hoàn thành đơn hàng và cộng coins cho khách

### 3. **Admin / Cơ quan môi trường**
- Quản lý toàn bộ hệ thống
- Xem thống kê tổng quan
- Gán đơn hàng cho shipper
- Quản lý người dùng, điểm thu gom
- Quản lý phần thưởng
- Theo dõi và xử lý đơn hàng

## 🚀 Hướng dẫn cài đặt

### Yêu cầu
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)
- Port database: **3307** (đã cấu hình sẵn)

### Các bước

1. **Import database**
```bash
mysql -u root -p --port=3307 < database.sql
```

2. **Cấu hình database** (đã cấu hình sẵn port 3307)
- File: `config/database.php`
- Username: `root`
- Database: `ewaste_management`

3. **Chạy website**
- Truy cập: `http://localhost/ewaste-management`

## 🔐 Tài khoản mặc định

### Admin
- Email: `admin@ewaste.com`
- Password: `password`
- Truy cập: `http://localhost/ewaste-management/admin/dashboard.php`

### Shipper 1
- Email: `shipper1@ewaste.com`
- Password: `password`
- Truy cập: `http://localhost/ewaste-management/shipper/dashboard.php`

### Shipper 2
- Email: `shipper2@ewaste.com`
- Password: `password`

### User (Đăng ký mới)
- Đăng ký tại: `http://localhost/ewaste-management/register.php`

## 📋 Luồng hoạt động

### 1. User đặt lịch thu gom
```
User → Đặt lịch → Nhập thông tin → Xác nhận → Trạng thái: Pending
```

### 2. Admin gán cho Shipper
```
Admin → Xem đơn hàng → Chọn shipper → Gán → Trạng thái: On the way
```

### 3. Shipper thu gom
```
Shipper → Nhận đơn → Đi thu gom → Xác nhận thu gom → Trạng thái: Collected
```

### 4. Shipper hoàn thành
```
Shipper → Hoàn thành → Hệ thống tự động cộng coins → Trạng thái: Completed
```

### 5. User nhận coins và theo dõi
```
User → Xem coins → Theo dõi thiết bị → Đổi thưởng
```

## 🗺️ Sơ đồ phân quyền

```
┌─────────────────────────────────────────┐
│           ADMIN (Toàn quyền)             │
│  - Quản lý user                          │
│  - Gán đơn cho shipper                   │
│  - Xem thống kê                          │
│  - Quản lý điểm thu gom                  │
└──────────────┬──────────────────────────┘
               │
               ├──► SHIPPER (Thu gom)
               │    - Xem đơn được gán
               │    - Cập nhật trạng thái
               │    - Hoàn thành đơn hàng
               │
               └──► USER (Người dùng)
                    - Đặt lịch
                    - Theo dõi thiết bị
                    - Đổi thưởng
```

## 🎨 Giao diện

- **Bootstrap 5**: Responsive, hiện đại
- **Font Awesome**: Icons đẹp mắt
- **Leaflet Maps**: Bản đồ GPS tương tác
- **Chart.js**: Biểu đồ thống kê

## 📱 Các trang chính

### User
- `index.php` - Trang chủ
- `map.php` - Tìm điểm thu gom
- `booking.php` - Đặt lịch
- `tracking.php` - Theo dõi thiết bị
- `rewards.php` - Đổi thưởng

### Shipper
- `shipper/dashboard.php` - Dashboard shipper
- `admin/bookings.php` - Quản lý đơn hàng

### Admin
- `admin/dashboard.php` - Dashboard admin
- `admin/bookings.php` - Quản lý đơn hàng
- `admin/users.php` - Quản lý người dùng
- `admin/collection_points.php` - Điểm thu gom

## 🔧 Chức năng nổi bật

### 1. Bản đồ GPS
- Tự động định vị
- Tìm điểm thu gom gần nhất
- Tính khoảng cách
- Hiển thị thông tin chi tiết

### 2. Hệ thống tích điểm
- Mỗi loại thiết bị có giá trị coins khác nhau
- Tự động cộng coins khi hoàn thành
- Theo dõi lịch sử

### 3. Timeline theo dõi
- Thu gom → Phân loại → Vận chuyển → Tái chế → Hoàn thành
- Minh bạch, cập nhật real-time

### 4. Dashboard Admin
- Thống kê tổng quan
- Biểu đồ phân tích
- Quản lý toàn hệ thống

### 5. Phân quyền chi tiết
- User: Chỉ xem và đặt đơn
- Shipper: Xem đơn được gán và xử lý
- Admin: Toàn quyền quản lý

## 📊 Database Schema

### Tables
- `users` - Người dùng (user/shipper/admin)
- `collection_points` - Điểm thu gom
- `bookings` - Đơn hàng
- `device_tracking` - Theo dõi thiết bị
- `rewards` - Phần thưởng
- `reward_redemptions` - Lịch sử đổi thưởng

## 🛠️ Tùy chỉnh

### Thêm loại thiết bị mới
```php
// Cập nhật trong functions.php
$deviceTypes = [
    'Điện thoại' => 10,
    'Laptop' => 20,
    'Pin' => 2,
    // Thêm thiết bị mới...
];
```

### Thêm phần thưởng
```sql
INSERT INTO rewards (name, description, coins_required, reward_type, value, status) 
VALUES ('Tên thưởng', 'Mô tả', 100, 'voucher', '100.000đ', 'active');
```

## 📞 Hỗ trợ

- Email: admin@ewaste.com
- GitHub: [Link to repo]

## 📄 License

MIT License - Tự do sử dụng và phát triển

---

**Lưu ý**: Đã cấu hình sẵn port **3307** cho database connection.
