<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$isLoggedIn = isset($_SESSION['user_id']);
$user = null;
if ($isLoggedIn) {
    $user = getUserById($_SESSION['user_id']);
}

// Get collection points
$collectionPoints = getCollectionPoints();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm điểm thu gom - E-Waste Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
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
                        <a class="nav-link active" href="map.php">Tìm điểm thu gom</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="booking.php">Đặt lịch thu gom</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rewards.php">Đổi thưởng</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if ($isLoggedIn): ?>
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

    <!-- Map Section -->
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Map Column -->
            <div class="col-lg-8">
                <div class="position-relative">
                    <div id="map" style="height: 100vh;"></div>
                    
                    <!-- Location Button -->
                    <div class="position-absolute top-0 end-0 p-3">
                        <button id="getLocationBtn" class="btn btn-primary btn-lg shadow">
                            <i class="fas fa-location-arrow"></i>
                        </button>
                    </div>
                    
                    <!-- Search Box -->
                    <div class="position-absolute top-0 start-0 p-3 w-100">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Tìm kiếm địa điểm...">
                                    <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Collection Points List -->
            <div class="col-lg-4">
                <div class="h-100 bg-light p-3 overflow-auto">
                    <h4 class="mb-3">
                        <i class="fas fa-map-marker-alt text-success me-2"></i>
                        Điểm thu gom gần nhất
                    </h4>
                    
                    <div id="collectionPointsList">
                        <?php foreach ($collectionPoints as $point): ?>
                        <div class="card mb-3 collection-point-card" data-lat="<?php echo $point['latitude']; ?>" data-lng="<?php echo $point['longitude']; ?>">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="fas fa-recycle me-2"></i><?php echo htmlspecialchars($point['name']); ?>
                                </h6>
                                <p class="card-text small text-muted mb-2">
                                    <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($point['address']); ?>
                                </p>
                                <p class="card-text small mb-2">
                                    <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($point['operating_hours']); ?>
                                </p>
                                <p class="card-text small mb-2">
                                    <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($point['phone']); ?>
                                </p>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-primary" onclick="showOnMap(<?php echo $point['latitude']; ?>, <?php echo $point['longitude']; ?>)">
                                        <i class="fas fa-eye me-1"></i>Xem trên bản đồ
                                    </button>
                                    <?php if ($isLoggedIn): ?>
                                    <button class="btn btn-sm btn-success" onclick="bookCollection(<?php echo $point['id']; ?>)">
                                        <i class="fas fa-calendar-plus me-1"></i>Đặt lịch
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (!$isLoggedIn): ?>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <a href="login.php" class="alert-link">Đăng nhập</a> để đặt lịch thu gom tại nhà
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Point Modal -->
    <div class="modal fade" id="collectionPointModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông tin điểm thu gom</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="modalBody">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <?php if ($isLoggedIn): ?>
                    <button type="button" class="btn btn-success" id="bookCollectionBtn">
                        <i class="fas fa-calendar-plus me-1"></i>Đặt lịch thu gom
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let userLocation = null;
        let markers = [];
        
        // Initialize map
        function initMap() {
            map = L.map('map').setView([10.7769, 106.7009], 12);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            
            // Add collection points markers
            <?php foreach ($collectionPoints as $point): ?>
            const marker<?php echo $point['id']; ?> = L.marker([<?php echo $point['latitude']; ?>, <?php echo $point['longitude']; ?>])
                .addTo(map)
                .bindPopup(`
                    <div class="text-center">
                        <h6 class="text-success"><?php echo htmlspecialchars($point['name']); ?></h6>
                        <p class="small mb-2"><?php echo htmlspecialchars($point['address']); ?></p>
                        <p class="small mb-2"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($point['operating_hours']); ?></p>
                        <p class="small mb-2"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($point['phone']); ?></p>
                        <button class="btn btn-sm btn-success" onclick="showCollectionPointDetails(<?php echo $point['id']; ?>)">
                            Xem chi tiết
                        </button>
                    </div>
                `);
            markers.push(marker<?php echo $point['id']; ?>);
            <?php endforeach; ?>
        }
        
        // Get user location
        function getUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLocation = [position.coords.latitude, position.coords.longitude];
                        map.setView(userLocation, 15);
                        
                        // Add user location marker
                        const userMarker = L.marker(userLocation)
                            .addTo(map)
                            .bindPopup('Vị trí của bạn')
                            .openPopup();
                        
                        // Find nearest collection points
                        findNearestPoints(userLocation);
                    },
                    function(error) {
                        alert('Không thể lấy vị trí của bạn. Vui lòng cho phép truy cập vị trí.');
                    }
                );
            } else {
                alert('Trình duyệt không hỗ trợ định vị GPS.');
            }
        }
        
        // Find nearest collection points
        function findNearestPoints(userLatLng) {
            const collectionPoints = <?php echo json_encode($collectionPoints); ?>;
            const pointsWithDistance = collectionPoints.map(point => {
                const distance = calculateDistance(
                    userLatLng[0], userLatLng[1],
                    parseFloat(point.latitude), parseFloat(point.longitude)
                );
                return { ...point, distance };
            });
            
            // Sort by distance
            pointsWithDistance.sort((a, b) => a.distance - b.distance);
            
            // Update the list
            updateCollectionPointsList(pointsWithDistance);
        }
        
        // Calculate distance between two points
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius of the Earth in km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }
        
        // Update collection points list
        function updateCollectionPointsList(points) {
            const listContainer = document.getElementById('collectionPointsList');
            listContainer.innerHTML = '';
            
            points.forEach(point => {
                const card = document.createElement('div');
                card.className = 'card mb-3 collection-point-card';
                card.setAttribute('data-lat', point.latitude);
                card.setAttribute('data-lng', point.longitude);
                
                card.innerHTML = `
                    <div class="card-body">
                        <h6 class="card-title text-success">
                            <i class="fas fa-recycle me-2"></i>${point.name}
                            <span class="badge bg-primary ms-2">${point.distance.toFixed(1)} km</span>
                        </h6>
                        <p class="card-text small text-muted mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>${point.address}
                        </p>
                        <p class="card-text small mb-2">
                            <i class="fas fa-clock me-1"></i>${point.operating_hours}
                        </p>
                        <p class="card-text small mb-2">
                            <i class="fas fa-phone me-1"></i>${point.phone}
                        </p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="showOnMap(${point.latitude}, ${point.longitude})">
                                <i class="fas fa-eye me-1"></i>Xem trên bản đồ
                            </button>
                            ${<?php echo $isLoggedIn ? 'true' : 'false'; ?> ? `
                            <button class="btn btn-sm btn-success" onclick="bookCollection(${point.id})">
                                <i class="fas fa-calendar-plus me-1"></i>Đặt lịch
                            </button>
                            ` : ''}
                        </div>
                    </div>
                `;
                
                listContainer.appendChild(card);
            });
        }
        
        // Show point on map
        function showOnMap(lat, lng) {
            map.setView([lat, lng], 16);
        }
        
        // Show collection point details
        function showCollectionPointDetails(pointId) {
            const collectionPoints = <?php echo json_encode($collectionPoints); ?>;
            const point = collectionPoints.find(p => p.id == pointId);
            
            if (point) {
                document.getElementById('modalBody').innerHTML = `
                    <div class="text-center">
                        <h5 class="text-success">${point.name}</h5>
                        <p class="text-muted">${point.address}</p>
                        <hr>
                        <div class="row text-start">
                            <div class="col-6">
                                <p><i class="fas fa-clock text-primary"></i> <strong>Giờ hoạt động:</strong></p>
                                <p>${point.operating_hours}</p>
                            </div>
                            <div class="col-6">
                                <p><i class="fas fa-phone text-success"></i> <strong>Điện thoại:</strong></p>
                                <p>${point.phone}</p>
                            </div>
                        </div>
                        ${point.description ? `
                        <hr>
                        <p><i class="fas fa-info-circle text-info"></i> <strong>Mô tả:</strong></p>
                        <p class="text-muted">${point.description}</p>
                        ` : ''}
                    </div>
                `;
                
                document.getElementById('bookCollectionBtn').onclick = () => bookCollection(pointId);
                
                const modal = new bootstrap.Modal(document.getElementById('collectionPointModal'));
                modal.show();
            }
        }
        
        // Book collection
        function bookCollection(pointId) {
            window.location.href = `booking.php?point_id=${pointId}`;
        }
        
        // Event listeners
        document.getElementById('getLocationBtn').addEventListener('click', getUserLocation);
        
        // Initialize map when page loads
        document.addEventListener('DOMContentLoaded', initMap);
    </script>
</body>
</html>
