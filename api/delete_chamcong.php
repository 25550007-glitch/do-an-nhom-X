<?php
include 'db.php';
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maNV = $_POST['MaNV'] ?? '';
    $ngay = $_POST['Ngay'] ?? '';

    if (empty($maNV) || empty($ngay)) {
        echo json_encode(["success" => false, "message" => "Thiếu thông tin Mã nhân viên hoặc Ngày."]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM ChamCong WHERE MaNV = ? AND Ngay = ?");
    $stmt->bind_param("ss", $maNV, $ngay);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Đã xoá chấm công thành công."]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi khi xoá chấm công: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Phương thức không hợp lệ."]);
}
?>
