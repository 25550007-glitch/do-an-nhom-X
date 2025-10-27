<?php
// login.php - Xử lý đăng nhập cho Admin
session_start();
header('Content-Type: application/json; charset=utf-8');
// Include file kết nối database
require_once './db.php';

// Hàm ghi log đăng nhập
function logLoginAttempt($conn, $adminId, $success = true) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    if ($success && $adminId) {
        $stmt = $conn->prepare("INSERT INTO SessionLog (MaAdmin, IP, UserAgent) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $adminId, $ip, $userAgent);
        $stmt->execute();
        $stmt->close();
    }
}

// Kiểm tra method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không hợp lệ'
    ]);
    exit;
}

// Lấy dữ liệu từ request
$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';
$rememberMe = $input['rememberMe'] ?? false;

// Validate dữ liệu đầu vào
// if (empty($username) || empty($password)) {
//     echo json_encode([
//         'success' => false,
//         'message' => 'Vui lòng nhập đầy đủ thông tin đăng nhập'
//     ]);
//     exit;
// }

try {
    // Tìm admin theo email HOẶC tên đăng nhập
    $stmt = $conn->prepare("
        SELECT MaAdmin, HoTen, Email, TenDangNhap, MatKhau, VaiTro, TrangThai 
        FROM Admin 
        WHERE (Email = ? OR TenDangNhap = ?) AND TrangThai = 'Hoạt động'
        LIMIT 1
    ");
    
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Tài khoản không tồn tại hoặc đã bị khóa'
        ]);
        $stmt->close();
        exit;
    }
    
    $admin = $result->fetch_assoc();
    $stmt->close();
    
    // Kiểm tra mật khẩu
    if (!password_verify($password, $admin['MatKhau'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Mật khẩu không chính xác'
        ]);
        exit;
    }
    
    // Đăng nhập thành công - Tạo session
    $_SESSION['admin_id'] = $admin['MaAdmin'];
    $_SESSION['admin_name'] = $admin['HoTen'];
    $_SESSION['admin_email'] = $admin['Email'];
    $_SESSION['admin_role'] = $admin['VaiTro'];
    $_SESSION['login_time'] = time();
    
    // Cập nhật thời gian đăng nhập cuối
    $updateStmt = $conn->prepare("UPDATE Admin SET LanDangNhapCuoi = NOW() WHERE MaAdmin = ?");
    $updateStmt->bind_param("i", $admin['MaAdmin']);
    $updateStmt->execute();
    $updateStmt->close();
    
    // Ghi log
    // logLoginAttempt($conn, $admin['MaAdmin'], true);
    
    // Set cookie nếu chọn "Ghi nhớ đăng nhập"
    // if ($rememberMe) {
    //     $token = bin2hex(random_bytes(32));
    //     setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
        
    //     // Lưu token vào database (cần tạo bảng RememberTokens)
    //     // Để đơn giản, bỏ qua phần này trong ví dụ
    // }
    
    echo json_encode([
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'data' => [
            'admin_id' => $admin['MaAdmin'],
            'name' => $admin['HoTen'],
            'email' => $admin['Email'],
            'role' => $admin['VaiTro']
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
?>