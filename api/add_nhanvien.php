<?php
include 'db.php';

$MaNV = $_POST['MaNV'];
$HoTen = $_POST['TenNV'];
$MaPB = $_POST['MaPB'] ?: NULL;
$LuongCB = $_POST['LuongCoBan'] ?: 0;
$SDT = $_POST['SoDienThoai'];
$TrangThai = 'Đang làm';

$stmt = $conn->prepare("INSERT INTO NhanVien (MaNV, HoTen, MaPB, LuongCB, SDT, TrangThai) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssidis", $MaNV, $HoTen, $MaPB, $LuongCB, $SDT, $TrangThai);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => $stmt->error]);
}
?>
