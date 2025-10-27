<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Kiểm tra đăng nhập
$isLoggedIn = isset($_SESSION['user_id']);
$user = null;
if ($isLoggedIn && isset($_SESSION['user_id'])) {
    $user = getUserById($_SESSION['user_id']);
    if (!$user) {
        $isLoggedIn = false;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Waste Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-recycle me-2"></i>E-Waste Manager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Trang chủ</a>
                    </li>
                    
                    <?php if ($isLoggedIn && $user && $user['role'] === 'admin'): ?>
                    <!-- Admin Menu -->
                    <li class="nav-item">
                        <a class="nav-link" href="admin/dashboard.php">
                            <i class="fas fa-chart-line me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/bookings.php">
                            <i class="fas fa-calendar-check me-1"></i>Đơn hàng
                        </a>
                    </li>
                    <?php elseif ($isLoggedIn && $user && $user['role'] === 'shipper'): ?>
                    <!-- Shipper Menu -->
                    <li class="nav-item">
                        <a class="nav-link" href="shipper/dashboard.php">
                            <i class="fas fa-truck me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/bookings.php">
                            <i class="fas fa-list me-1"></i>Đơn hàng
                        </a>
                    </li>
                    <?php else: ?>
                    <!-- User Menu -->
                    <li class="nav-item">
                        <a class="nav-link" href="map.php">Tìm điểm thu gom</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">Đặt lịch thu gom</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rewards.php">Đổi thưởng</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn && $user): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($user['name'] ?? 'User'); ?>
                                <span class="badge bg-warning text-dark ms-1"><?php echo $user['coins'] ?? 0; ?> coins</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php">Hồ sơ</a></li>
                                <li><a class="dropdown-item" href="tracking.php">Theo dõi thiết bị</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Đăng ký</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section bg-gradient-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <?php if ($isLoggedIn && $user && $user['role'] === 'admin'): ?>
                        <h1 class="display-4 fw-bold mb-4">Dashboard Quản trị viên</h1>
                        <p class="lead mb-4">Quản lý toàn bộ hệ thống, xem thống kê, gán đơn hàng và quản lý người dùng.</p>
                        <div class="d-flex gap-3">
                            <a href="admin/dashboard.php" class="btn btn-light btn-lg">
                                <i class="fas fa-chart-line me-2"></i>Xem Dashboard
                            </a>
                            <a href="admin/bookings.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-calendar-check me-2"></i>Quản lý đơn hàng
                            </a>
                        </div>
                    <?php elseif ($isLoggedIn && $user && $user['role'] === 'shipper'): ?>
                        <h1 class="display-4 fw-bold mb-4">Dashboard Đối tác thu gom</h1>
                        <p class="lead mb-4">Nhận và xử lý đơn hàng thu gom, cập nhật trạng thái và quản lý lịch trình của bạn.</p>
                        <div class="d-flex gap-3">
                            <a href="shipper/dashboard.php" class="btn btn-light btn-lg">
                                <i class="fas fa-truck me-2"></i>Xem Dashboard
                            </a>
                            <a href="admin/bookings.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-list me-2"></i>Xem đơn hàng
                            </a>
                        </div>
                    <?php else: ?>
                        <h1 class="display-4 fw-bold mb-4">Quản lý rác thải điện tử thông minh</h1>
                        <p class="lead mb-4">Tìm điểm thu gom, đặt lịch tại nhà, tích điểm đổi thưởng và theo dõi quá trình tái chế một cách minh bạch.</p>
                        <div class="d-flex gap-3">
                            <a href="map.php" class="btn btn-light btn-lg">
                                <i class="fas fa-map-marker-alt me-2"></i>Tìm điểm thu gom
                            </a>
                            <a href="booking.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-calendar-plus me-2"></i>Đặt lịch thu gom
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <?php if ($isLoggedIn && $user && $user['role'] === 'admin'): ?>
                            <i class="fas fa-crown display-1 opacity-75"></i>
                        <?php elseif ($isLoggedIn && $user && $user['role'] === 'shipper'): ?>
                            <i class="fas fa-truck display-1 opacity-75"></i>
                        <?php else: ?>
                            <i class="fas fa-recycle display-1 opacity-75"></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Tính năng chính</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon bg-primary text-white rounded-circle mx-auto mb-3">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <h5 class="card-title">Tìm điểm thu gom</h5>
                            <p class="card-text">Sử dụng GPS để tìm các điểm thu gom rác thải điện tử gần nhất.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon bg-success text-white rounded-circle mx-auto mb-3">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h5 class="card-title">Thu gom tại nhà</h5>
                            <p class="card-text">Đặt lịch để nhân viên đến thu gom rác thải điện tử tại nhà.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon bg-warning text-white rounded-circle mx-auto mb-3">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h5 class="card-title">Tích điểm đổi thưởng</h5>
                            <p class="card-text">Nhận coin khi giao thiết bị và đổi lấy voucher, sản phẩm.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <div class="feature-icon bg-info text-white rounded-circle mx-auto mb-3">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h5 class="card-title">Theo dõi minh bạch</h5>
                            <p class="card-text">Xem hành trình xử lý thiết bị từ thu gom đến tái chế.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Device Types Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Các loại thiết bị được thu gom</h2>
            <div class="row g-4">
                <?php
                $deviceTypes = [
                    ['name' => 'Điện thoại', 'icon' => 'fas fa-mobile-alt', 'coins' => 10, 'color' => 'primary'],
                    ['name' => 'Laptop', 'icon' => 'fas fa-laptop', 'coins' => 20, 'color' => 'success'],
                    ['name' => 'Pin', 'icon' => 'fas fa-battery-half', 'coins' => 2, 'color' => 'warning'],
                    ['name' => 'TV', 'icon' => 'fas fa-tv', 'coins' => 15, 'color' => 'info'],
                    ['name' => 'Máy tính bàn', 'icon' => 'fas fa-desktop', 'coins' => 25, 'color' => 'secondary'],
                    ['name' => 'Máy in', 'icon' => 'fas fa-print', 'coins' => 8, 'color' => 'dark']
                ];
                ?>
                <?php foreach ($deviceTypes as $device): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="device-icon bg-<?php echo $device['color']; ?> text-white rounded-circle mx-auto mb-3">
                                <i class="<?php echo $device['icon']; ?>"></i>
                            </div>
                            <h5 class="card-title"><?php echo $device['name']; ?></h5>
                            <p class="card-text">
                                <span class="badge bg-warning text-dark fs-6"><?php echo $device['coins']; ?> coins</span>
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>E-Waste Management System</h5>
                    <p class="mb-0">Hệ thống quản lý rác thải điện tử thông minh và minh bạch.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2024 E-Waste Manager. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
