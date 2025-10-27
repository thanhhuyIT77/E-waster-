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

// Handle reward redemption
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['redeem_reward'])) {
    $rewardId = (int)($_POST['reward_id'] ?? 0);
    
    if ($rewardId > 0) {
        $result = redeemReward($user['id'], $rewardId);
        if ($result) {
            $message = 'Đổi thưởng thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.';
            // Refresh user data
            $user = getUserById($_SESSION['user_id']);
        } else {
            $error = 'Không đủ coins để đổi thưởng này.';
        }
    }
}

$rewards = getRewards();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi thưởng - E-Waste Manager</title>
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
                        <a class="nav-link active" href="rewards.php">Đổi thưởng</a>
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

    <!-- Rewards Section -->
    <div class="container py-5">
        <!-- User Coins Display -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body text-center">
                        <h2 class="mb-3">
                            <i class="fas fa-coins me-2"></i>Số dư coins của bạn
                        </h2>
                        <div class="display-4 fw-bold text-warning"><?php echo number_format($user['coins']); ?></div>
                        <p class="mb-0">Sử dụng coins để đổi thưởng hấp dẫn</p>
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
        
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <!-- Rewards Grid -->
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="fas fa-gift text-success me-2"></i>Danh sách phần thưởng
                </h3>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach ($rewards as $reward): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex flex-column">
                        <div class="text-center mb-3">
                            <div class="reward-icon bg-<?php echo $reward['reward_type'] === 'voucher' ? 'primary' : ($reward['reward_type'] === 'product' ? 'success' : 'info'); ?> text-white rounded-circle mx-auto mb-3">
                                <?php if ($reward['reward_type'] === 'voucher'): ?>
                                    <i class="fas fa-ticket-alt"></i>
                                <?php elseif ($reward['reward_type'] === 'product'): ?>
                                    <i class="fas fa-gift"></i>
                                <?php else: ?>
                                    <i class="fas fa-heart"></i>
                                <?php endif; ?>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($reward['name']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($reward['description']); ?></p>
                        </div>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge bg-warning text-dark fs-6">
                                    <i class="fas fa-coins me-1"></i><?php echo $reward['coins_required']; ?> coins
                                </span>
                                <span class="text-success fw-bold"><?php echo htmlspecialchars($reward['value']); ?></span>
                            </div>
                            
                            <?php if ($user['coins'] >= $reward['coins_required']): ?>
                            <form method="POST" class="d-grid">
                                <input type="hidden" name="reward_id" value="<?php echo $reward['id']; ?>">
                                <button type="submit" name="redeem_reward" class="btn btn-success">
                                    <i class="fas fa-exchange-alt me-2"></i>Đổi ngay
                                </button>
                            </form>
                            <?php else: ?>
                            <button class="btn btn-outline-secondary w-100" disabled>
                                <i class="fas fa-lock me-2"></i>Cần thêm <?php echo $reward['coins_required'] - $user['coins']; ?> coins
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- How to Earn Coins -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-lightbulb me-2"></i>Cách kiếm coins
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">Thu gom thiết bị điện tử</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-mobile-alt text-primary me-2"></i>Điện thoại: 10 coins/thiết bị</li>
                                    <li><i class="fas fa-laptop text-success me-2"></i>Laptop: 20 coins/thiết bị</li>
                                    <li><i class="fas fa-battery-half text-warning me-2"></i>Pin: 2 coins/thiết bị</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Thiết bị khác</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-tv text-info me-2"></i>TV: 15 coins/thiết bị</li>
                                    <li><i class="fas fa-desktop text-secondary me-2"></i>Máy tính bàn: 25 coins/thiết bị</li>
                                    <li><i class="fas fa-print text-dark me-2"></i>Máy in: 8 coins/thiết bị</li>
                                </ul>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <a href="booking.php" class="btn btn-success">
                                <i class="fas fa-calendar-plus me-2"></i>Đặt lịch thu gom ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Environmental Impact -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h5 class="text-success mb-3">
                            <i class="fas fa-leaf me-2"></i>Tác động tích cực đến môi trường
                        </h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="fas fa-recycle fa-2x text-success mb-2"></i>
                                    <h6>Tái chế</h6>
                                    <p class="small text-muted">Giảm rác thải điện tử</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="fas fa-leaf fa-2x text-success mb-2"></i>
                                    <h6>Bảo vệ môi trường</h6>
                                    <p class="small text-muted">Ngăn chặn ô nhiễm</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="fas fa-seedling fa-2x text-success mb-2"></i>
                                    <h6>Tài nguyên</h6>
                                    <p class="small text-muted">Tiết kiệm nguyên liệu</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <i class="fas fa-heart fa-2x text-success mb-2"></i>
                                    <h6>Cộng đồng</h6>
                                    <p class="small text-muted">Xây dựng tương lai xanh</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
.reward-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #28a745, #20c997);
}
</style>
