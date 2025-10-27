# E-Waste Management System

Hệ thống quản lý rác thải điện tử thông minh giúp người dân tìm điểm thu gom, đặt lịch thu gom tại nhà, tích điểm đổi thưởng và theo dõi quá trình tái chế một cách minh bạch.

## 🌟 Tính năng chính

### 👥 Người dân (User)
- **Tìm điểm thu gom**: Sử dụng GPS để tìm các điểm thu gom rác thải điện tử gần nhất
- **Đặt lịch thu gom tại nhà**: Đặt lịch để nhân viên đến thu gom thiết bị
- **Tích điểm đổi thưởng**: Nhận coins khi giao thiết bị và đổi lấy voucher, sản phẩm
- **Theo dõi minh bạch**: Xem hành trình xử lý thiết bị từ thu gom đến tái chế

### 🚚 Đơn vị thu gom / Shipper
- Nhận yêu cầu thu gom từ người dân
- Xác nhận và cập nhật trạng thái đơn hàng
- Quản lý lịch trình thu gom

### 👨‍💼 Admin / Cơ quan môi trường
- **Thống kê tổng quan**: Xem số liệu về lượng rác thải, người dùng, đơn hàng
- **Quản lý hệ thống**: Quản lý người dùng, điểm thu gom, phần thưởng
- **Báo cáo**: Xuất báo cáo cho UBND / Sở TNMT

## 🚀 Cài đặt

### Yêu cầu hệ thống
- PHP 7.4 trở lên
- MySQL 5.7 trở lên
- Web server (Apache/Nginx)
- Composer (tùy chọn)

### Các bước cài đặt

1. **Clone repository**
```bash
git clone https://github.com/your-repo/ewaste-management.git
cd ewaste-management
```

2. **Cấu hình database**
- Tạo database MySQL mới
- Import file `database.sql` để tạo cấu trúc database và dữ liệu mẫu
```sql
mysql -u root -p < database.sql
```

3. **Cấu hình kết nối database**
- Mở file `config/database.php`
- Cập nhật thông tin kết nối database:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ewaste_management');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

4. **Cấu hình web server**
- Đặt thư mục dự án vào thư mục web root
- Cấu hình virtual host (nếu cần)

5. **Truy cập ứng dụng**
- Mở trình duyệt và truy cập: `http://localhost/ewaste-management`
- Đăng nhập với tài khoản admin mặc định:
  - Email: `admin@ewaste.com`
  - Password: `password`

## 📱 Cách sử dụng

### Đăng ký tài khoản
1. Truy cập trang đăng ký
2. Điền đầy đủ thông tin cá nhân
3. Xác nhận email và đăng nhập

### Tìm điểm thu gom
1. Vào trang "Tìm điểm thu gom"
2. Cho phép truy cập vị trí GPS
3. Xem danh sách điểm thu gom gần nhất
4. Chọn điểm thu gom hoặc đặt lịch tại nhà

### Đặt lịch thu gom tại nhà
1. Vào trang "Đặt lịch thu gom"
2. Chọn loại thiết bị và số lượng
3. Nhập địa chỉ và thông tin liên hệ
4. Chọn ngày và khung giờ thu gom
5. Xác nhận đặt lịch

### Theo dõi thiết bị
1. Vào trang "Theo dõi thiết bị"
2. Chọn đơn hàng cần theo dõi
3. Xem hành trình xử lý thiết bị
4. Nhận coins khi hoàn thành

### Đổi thưởng
1. Vào trang "Đổi thưởng"
2. Xem danh sách phần thưởng có sẵn
3. Chọn phần thưởng phù hợp với số coins
4. Xác nhận đổi thưởng

## 🗂️ Cấu trúc dự án

```
ewaste-management/
├── admin/
│   └── dashboard.php          # Dashboard admin
├── assets/
│   ├── css/
│   │   └── style.css          # CSS tùy chỉnh
│   └── js/
│       └── main.js            # JavaScript tùy chỉnh
├── config/
│   └── database.php           # Cấu hình database
├── includes/
│   └── functions.php          # Các hàm tiện ích
├── index.php                  # Trang chủ
├── login.php                  # Trang đăng nhập
├── register.php               # Trang đăng ký
├── logout.php                 # Đăng xuất
├── map.php                    # Trang bản đồ GPS
├── booking.php                # Trang đặt lịch thu gom
├── tracking.php               # Trang theo dõi thiết bị
├── rewards.php                # Trang đổi thưởng
├── database.sql               # File SQL tạo database
└── README.md                  # File hướng dẫn
```

## 🗄️ Cấu trúc Database

### Bảng chính
- **users**: Thông tin người dùng, shipper, admin
- **collection_points**: Điểm thu gom rác thải điện tử
- **device_types**: Loại thiết bị và giá trị coins
- **bookings**: Đơn hàng thu gom
- **device_tracking**: Theo dõi vòng đời thiết bị
- **rewards**: Phần thưởng có thể đổi
- **reward_redemptions**: Lịch sử đổi thưởng

## 🎨 Giao diện

- **Responsive Design**: Tương thích với mọi thiết bị
- **Bootstrap 5**: Framework CSS hiện đại
- **Font Awesome**: Icon đẹp mắt
- **Leaflet**: Bản đồ tương tác
- **Chart.js**: Biểu đồ thống kê

## 🔧 Tùy chỉnh

### Thêm loại thiết bị mới
1. Cập nhật bảng `device_types` trong database
2. Cập nhật mảng `$deviceTypes` trong các file PHP
3. Thêm icon và màu sắc phù hợp

### Thêm điểm thu gom
1. Vào trang admin
2. Thêm điểm thu gom mới với tọa độ GPS
3. Cập nhật thông tin liên hệ và giờ hoạt động

### Tùy chỉnh phần thưởng
1. Cập nhật bảng `rewards` trong database
2. Thêm loại phần thưởng mới nếu cần
3. Cập nhật giao diện trang đổi thưởng

## 🚀 Triển khai Production

### Cấu hình bảo mật
1. Thay đổi mật khẩu admin mặc định
2. Cấu hình HTTPS
3. Thiết lập firewall
4. Backup database định kỳ

### Tối ưu hiệu suất
1. Bật caching cho PHP
2. Tối ưu database indexes
3. Sử dụng CDN cho static files
4. Cấu hình gzip compression

## 🤝 Đóng góp

1. Fork repository
2. Tạo feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## 📄 License

Dự án này được phân phối dưới MIT License. Xem file `LICENSE` để biết thêm chi tiết.

## 📞 Liên hệ

- **Email**: admin@ewaste.com
- **Website**: https://ewaste-management.com
- **GitHub**: https://github.com/your-repo/ewaste-management

## 🙏 Cảm ơn

Cảm ơn tất cả những người đã đóng góp vào dự án này!

---

**Lưu ý**: Đây là phiên bản demo. Để triển khai thực tế, cần thêm các tính năng bảo mật, xử lý lỗi và tối ưu hiệu suất.
