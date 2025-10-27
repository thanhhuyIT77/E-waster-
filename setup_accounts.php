<?php
// Script to insert admin and shipper accounts into database
// Run this file once: php setup_accounts.php

require_once 'config/database.php';

$adminPassword = '$2y$10$YOlP8Owny81gzw9cBISFre1ycUrWQ5Z3hlKaEmL5cdsZvnPebYVTW'; // 123456

try {
    // Delete old accounts if exist
    $db->query("DELETE FROM users WHERE email IN ('admin@gmail.com', 'shipper@gmail.com', 'admin2@gmail.com', 'shipper2@gmail.com')");
    
    // Insert admin
    $db->query("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)",
        ['Cơ quan Môi trường', 'admin@gmail.com', $adminPassword, 'admin', '0987654321', 'TP.HCM']
    );
    $adminId = $db->lastInsertId();
    
    // Insert admin details
    $db->query("INSERT INTO admin_details (user_id, department, position, office_address, notes) VALUES (?, ?, ?, ?, ?)",
        [$adminId, 'Sở Tài nguyên và Môi trường', 'Trưởng phòng', 'Số 72 Nguyễn Trãi, Quận 1, TP.HCM', 'Cơ quan quản lý môi trường thành phố']
    );
    
    // Insert admin 2
    $db->query("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)",
        ['Phó Giám đốc', 'admin2@gmail.com', $adminPassword, 'admin', '0923456789', 'TP.HCM']
    );
    $admin2Id = $db->lastInsertId();
    
    $db->query("INSERT INTO admin_details (user_id, department, position, office_address, notes) VALUES (?, ?, ?, ?, ?)",
        [$admin2Id, 'Sở Tài nguyên và Môi trường', 'Phó Giám đốc', 'Số 72 Nguyễn Trãi, Quận 1, TP.HCM', 'Phụ trách quản lý rác thải điện tử']
    );
    
    // Insert shipper
    $db->query("INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)",
        ['Đơn vị thu gom A', 'shipper@gmail.com', $adminPassword, '0901234567', 'TP.HCM', 'shipper']
    );
    $shipperId = $db->lastInsertId();
    
    // Insert shipper details
    $db->query("INSERT INTO shipper_details (user_id, company_name, license_number, vehicle_info, area_covered, notes) VALUES (?, ?, ?, ?, ?, ?)",
        [$shipperId, 'Công ty Môi trường Xanh', 'GP123456', 'Xe tải 5 tấn - 29A-12345', 'Toàn thành phố', 'Đơn vị thu gom rác thải điện tử chuyên nghiệp']
    );
    
    // Insert shipper 2
    $db->query("INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)",
        ['Công ty Tái chế ABC', 'shipper2@gmail.com', $adminPassword, '0912345678', 'TP.HCM', 'shipper']
    );
    $shipper2Id = $db->lastInsertId();
    
    $db->query("INSERT INTO shipper_details (user_id, company_name, license_number, vehicle_info, area_covered, notes) VALUES (?, ?, ?, ?, ?, ?)",
        [$shipper2Id, 'Công ty Tái chế ABC', 'GP789012', 'Xe container - 51B-67890', 'Quận 1, 3, 7', 'Chuyên thu gom và tái chế rác thải điện tử']
    );
    
    echo "✓ Tài khoản đã được tạo thành công!\n\n";
    echo "Admin 1: admin@gmail.com / 123456\n";
    echo "Admin 2: admin2@gmail.com / 123456\n";
    echo "Shipper 1: shipper@gmail.com / 123456\n";
    echo "Shipper 2: shipper2@gmail.com / 123456\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
