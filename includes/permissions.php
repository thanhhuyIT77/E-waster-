<?php
// Permissions for each role

// User Permissions
define('PERMISSION_VIEW_MAP', 'view_map');
define('PERMISSION_CREATE_BOOKING', 'create_booking');
define('PERMISSION_VIEW_OWN_ORDERS', 'view_own_orders');
define('PERMISSION_REDEEM_REWARDS', 'redeem_rewards');
define('PERMISSION_TRACK_DEVICES', 'track_devices');

// Shipper Permissions
define('PERMISSION_VIEW_PENDING_ORDERS', 'view_pending_orders');
define('PERMISSION_ACCEPT_ORDERS', 'accept_orders');
define('PERMISSION_UPDATE_ORDER_STATUS', 'update_order_status');
define('PERMISSION_VIEW_SHIPPER_ORDERS', 'view_shipper_orders');

// Admin Permissions
define('PERMISSION_MANAGE_USERS', 'manage_users');
define('PERMISSION_MANAGE_SHIPPERS', 'manage_shippers');
define('PERMISSION_MANAGE_COLLECTION_POINTS', 'manage_collection_points');
define('PERMISSION_VIEW_ALL_ORDERS', 'view_all_orders');
define('PERMISSION_ASSIGN_ORDERS', 'assign_orders');
define('PERMISSION_VIEW_SYSTEM_REPORTS', 'view_system_reports');
define('PERMISSION_EXPORT_REPORTS', 'export_reports');
define('PERMISSION_MANAGE_SYSTEM_CONFIG', 'manage_system_config');

// Role Permissions Mapping
$rolePermissions = [
    'user' => [
        PERMISSION_VIEW_MAP,
        PERMISSION_CREATE_BOOKING,
        PERMISSION_VIEW_OWN_ORDERS,
        PERMISSION_REDEEM_REWARDS,
        PERMISSION_TRACK_DEVICES,
    ],
    
    'shipper' => [
        PERMISSION_VIEW_MAP,
        PERMISSION_VIEW_PENDING_ORDERS,
        PERMISSION_ACCEPT_ORDERS,
        PERMISSION_UPDATE_ORDER_STATUS,
        PERMISSION_VIEW_SHIPPER_ORDERS,
        PERMISSION_VIEW_OWN_ORDERS,
        PERMISSION_TRACK_DEVICES,
    ],
    
    'admin' => [
        PERMISSION_VIEW_MAP,
        PERMISSION_CREATE_BOOKING,
        PERMISSION_VIEW_OWN_ORDERS,
        PERMISSION_REDEEM_REWARDS,
        PERMISSION_TRACK_DEVICES,
        PERMISSION_MANAGE_USERS,
        PERMISSION_MANAGE_SHIPPERS,
        PERMISSION_MANAGE_COLLECTION_POINTS,
        PERMISSION_VIEW_ALL_ORDERS,
        PERMISSION_ASSIGN_ORDERS,
        PERMISSION_VIEW_SYSTEM_REPORTS,
        PERMISSION_EXPORT_REPORTS,
        PERMISSION_MANAGE_SYSTEM_CONFIG,
    ]
];

/**
 * Check if user has permission
 */
function hasPermission($permission) {
    if (!isset($_SESSION['user_role'])) {
        return false;
    }
    
    global $rolePermissions;
    $role = $_SESSION['user_role'];
    
    return isset($rolePermissions[$role]) && in_array($permission, $rolePermissions[$role]);
}

/**
 * Check if user has any of the permissions
 */
function hasAnyPermission($permissions) {
    foreach ($permissions as $permission) {
        if (hasPermission($permission)) {
            return true;
        }
    }
    return false;
}

/**
 * Get all permissions for a role
 */
function getRolePermissions($role) {
    global $rolePermissions;
    return $rolePermissions[$role] ?? [];
}

/**
 * Check if user can access a page based on required permissions
 */
function requirePermission($permission, $redirectUrl = 'index.php') {
    if (!hasPermission($permission)) {
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Check if user can access a page based on required any permissions
 */
function requireAnyPermission($permissions, $redirectUrl = 'index.php') {
    if (!hasAnyPermission($permissions)) {
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Get role name in Vietnamese
 */
function getRoleName($role) {
    $roleNames = [
        'user' => 'Người dùng',
        'shipper' => 'Đối tác thu gom',
        'admin' => 'Quản trị viên'
    ];
    return $roleNames[$role] ?? $role;
}

/**
 * Get role badge class
 */
function getRoleBadgeClass($role) {
    $classes = [
        'user' => 'bg-primary',
        'shipper' => 'bg-info',
        'admin' => 'bg-danger'
    ];
    return $classes[$role] ?? 'bg-secondary';
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Check if user is shipper
 */
function isShipper() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'shipper';
}

/**
 * Check if user is regular user
 */
function isUser() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user';
}
?>
