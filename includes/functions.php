<?php
require_once 'config/database.php';

// User functions
function getUserById($userId) {
    global $db;
    return $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
}

function getUserByEmail($email) {
    global $db;
    return $db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
}

function createUser($name, $email, $password, $phone, $address) {
    global $db;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, password, phone, address, coins, role, created_at) VALUES (?, ?, ?, ?, ?, 0, 'user', NOW())";
    $db->query($sql, [$name, $email, $hashedPassword, $phone, $address]);
    return $db->lastInsertId();
}

function authenticateUser($email, $password) {
    $user = getUserByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

// Collection points functions
function getCollectionPoints($lat = null, $lng = null, $radius = 10) {
    global $db;
    if ($lat && $lng) {
        $sql = "SELECT *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
                FROM collection_points 
                WHERE status = 'active'
                HAVING distance < ?
                ORDER BY distance";
        return $db->fetchAll($sql, [$lat, $lng, $lat, $radius]);
    }
    return $db->fetchAll("SELECT * FROM collection_points WHERE status = 'active'");
}

function getCollectionPointById($id) {
    global $db;
    return $db->fetch("SELECT * FROM collection_points WHERE id = ?", [$id]);
}

// Booking functions
function createBooking($userId, $deviceType, $deviceCount, $address, $phone, $notes, $preferredDate) {
    global $db;
    $sql = "INSERT INTO bookings (user_id, device_type, device_count, address, phone, notes, preferred_date, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
    $db->query($sql, [$userId, $deviceType, $deviceCount, $address, $phone, $notes, $preferredDate]);
    return $db->lastInsertId();
}

function getUserBookings($userId) {
    global $db;
    return $db->fetchAll("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
}

function getBookingById($id) {
    global $db;
    return $db->fetch("SELECT * FROM bookings WHERE id = ?", [$id]);
}

function updateBookingStatus($id, $status) {
    global $db;
    $db->query("UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?", [$status, $id]);
}

function getAllBookings($status = null) {
    global $db;
    $sql = "SELECT b.*, u.name as user_name, u.phone as user_phone 
            FROM bookings b 
            JOIN users u ON b.user_id = u.id";
    $params = [];
    
    if ($status) {
        $sql .= " WHERE b.status = ?";
        $params[] = $status;
    }
    
    $sql .= " ORDER BY b.created_at DESC";
    return $db->fetchAll($sql, $params);
}

function assignBookingToShipper($bookingId, $shipperId) {
    global $db;
    $db->query("UPDATE bookings SET shipper_id = ?, status = 'on_the_way', updated_at = NOW() WHERE id = ?", [$shipperId, $bookingId]);
}

function completeBooking($bookingId, $userId, $coinsEarned) {
    global $db;
    $db->query("UPDATE bookings SET status = 'completed', coins_earned = ?, updated_at = NOW() WHERE id = ?", [$coinsEarned, $bookingId]);
    addCoinsToUser($userId, $coinsEarned);
}

// Device tracking functions
function createDeviceTracking($bookingId, $deviceType, $deviceCount) {
    global $db;
    $sql = "INSERT INTO device_tracking (booking_id, device_type, device_count, status, created_at) 
            VALUES (?, ?, ?, 'collected', NOW())";
    $db->query($sql, [$bookingId, $deviceType, $deviceCount]);
    return $db->lastInsertId();
}

function getDeviceTracking($bookingId) {
    global $db;
    return $db->fetchAll("SELECT * FROM device_tracking WHERE booking_id = ? ORDER BY created_at ASC", [$bookingId]);
}

function updateDeviceTrackingStatus($id, $status) {
    global $db;
    $db->query("UPDATE device_tracking SET status = ?, updated_at = NOW() WHERE id = ?", [$status, $id]);
}

// Coin functions
function addCoinsToUser($userId, $coins) {
    global $db;
    $db->query("UPDATE users SET coins = coins + ? WHERE id = ?", [$coins, $userId]);
}

function getUserCoins($userId) {
    global $db;
    $user = $db->fetch("SELECT coins FROM users WHERE id = ?", [$userId]);
    return $user ? $user['coins'] : 0;
}

// Rewards functions
function getRewards() {
    global $db;
    return $db->fetchAll("SELECT * FROM rewards WHERE status = 'active' ORDER BY coins_required ASC");
}

function getRewardById($id) {
    global $db;
    return $db->fetch("SELECT * FROM rewards WHERE id = ?", [$id]);
}

function redeemReward($userId, $rewardId) {
    global $db;
    $reward = getRewardById($rewardId);
    $user = getUserById($userId);
    
    if (!$reward || !$user) {
        return false;
    }
    
    if ($user['coins'] < $reward['coins_required']) {
        return false;
    }
    
    // Deduct coins
    $db->query("UPDATE users SET coins = coins - ? WHERE id = ?", [$reward['coins_required'], $userId]);
    
    // Create redemption record
    $sql = "INSERT INTO reward_redemptions (user_id, reward_id, coins_used, status, created_at) 
            VALUES (?, ?, ?, 'pending', NOW())";
    $db->query($sql, [$userId, $rewardId, $reward['coins_required']]);
    
    return $db->lastInsertId();
}

// Admin functions
function getAdminStats() {
    global $db;
    $stats = [];
    
    // Total users
    $stats['total_users'] = $db->fetch("SELECT COUNT(*) as count FROM users WHERE role = 'user'")['count'];
    
    // Total bookings
    $stats['total_bookings'] = $db->fetch("SELECT COUNT(*) as count FROM bookings")['count'];
    
    // Total collection points
    $stats['total_collection_points'] = $db->fetch("SELECT COUNT(*) as count FROM collection_points WHERE status = 'active'")['count'];
    
    // Total coins distributed
    $stats['total_coins'] = $db->fetch("SELECT SUM(coins) as total FROM users")['total'] ?? 0;
    
    // Recent bookings
    $stats['recent_bookings'] = $db->fetchAll("
        SELECT b.*, u.name as user_name 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.created_at DESC 
        LIMIT 10
    ");
    
    return $stats;
}

function getDeviceTypeStats() {
    global $db;
    return $db->fetchAll("
        SELECT device_type, COUNT(*) as count, SUM(device_count) as total_devices
        FROM bookings 
        WHERE status IN ('completed', 'collected')
        GROUP BY device_type
        ORDER BY count DESC
    ");
}

// Utility functions
function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function formatCoins($coins) {
    return number_format($coins) . ' coins';
}

function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'bg-warning',
        'on_the_way' => 'bg-info',
        'collected' => 'bg-primary',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger'
    ];
    return $classes[$status] ?? 'bg-secondary';
}

function getStatusText($status) {
    $texts = [
        'pending' => 'Chờ xử lý',
        'on_the_way' => 'Đang đến',
        'collected' => 'Đã thu gom',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy'
    ];
    return $texts[$status] ?? $status;
}
?>
