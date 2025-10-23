<?php
include 'db.php';
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maNV = $_POST['MaNV'] ?? '';
    $ngay = $_POST['Ngay'] ?? '';
    $gioVao = $_POST['GioVao'] ?? '';
    $gioRa = $_POST['GioRa'] ?? '';
    $gioLam = $_POST['GioLam'] ?? 0;
    $loaiCong = $_POST['LoaiCong'] ?? 'Công thường';
    $ghiChu = $_POST['GhiChu'] ?? '';

    if (empty($maNV) || empty($ngay)) {
        echo json_encode(["success" => false, "message" => "Thiếu thông tin nhân viên hoặc ngày."]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE ChamCong 
                            SET GioVao=?, GioRa=?, GioLam=?, LoaiCong=?, GhiChu=? 
                            WHERE MaNV=? AND Ngay=?");
    $stmt->bind_param("ssdssss", $gioVao, $gioRa, $gioLam, $loaiCong, $ghiChu, $maNV, $ngay);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Cập nhật chấm công thành công."]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Phương thức không hợp lệ."]);
}
?>
