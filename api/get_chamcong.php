<?php
include 'db.php';
header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$thang = $_GET['Thang'] ?? '';
$nam = $_GET['Nam'] ?? '';
$maNV = $_GET['MaNV'] ?? '';
$loaiCong = $_GET['LoaiCong'] ?? '';

$sql = "
    SELECT 
        cc.Ngay,
        cc.MaNV,
        nv.HoTen,
        cc.GioVao,
        cc.GioRa,
        cc.GioLam,
        cc.LoaiCong,
        cc.TrangThai,
        cc.GhiChu
    FROM ChamCong cc
    JOIN NhanVien nv ON cc.MaNV = nv.MaNV
    WHERE 1=1
";

if (!empty($thang)) {
    $sql .= " AND MONTH(cc.Ngay) = " . intval($thang);
}

if (!empty($nam)) {
    $sql .= " AND YEAR(cc.Ngay) = " . intval($nam);
}

if (!empty($maNV)) {
    $sql .= " AND cc.MaNV = '" . mysqli_real_escape_string($conn, $maNV) . "'";
}

if (!empty($loaiCong)) {
    $sql .= " AND cc.LoaiCong = '" . mysqli_real_escape_string($conn, $loaiCong) . "'";
}

$sql .= " ORDER BY cc.Ngay DESC";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
$conn->close();
?>
