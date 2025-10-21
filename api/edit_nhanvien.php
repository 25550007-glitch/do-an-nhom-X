<?php
include 'db.php'; // file kết nối CSDL

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Chỉ xử lý khi phương thức là POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maNV = $_POST['MaNV'] ?? '';
    $hoTen = $_POST['HoTen'] ?? '';
    $maPB = $_POST['MaPB'] ?? '';
    $luongCB = $_POST['LuongCB'] ?? '';
    $sdt = $_POST['SDT'] ?? '';
    $trangThai = $_POST['TrangThai'] ?? '';

    // Kiểm tra dữ liệu bắt buộc
    if (empty($maNV) || empty($hoTen)) {
        echo json_encode(['success' => false, 'error' => 'Thiếu thông tin nhân viên!']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            UPDATE nhanvien
            SET HoTen = ?, MaPB = ?, LuongCB = ?, SDT = ?, TrangThai = ?
            WHERE MaNV = ?
        ");
        $stmt->bind_param("sidsss", $hoTen, $maPB, $luongCB, $sdt, $trangThai, $maNV);


        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật nhân viên thành công!']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Không thể cập nhật nhân viên!']);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Phương thức không hợp lệ!']);
}
?>
