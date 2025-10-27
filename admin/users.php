<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$user = getUserById($_SESSION['user_id']);
$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $userId = (int)$_POST['user_id'];
    $action = $_POST['action'];
    
    switch ($action) {
        case 'update_role':
            $newRole = $_POST['role'];
            global $db;
            $db->query("UPDATE users SET role = ? WHERE id = ?", [$newRole, $userId]);
            $message = 'Đã cập nhật vai trò thành công!';
            break;
            
        case 'toggle_status':
            global $db;
            $db->query("UPDATE users SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?", [$userId]);
            $message = 'Đã thay đổi trạng thái!';
            break;
            
        case 'delete':
            global $db;
            $db->query("DELETE FROM users WHERE id = ?", [$userId]);
            $message = 'Đã xóa người dùng!';
            break;
    }
}

// Get all users
global $db;
$users = $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-recycle me-2"></i>E-Waste Manager Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="admin/dashboard.php">Dashboard</a>
                <a class="nav-link active" href="admin/users.php">Quản lý người dùng</a>
                <a class="nav-link" href="admin/bookings.php">Đơn hàng</a>
                <a class="nav-link" href="index.php">Về trang chủ</a>
                <a class="nav-link" href="logout.php">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2>
                    <i class="fas fa-users text-success me-2"></i>Quản lý người dùng
                </h2>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Danh sách người dùng
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Điện thoại</th>
                                <th>Vai trò</th>
                                <th>Coins</th>
                                <th>Trạng thái</th>
                                <th>Ngày đăng ký</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo $u['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['phone'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge <?php 
                                        echo $u['role'] === 'admin' ? 'bg-danger' : 
                                        ($u['role'] === 'shipper' ? 'bg-info' : 'bg-primary'); 
                                    ?>">
                                        <?php 
                                        echo $u['role'] === 'admin' ? 'Admin' : 
                                        ($u['role'] === 'shipper' ? 'Đối tác thu gom' : 'Người dùng'); 
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark"><?php echo $u['coins']; ?></span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $u['status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo $u['status'] === 'active' ? 'Hoạt động' : 'Khóa'; ?>
                                    </span>
                                </td>
                                <td><?php echo formatDate($u['created_at']); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" 
                                                data-bs-target="#editModal<?php echo $u['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-<?php echo $u['status'] === 'active' ? 'warning' : 'success'; ?>" 
                                                onclick="toggleStatus(<?php echo $u['id']; ?>)">
                                            <i class="fas fa-<?php echo $u['status'] === 'active' ? 'lock' : 'unlock'; ?>"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $u['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Chỉnh sửa người dùng</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update_role">
                                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Tên</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($u['name']); ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Email</label>
                                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($u['email']); ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Vai trò</label>
                                                    <select name="role" class="form-select">
                                                        <option value="user" <?php echo $u['role'] === 'user' ? 'selected' : ''; ?>>Người dùng</option>
                                                        <option value="shipper" <?php echo $u['role'] === 'shipper' ? 'selected' : ''; ?>>Đối tác thu gom</option>
                                                        <option value="admin" <?php echo $u['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                                <button type="submit" class="btn btn-success">Cập nhật</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleStatus(userId) {
            if (confirm('Bạn có chắc muốn thay đổi trạng thái?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                
                const action = document.createElement('input');
                action.type = 'hidden';
                action.name = 'action';
                action.value = 'toggle_status';
                form.appendChild(action);
                
                const userIdInput = document.createElement('input');
                userIdInput.type = 'hidden';
                userIdInput.name = 'user_id';
                userIdInput.value = userId;
                form.appendChild(userIdInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>
