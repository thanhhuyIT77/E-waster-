<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if user is logged in and is shipper
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
if ($user['role'] !== 'shipper') {
    header('Location: ../index.php');
    exit;
}

$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = (int)$_POST['booking_id'];
    $action = $_POST['action'];
    
    switch ($action) {
        case 'accept':
            updateBookingStatus($bookingId, 'on_the_way');
            $message = 'Đã nhận đơn hàng!';
            break;
            
        case 'collected':
            updateBookingStatus($bookingId, 'collected');
            $message = 'Đã thu gom thành công!';
            break;
            
        case 'complete':
            $booking = getBookingById($bookingId);
            if ($booking) {
                $coinsEarned = calculateCoinsForBooking($booking);
                completeBooking($bookingId, $booking['user_id'], $coinsEarned);
                $message = 'Đã hoàn thành đơn hàng!';
            }
            break;
    }
}

// Calculate coins helper
function calculateCoinsForBooking($booking) {
    $deviceTypes = [
        'Điện thoại' => 10,
        'Laptop' => 20,
        'Pin' => 2,
        'TV' => 15,
        'Máy tính bàn' => 25,
        'Máy in' => 8
    ];
    
    $coinsPerDevice = $deviceTypes[$booking['device_type']] ?? 0;
    return $coinsPerDevice * $booking['device_count'];
}

// Get bookings assigned to this shipper
$allBookings = getAllBookings();
$assignedBookings = array_filter($allBookings, function($b) use ($user) {
    return $b['shipper_id'] == $user['id'];
});

$pendingBookings = array_filter($allBookings, function($b) {
    return $b['status'] === 'pending' && !$b['shipper_id'];
});
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipper Dashboard - E-Waste Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-info">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-truck me-2"></i>E-Waste Shipper
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="shipper/dashboard.php">Dashboard</a>
                <a class="nav-link" href="index.php">Về trang chủ</a>
                <a class="nav-link" href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2>Xin chào, <?php echo htmlspecialchars($user['name']); ?>!</h2>
                                <p class="mb-0">Bạn đang đăng nhập với vai trò: Đối tác thu gom</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <i class="fas fa-truck fa-3x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-left-warning">
                    <div class="card-body">
                        <h6 class="text-warning">Đơn chờ</h6>
                        <h3><?php echo count($pendingBookings); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-info">
                    <div class="card-body">
                        <h6 class="text-info">Đang xử lý</h6>
                        <h3><?php echo count(array_filter($assignedBookings, fn($b) => in_array($b['status'], ['on_the_way', 'collected']))); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-success">
                    <div class="card-body">
                        <h6 class="text-success">Hoàn thành</h6>
                        <h3><?php echo count(array_filter($assignedBookings, fn($b) => $b['status'] === 'completed')); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-left-primary">
                    <div class="card-body">
                        <h6 class="text-primary">Tổng đơn</h6>
                        <h3><?php echo count($assignedBookings); ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Bookings -->
        <?php if (!empty($pendingBookings)): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Đơn hàng chờ nhận
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php foreach (array_slice($pendingBookings, 0, 4) as $booking): ?>
                    <div class="col-md-6">
                        <div class="card border-left-warning h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6>#<?php echo $booking['id']; ?> - <?php echo htmlspecialchars($booking['device_type']); ?></h6>
                                    <span class="badge bg-warning"><?php echo $booking['device_count']; ?> thiết bị</span>
                                </div>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($booking['user_name']); ?>
                                </p>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars(substr($booking['address'], 0, 50)); ?>...
                                </p>
                                <p class="text-muted mb-3">
                                    <i class="fas fa-calendar me-1"></i><?php echo formatDate($booking['created_at']); ?>
                                </p>
                                <form method="POST" class="d-grid">
                                    <input type="hidden" name="action" value="accept">
                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-hand-paper me-1"></i>Nhận đơn này
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- My Bookings -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Đơn hàng của tôi
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($assignedBookings)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Bạn chưa có đơn hàng nào</p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Khách hàng</th>
                                <th>Loại thiết bị</th>
                                <th>Số lượng</th>
                                <th>Địa chỉ</th>
                                <th>Ngày đặt</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assignedBookings as $booking): ?>
                            <tr>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($booking['phone']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['device_type']); ?></td>
                                <td><?php echo $booking['device_count']; ?></td>
                                <td>
                                    <small><?php echo htmlspecialchars(substr($booking['address'], 0, 30)); ?>...</small>
                                </td>
                                <td><?php echo formatDate($booking['created_at']); ?></td>
                                <td>
                                    <span class="badge <?php echo getStatusBadgeClass($booking['status']); ?>">
                                        <?php echo getStatusText($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] === 'on_the_way'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="collected">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check me-1"></i>Đã thu gom
                                        </button>
                                    </form>
                                    <?php elseif ($booking['status'] === 'collected'): ?>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="complete">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check-double me-1"></i>Hoàn thành
                                        </button>
                                    </form>
                                    <?php elseif ($booking['status'] === 'completed'): ?>
                                    <span class="badge bg-success">Hoàn thành</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
