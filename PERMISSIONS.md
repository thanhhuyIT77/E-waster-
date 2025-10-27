# Hệ thống Phân quyền - E-Waste Management System

## 📋 Tổng quan

Hệ thống có 3 vai trò chính với các quyền (permissions) riêng biệt:

## 👤 Vai trò: Người dùng (User)

### Quyền cơ bản:
- ✅ **Xem bản đồ điểm thu gom** (`view_map`)
- ✅ **Đặt lịch thu gom tại nhà** (`create_booking`)
- ✅ **Xem lịch sử đơn/thống kê của riêng mình** (`view_own_orders`)
- ✅ **Đổi điểm thưởng** (`redeem_rewards`)
- ✅ **Xem tiến trình tái chế thiết bị** (`track_devices`)

### Chức năng:
1. Tìm điểm thu gom gần nhất qua GPS
2. Đặt lịch thu gom rác thải điện tử tại nhà
3. Theo dõi đơn hàng của mình
4. Nhận coins sau khi hoàn thành
5. Đổi coins lấy phần thưởng
6. Xem timeline xử lý thiết bị

### Trang truy cập:
- `index.php` - Trang chủ
- `map.php` - Bản đồ
- `booking.php` - Đặt lịch
- `tracking.php` - Theo dõi
- `rewards.php` - Đổi thưởng

---

## 🚚 Vai trò: Đối tác/Đơn vị thu gom (Shipper)

### Quyền cơ bản:
- ✅ **Xem bản đồ điểm thu gom** (`view_map`)
- ✅ **Xem đơn chờ thu gom** (`view_pending_orders`)
- ✅ **Nhận đơn hàng** (`accept_orders`)
- ✅ **Cập nhật trạng thái đơn** (`update_order_status`)
- ✅ **Xem quản lý lịch thu gom** (`view_shipper_orders`)
- ✅ **Xem đơn của riêng mình** (`view_own_orders`)
- ✅ **Theo dõi thiết bị** (`track_devices`)

### Không có quyền:
- ❌ Quản lý người dùng
- ❌ Xem thống kê tổng thể
- ❌ Thay đổi cấu hình hệ thống
- ❌ Gán đơn cho shipper khác

### Chức năng:
1. Xem đơn hàng chờ nhận (pending)
2. Nhận đơn hàng được admin gán
3. Cập nhật trạng thái: On the way → Collected → Completed
4. Hoàn thành đơn và cộng coins cho khách
5. Xem dashboard cá nhân với thống kê

### Trang truy cập:
- `shipper/dashboard.php` - Dashboard Shipper
- `admin/bookings.php` - Quản lý đơn hàng (chỉ xem được phân công)

---

## 👑 Vai trò: Admin / Cơ quan môi trường (Admin)

### Quyền toàn hệ thống:
- ✅ **Tất cả quyền của User**
- ✅ **Tất cả quyền của Shipper**
- ✅ **Quản lý người dùng** (`manage_users`)
- ✅ **Quản lý đối tác** (`manage_shippers`)
- ✅ **Quản lý điểm thu gom** (`manage_collection_points`)
- ✅ **Xem toàn bộ đơn hàng** (`view_all_orders`)
- ✅ **Gán đơn cho shipper** (`assign_orders`)
- ✅ **Xem báo cáo hệ thống** (`view_system_reports`)
- ✅ **Xuất báo cáo** (`export_reports`)
- ✅ **Quản lý cấu hình** (`manage_system_config`)

### Chức năng:
1. Quản lý toàn bộ người dùng, shipper
2. Gán đơn hàng cho shipper
3. Xem thống kê tổng quan hệ thống
4. Quản lý điểm thu gom
5. Xuất báo cáo cho UBND/Sở TNMT
6. Cấu hình tỷ lệ coins, loại thiết bị
7. Quản lý phần thưởng

### Trang truy cập:
- `admin/dashboard.php` - Dashboard Admin
- `admin/users.php` - Quản lý người dùng
- `admin/bookings.php` - Quản lý đơn hàng
- `admin/collection_points.php` - Quản lý điểm thu gom
- `admin/rewards.php` - Quản lý phần thưởng

---

## 🔐 Luồng Phân quyền

### User Flow:
```
Đăng nhập → Trang chủ
  ├─ Xem bản đồ
  ├─ Đặt lịch thu gom
  ├─ Theo dõi thiết bị
  └─ Đổi thưởng
```

### Shipper Flow:
```
Đăng nhập → Shipper Dashboard
  ├─ Xem đơn chờ nhận
  ├─ Nhận đơn hàng
  ├─ Cập nhật trạng thái
  └─ Hoàn thành đơn
```

### Admin Flow:
```
Đăng nhập → Admin Dashboard
  ├─ Quản lý người dùng
  ├─ Quản lý đơn hàng
  ├─ Gán đơn cho shipper
  ├─ Xem thống kê
  ├─ Quản lý điểm thu gom
  └─ Xuất báo cáo
```

---

## 📊 Bảng So sánh Quyền

| Chức năng | User | Shipper | Admin |
|-----------|------|---------|-------|
| Xem bản đồ | ✅ | ✅ | ✅ |
| Đặt lịch | ✅ | ❌ | ✅ |
| Xem đơn của mình | ✅ | ✅ | ✅ |
| Nhận đơn hàng | ❌ | ✅ | ✅ |
| Gán đơn cho shipper | ❌ | ❌ | ✅ |
| Quản lý users | ❌ | ❌ | ✅ |
| Xem thống kê tổng | ❌ | ❌ | ✅ |
| Xuất báo cáo | ❌ | ❌ | ✅ |
| Đổi thưởng | ✅ | ❌ | ✅ |
| Quản lý cấu hình | ❌ | ❌ | ✅ |

---

## 💻 Cách sử dụng Permissions

### Trong PHP:
```php
<?php
require_once 'includes/permissions.php';

// Check permission
if (hasPermission(PERMISSION_VIEW_MAP)) {
    // Show map
}

// Require permission (redirect if not)
requirePermission(PERMISSION_MANAGE_USERS, 'index.php');

// Check role
if (isAdmin()) {
    // Admin only code
}
?>
```

### Trong HTML:
```php
<?php if (hasPermission(PERMISSION_CREATE_BOOKING)): ?>
    <a href="booking.php">Đặt lịch</a>
<?php endif; ?>
```

---

## 🔑 Tài khoản Test

### Admin
- Email: `admin@ewaste.com`
- Password: `password`
- URL: `/admin/dashboard.php`

### Shipper 1
- Email: `shipper1@ewaste.com`
- Password: `password`
- URL: `/shipper/dashboard.php`

### Shipper 2
- Email: `shipper2@ewaste.com`
- Password: `password`
- URL: `/shipper/dashboard.php`

### User
- Đăng ký mới tại: `/register.php`

---

## 📝 Ghi chú

- Tất cả vai trò đều có quyền truy cập trang chủ `index.php`
- Shipper không thể tự đăng ký, phải do Admin tạo
- Admin có toàn quyền, có thể thực hiện tất cả chức năng
- Mỗi vai trò có dashboard riêng
