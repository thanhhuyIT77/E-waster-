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
$bookings = getUserBookings($user['id']);

// Get specific booking if requested
$selectedBooking = null;
if (isset($_GET['booking_id'])) {
    $selectedBooking = getBookingById($_GET['booking_id']);
    if (!$selectedBooking || $selectedBooking['user_id'] != $user['id']) {
        $selectedBooking = null;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theo dõi thiết bị - E-Waste Manager</title>
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
                        <a class="nav-link" href="booking.php">Đặt lịch thu gom</a>
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
                            <li><a class="dropdown-item active" href="tracking.php">Theo dõi thiết bị</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Đăng xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Tracking Section -->
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-eye text-success me-2"></i>Theo dõi thiết bị
                </h2>
            </div>
        </div>
        
        <div class="row">
            <!-- Bookings List -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Danh sách đơn hàng
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($bookings)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Chưa có đơn hàng nào</p>
                            <a href="booking.php" class="btn btn-success">Đặt lịch thu gom</a>
                        </div>
                        <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($bookings as $booking): ?>
                            <a href="tracking.php?booking_id=<?php echo $booking['id']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $selectedBooking && $selectedBooking['id'] == $booking['id'] ? 'active' : ''; ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($booking['device_type']); ?></h6>
                                    <small><?php echo formatDate($booking['created_at']); ?></small>
                                </div>
                                <p class="mb-1"><?php echo $booking['device_count']; ?> thiết bị</p>
                                <span class="badge <?php echo getStatusBadgeClass($booking['status']); ?>">
                                    <?php echo getStatusText($booking['status']); ?>
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Tracking Details -->
            <div class="col-lg-8">
                <?php if ($selectedBooking): ?>
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Chi tiết đơn hàng #<?php echo $selectedBooking['id']; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Booking Info -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-success">Thông tin thiết bị</h6>
                                <p><strong>Loại:</strong> <?php echo htmlspecialchars($selectedBooking['device_type']); ?></p>
                                <p><strong>Số lượng:</strong> <?php echo $selectedBooking['device_count']; ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($selectedBooking['address']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Thông tin đơn hàng</h6>
                                <p><strong>Ngày đặt:</strong> <?php echo formatDate($selectedBooking['created_at']); ?></p>
                                <p><strong>Ngày thu gom:</strong> <?php echo $selectedBooking['preferred_date'] ? date('d/m/Y', strtotime($selectedBooking['preferred_date'])) : 'Chưa xác định'; ?></p>
                                <p><strong>Trạng thái:</strong> 
                                    <span class="badge <?php echo getStatusBadgeClass($selectedBooking['status']); ?>">
                                        <?php echo getStatusText($selectedBooking['status']); ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Tracking Timeline -->
                        <h6 class="text-success mb-3">Hành trình xử lý</h6>
                        <div class="timeline">
                            <?php
                            $trackingSteps = [
                                'collected' => ['icon' => 'fas fa-hand-holding', 'title' => 'Đã thu gom', 'description' => 'Thiết bị đã được thu gom thành công'],
                                'sorted' => ['icon' => 'fas fa-sort', 'title' => 'Phân loại', 'description' => 'Thiết bị đang được phân loại và kiểm tra'],
                                'transported' => ['icon' => 'fas fa-truck', 'title' => 'Vận chuyển', 'description' => 'Thiết bị đang được vận chuyển đến cơ sở tái chế'],
                                'recycled' => ['icon' => 'fas fa-recycle', 'title' => 'Tái chế', 'description' => 'Thiết bị đang được xử lý và tái chế'],
                                'completed' => ['icon' => 'fas fa-check-circle', 'title' => 'Hoàn thành', 'description' => 'Quá trình tái chế đã hoàn thành']
                            ];
                            
                            $currentStatus = $selectedBooking['status'];
                            $stepOrder = ['collected', 'sorted', 'transported', 'recycled', 'completed'];
                            $currentStepIndex = array_search($currentStatus, $stepOrder);
                            
                            foreach ($stepOrder as $index => $step): 
                                $isCompleted = $index <= $currentStepIndex;
                                $isCurrent = $index == $currentStepIndex;
                            ?>
                            <div class="timeline-item <?php echo $isCompleted ? 'completed' : ''; ?> <?php echo $isCurrent ? 'current' : ''; ?>">
                                <div class="timeline-marker">
                                    <i class="<?php echo $trackingSteps[$step]['icon']; ?>"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="timeline-title"><?php echo $trackingSteps[$step]['title']; ?></h6>
                                    <p class="timeline-description"><?php echo $trackingSteps[$step]['description']; ?></p>
                                    <?php if ($isCompleted): ?>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php echo $selectedBooking['updated_at'] ? formatDate($selectedBooking['updated_at']) : 'Đang cập nhật...'; ?>
                                    </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Coins Earned -->
                        <?php if ($selectedBooking['coins_earned'] > 0): ?>
                        <div class="alert alert-success mt-4">
                            <i class="fas fa-coins me-2"></i>
                            <strong>Bạn đã nhận được <?php echo $selectedBooking['coins_earned']; ?> coins!</strong>
                            <p class="mb-0 mt-2">Sử dụng coins để đổi thưởng tại <a href="rewards.php" class="alert-link">trang đổi thưởng</a></p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Notes -->
                        <?php if ($selectedBooking['notes']): ?>
                        <div class="mt-4">
                            <h6 class="text-success">Ghi chú</h6>
                            <p class="text-muted"><?php echo htmlspecialchars($selectedBooking['notes']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Chọn một đơn hàng để xem chi tiết</h5>
                        <p class="text-muted">Click vào đơn hàng bên trái để theo dõi hành trình xử lý</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-item.completed .timeline-marker {
    background: #28a745;
    color: white;
}

.timeline-item.current .timeline-marker {
    background: #007bff;
    color: white;
    animation: pulse 2s infinite;
}

.timeline-item:not(.completed):not(.current) .timeline-marker {
    background: #e9ecef;
    color: #6c757d;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}

.timeline-content {
    padding-left: 20px;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-description {
    margin-bottom: 5px;
    color: #6c757d;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>
