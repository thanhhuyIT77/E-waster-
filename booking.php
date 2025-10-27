<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
$message = '';
$error = '';

// Get collection point if specified
$selectedPoint = null;
if (isset($_GET['point_id'])) {
    $selectedPoint = getCollectionPointById($_GET['point_id']);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deviceType = $_POST['device_type'] ?? '';
    $deviceCount = (int)($_POST['device_count'] ?? 0);
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $preferredDate = $_POST['preferred_date'] ?? '';
    $preferredTime = $_POST['preferred_time'] ?? '';
    $collectionPointId = $_POST['collection_point_id'] ?? null;
    
    // Validation
    if (empty($deviceType) || $deviceCount <= 0 || empty($address) || empty($phone) || empty($preferredDate)) {
        $error = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
    } else {
        try {
            $bookingId = createBooking(
                $user['id'],
                $deviceType,
                $deviceCount,
                $address,
                $phone,
                $notes,
                $preferredDate
            );
            
            // Create device tracking record
            createDeviceTracking($bookingId, $deviceType, $deviceCount);
            
            $message = 'Đặt lịch thu gom thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.';
            
            // Redirect to tracking page
            header("Location: tracking.php?booking_id=$bookingId");
            exit;
        } catch (Exception $e) {
            $error = 'Có lỗi xảy ra. Vui lòng thử lại.';
        }
    }
}

// Get device types and their coin values
$deviceTypes = [
    'Điện thoại' => 10,
    'Laptop' => 20,
    'Pin' => 2,
    'TV' => 15,
    'Máy tính bàn' => 25,
    'Máy in' => 8
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lịch thu gom - E-Waste Manager</title>
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
                        <a class="nav-link" href="index.php">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="map.php">Tìm điểm thu gom</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="booking.php">Đặt lịch thu gom</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rewards.php">Đổi thưởng</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($user['name']); ?>
                            <span class="badge bg-warning text-dark ms-1"><?php echo $user['coins']; ?> coins</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Hồ sơ</a></li>
                            <li><a class="dropdown-item" href="tracking.php">Theo dõi thiết bị</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Booking Form -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-calendar-plus me-2"></i>Đặt lịch thu gom rác thải điện tử
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="bookingForm">
                            <!-- Device Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-success mb-3">
                                        <i class="fas fa-mobile-alt me-2"></i>Thông tin thiết bị
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <label for="device_type" class="form-label">Loại thiết bị <span class="text-danger">*</span></label>
                                    <select class="form-select" id="device_type" name="device_type" required>
                                        <option value="">Chọn loại thiết bị</option>
                                        <?php foreach ($deviceTypes as $type => $coins): ?>
                                        <option value="<?php echo $type; ?>" data-coins="<?php echo $coins; ?>">
                                            <?php echo $type; ?> (<?php echo $coins; ?> coins/thiết bị)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="device_count" class="form-label">Số lượng <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="device_count" name="device_count" 
                                           min="1" max="100" required>
                                </div>
                            </div>
                            
                            <!-- Coin Preview -->
                            <div class="alert alert-info" id="coinPreview" style="display: none;">
                                <i class="fas fa-coins me-2"></i>
                                <strong>Bạn sẽ nhận được: <span id="totalCoins">0</span> coins</strong>
                            </div>
                            
                            <!-- Collection Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-success mb-3">
                                        <i class="fas fa-map-marker-alt me-2"></i>Thông tin thu gom
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <label for="address" class="form-label">Địa chỉ thu gom <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="address" name="address" rows="3" 
                                              placeholder="Nhập địa chỉ chi tiết..." required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <!-- Preferred Time -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="preferred_date" class="form-label">Ngày thu gom mong muốn <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="preferred_date" name="preferred_date" 
                                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="preferred_time" class="form-label">Khung giờ thu gom</label>
                                    <select class="form-select" id="preferred_time" name="preferred_time">
                                        <option value="">Chọn khung giờ</option>
                                        <option value="08:00-10:00">Sáng (8:00 - 10:00)</option>
                                        <option value="10:00-12:00">Trưa (10:00 - 12:00)</option>
                                        <option value="14:00-16:00">Chiều (14:00 - 16:00)</option>
                                        <option value="16:00-18:00">Tối (16:00 - 18:00)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Collection Point -->
                            <?php if ($selectedPoint): ?>
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-map-marker-alt me-2"></i>Điểm thu gom đã chọn:</h6>
                                        <p class="mb-1"><strong><?php echo htmlspecialchars($selectedPoint['name']); ?></strong></p>
                                        <p class="mb-0 small text-muted"><?php echo htmlspecialchars($selectedPoint['address']); ?></p>
                                        <input type="hidden" name="collection_point_id" value="<?php echo $selectedPoint['id']; ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Additional Notes -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label for="notes" class="form-label">Ghi chú thêm</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3" 
                                              placeholder="Mô tả thêm về thiết bị hoặc yêu cầu đặc biệt..."></textarea>
                                </div>
                            </div>
                            
                            <!-- Terms and Conditions -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="terms" required>
                                        <label class="form-check-label" for="terms">
                                            Tôi đồng ý với <a href="#" class="text-success">điều khoản sử dụng</a> và cam kết thiết bị đã được xóa dữ liệu cá nhân
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="map.php" class="btn btn-outline-secondary me-md-2">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-calendar-check me-2"></i>Đặt lịch thu gom
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Information Card -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Thông tin quan trọng
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Nhân viên sẽ liên hệ xác nhận trước khi đến thu gom
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Vui lòng chuẩn bị thiết bị và đảm bảo đã xóa dữ liệu cá nhân
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Bạn sẽ nhận được coins sau khi thiết bị được thu gom thành công
                            </li>
                            <li class="mb-0">
                                <i class="fas fa-check text-success me-2"></i>
                                Có thể theo dõi quá trình xử lý thiết bị trên hệ thống
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calculate coins preview
        function updateCoinPreview() {
            const deviceType = document.getElementById('device_type');
            const deviceCount = document.getElementById('device_count');
            const coinPreview = document.getElementById('coinPreview');
            const totalCoins = document.getElementById('totalCoins');
            
            if (deviceType.value && deviceCount.value) {
                const coinsPerDevice = parseInt(deviceType.options[deviceType.selectedIndex].getAttribute('data-coins'));
                const count = parseInt(deviceCount.value);
                const total = coinsPerDevice * count;
                
                totalCoins.textContent = total;
                coinPreview.style.display = 'block';
            } else {
                coinPreview.style.display = 'none';
            }
        }
        
        // Event listeners
        document.getElementById('device_type').addEventListener('change', updateCoinPreview);
        document.getElementById('device_count').addEventListener('input', updateCoinPreview);
        
        // Set minimum date to tomorrow
        document.getElementById('preferred_date').min = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    </script>
</body>
</html>
