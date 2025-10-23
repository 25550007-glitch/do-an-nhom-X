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
    $loaiCong = $_POST['LoaiCong'] ?? 'Thường';
    $ghiChu = $_POST['GhiChu'] ?? '';

    if (empty($maNV) || empty($ngay)) {
        echo json_encode(["success" => false, "message" => "Thiếu thông tin nhân viên hoặc ngày."]);
        exit;
    }

    // Nếu không nhập trạng thái thì mặc định là "Đã duyệt"
    $trangThai = 'Đã duyệt';

    $stmt = $conn->prepare("INSERT INTO ChamCong (MaNV, Ngay, GioVao, GioRa, GioLam, LoaiCong, TrangThai, GhiChu)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $maNV, $ngay, $gioVao, $gioRa, $gioLam, $loaiCong, $trangThai, $ghiChu);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Thêm chấm công thành công."]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi khi thêm chấm công: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Phương thức không hợp lệ."]);
}
?>
