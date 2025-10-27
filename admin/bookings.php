<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Check if user is logged in and is admin or shipper
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
if ($user['role'] !== 'admin' && $user['role'] !== 'shipper') {
    header('Location: ../index.php');
    exit;
}

$message = '';
$error = '';

// Handle action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $bookingId = (int)$_POST['booking_id'];
    $action = $_POST['action'];
    
    switch ($action) {
        case 'assign':
            if ($user['role'] === 'admin') {
                $shipperId = (int)$_POST['shipper_id'];
                assignBookingToShipper($bookingId, $shipperId);
                $message = 'Đã gán đơn hàng cho shipper thành công!';
            }
            break;
            
        case 'complete':
            $booking = getBookingById($bookingId);
            if ($booking) {
                $coinsEarned = calculateCoinsForBooking($booking);
                completeBooking($bookingId, $booking['user_id'], $coinsEarned);
                $message = 'Đã hoàn thành đơn hàng và cộng coins cho người dùng!';
            }
            break;
            
        case 'cancel':
            updateBookingStatus($bookingId, 'cancelled');
            $message = 'Đã hủy đơn hàng!';
            break;
            
        case 'update_status':
            $status = $_POST['status'];
            updateBookingStatus($bookingId, $status);
            $message = 'Đã cập nhật trạng thái đơn hàng!';
            break;
    }
}

// Calculate coins for booking
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

// Get bookings based on role
if ($user['role'] === 'admin') {
    $bookings = getAllBookings();
} else {
    $bookings = getAllBookings();
    // Filter for shipper's assigned bookings
    $bookings = array_filter($bookings, function($b) use ($user) {
        return $b['shipper_id'] == $user['id'] || $b['status'] === 'pending';
    });
}

// Get all shippers for admin
$shippers = [];
if ($user['role'] === 'admin') {
    $shippers = $db->fetchAll("SELECT * FROM users WHERE role = 'shipper' AND status = 'active'");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng - E-Waste Manager</title>
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
            <div class="navbar-nav ms-auto">
                <?php if ($user['role'] === 'admin'): ?>
                    <a class="nav-link" href="admin/dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a class="nav-link" href="index.php">Về trang chủ</a>
                <a class="nav-link" href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2>
                    <i class="fas fa-list-alt text-success me-2"></i>
                    Quản lý đơn hàng
                    <?php if ($user['role'] === 'shipper'): ?>
                        <span class="badge bg-info ms-2">Đối tác thu gom</span>
                    <?php endif; ?>
                </h2>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <?php
            $pendingCount = count(array_filter($bookings, fn($b) => $b['status'] === 'pending'));
            $activeCount = count(array_filter($bookings, fn($b) => in_array($b['status'], ['on_the_way', 'collected'])));
            $completedCount = count(array_filter($bookings, fn($b) => $b['status'] === 'completed'));
            ?>
            <div class="col-md-4">
                <div class="card border-left-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-warning">Chờ xử lý</h6>
                                <h3><?php echo $pendingCount; ?></h3>
                            </div>
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-info">Đang xử lý</h6>
                                <h3><?php echo $activeCount; ?></h3>
                            </div>
                            <i class="fas fa-sync fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-left-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-success">Hoàn thành</h6>
                                <h3><?php echo $completedCount; ?></h3>
                            </div>
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-check me-2"></i>Danh sách đơn hàng
                </h5>
            </div>
            <div class="card-body">
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
                            <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Không có đơn hàng nào</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>#<?php echo $booking['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($booking['user_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($booking['phone']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($booking['device_type']); ?></td>
                                <td><?php echo $booking['device_count']; ?></td>
                                <td>
                                    <small><?php echo htmlspecialchars(substr($booking['address'], 0, 50)); ?>...</small>
                                </td>
                                <td><?php echo formatDate($booking['created_at']); ?></td>
                                <td>
                                    <span class="badge <?php echo getStatusBadgeClass($booking['status']); ?>">
                                        <?php echo getStatusText($booking['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" 
                                                data-bs-target="#bookingModal<?php echo $booking['id']; ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($booking['status'] === 'pending'): ?>
                                            <?php if ($user['role'] === 'admin'): ?>
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal" 
                                                        data-bs-target="#assignModal<?php echo $booking['id']; ?>">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" 
                                                    data-bs-target="#cancelModal<?php echo $booking['id']; ?>">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php elseif (in_array($booking['status'], ['on_the_way', 'collected'])): ?>
                                            <button type="button" class="btn btn-success" data-bs-toggle="modal" 
                                                    data-bs-target="#completeModal<?php echo $booking['id']; ?>">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>

                            <!-- View Booking Modal -->
                            <div class="modal fade" id="bookingModal<?php echo $booking['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Chi tiết đơn hàng #<?php echo $booking['id']; ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6>Thông tin khách hàng</h6>
                                                    <p><strong>Tên:</strong> <?php echo htmlspecialchars($booking['user_name']); ?></p>
                                                    <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6>Thông tin đơn hàng</h6>
                                                    <p><strong>Loại thiết bị:</strong> <?php echo htmlspecialchars($booking['device_type']); ?></p>
                                                    <p><strong>Số lượng:</strong> <?php echo $booking['device_count']; ?></p>
                                                    <p><strong>Ngày đặt:</strong> <?php echo formatDate($booking['created_at']); ?></p>
                                                    <p><strong>Trạng thái:</strong> 
                                                        <span class="badge <?php echo getStatusBadgeClass($booking['status']); ?>">
                                                            <?php echo getStatusText($booking['status']); ?>
                                                        </span>
                                                    </p>
                                                </div>
                                            </div>
                                            <hr>
                                            <h6>Địa chỉ thu gom</h6>
                                            <p><?php echo htmlspecialchars($booking['address']); ?></p>
                                            <?php if ($booking['notes']): ?>
                                            <hr>
                                            <h6>Ghi chú</h6>
                                            <p><?php echo htmlspecialchars($booking['notes']); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Assign Modal (Admin only) -->
                            <?php if ($user['role'] === 'admin' && $booking['status'] === 'pending'): ?>
                            <div class="modal fade" id="assignModal<?php echo $booking['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Gán đơn hàng cho shipper</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="assign">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Chọn shipper</label>
                                                    <select name="shipper_id" class="form-select" required>
                                                        <option value="">Chọn shipper...</option>
                                                        <?php foreach ($shippers as $shipper): ?>
                                                        <option value="<?php echo $shipper['id']; ?>">
                                                            <?php echo htmlspecialchars($shipper['name']); ?> - <?php echo htmlspecialchars($shipper['phone']); ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-success">Gán đơn hàng</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Complete Modal -->
                            <?php if (in_array($booking['status'], ['on_the_way', 'collected'])): ?>
                            <div class="modal fade" id="completeModal<?php echo $booking['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hoàn thành đơn hàng</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="complete">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <p>Bạn có chắc muốn hoàn thành đơn hàng này?</p>
                                                <p><strong>Số coins sẽ được cộng:</strong> 
                                                    <span class="badge bg-warning text-dark">
                                                        <?php echo calculateCoinsForBooking($booking); ?> coins
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-success">Xác nhận hoàn thành</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Cancel Modal -->
                            <div class="modal fade" id="cancelModal<?php echo $booking['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hủy đơn hàng</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="cancel">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <p>Bạn có chắc muốn hủy đơn hàng này?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Không</button>
                                                <button type="submit" class="btn btn-danger">Có, hủy đơn hàng</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
