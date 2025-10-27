<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Vui lòng điền đầy đủ thông tin.';
    } else {
        $user = authenticateUser($email, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } elseif ($user['role'] === 'shipper') {
                header('Location: shipper/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Email hoặc mật khẩu không đúng.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - E-Waste Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-recycle me-2"></i>E-Waste Manager
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="register.php">Đăng ký</a>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow">
                    <div class="card-header bg-success text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Roles Info -->
                        <div class="alert alert-info mb-3">
                            <h6 class="mb-2"><i class="fas fa-info-circle me-1"></i>3 Vai trò trong hệ thống:</h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <i class="fas fa-crown text-danger fa-2x mb-1"></i>
                                    <p class="small mb-0"><strong>Admin</strong><br>Quản lý hệ thống</p>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-truck text-info fa-2x mb-1"></i>
                                    <p class="small mb-0"><strong>Shipper</strong><br>Thu gom rác thải</p>
                                </div>
                                <div class="col-4">
                                    <i class="fas fa-user text-primary fa-2x mb-1"></i>
                                    <p class="small mb-0"><strong>User</strong><br>Người dùng</p>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST" id="loginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">
                                    Ghi nhớ đăng nhập
                                </label>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <div class="text-center">
                            <p class="mb-0">Chưa có tài khoản? <a href="register.php" class="text-success">Đăng ký ngay</a></p>
                        </div>
                    </div>
                </div>
                
                <!-- Demo Account Info -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Tài khoản demo
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="p-2 border border-success rounded">
                                    <strong class="text-success"><i class="fas fa-crown me-1"></i>Admin:</strong><br>
                                    <small class="text-muted">admin@gmail.com</small><br>
                                    <small class="text-dark fw-bold">123456</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="p-2 border border-info rounded">
                                    <strong class="text-info"><i class="fas fa-truck me-1"></i>Shipper:</strong><br>
                                    <small class="text-muted">shipper@gmail.com</small><br>
                                    <small class="text-dark fw-bold">123456</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="p-2 border border-primary rounded">
                                    <strong class="text-primary"><i class="fas fa-user me-1"></i>User:</strong><br>
                                    <small class="text-muted">(Đăng ký mới)</small><br>
                                    <small class="text-dark fw-bold">-</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const button = passwordField.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
