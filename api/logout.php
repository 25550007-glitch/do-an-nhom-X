<?php
// logout.php - Xử lý đăng xuất
session_start();

require_once './db.php';

// Ghi log đăng xuất
if (isset($_SESSION['admin_id'])) {
    try {
        $stmt = $conn->prepare("
            UPDATE SessionLog 
            SET ThoiGianDangXuat = NOW(), TrangThai = 'Đã đăng xuất'
            WHERE MaAdmin = ? AND TrangThai = 'Đang hoạt động'
            ORDER BY ThoiGianDangNhap DESC
            LIMIT 1
        ");
        $stmt->bind_param("i", $_SESSION['admin_id']);
        $stmt->execute();
        $stmt->close();
    } catch (Exception $e) {
        // Log error nếu cần
    }
}

// Xóa tất cả session
session_unset();
session_destroy();

// Xóa cookie remember me
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// Redirect về trang login
header('Location: ../pages/login.php?message=logout_success');
exit;
?>