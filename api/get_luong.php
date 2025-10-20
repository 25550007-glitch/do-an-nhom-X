<?php
include 'db.php';

$thang = isset($_GET['thang']) ? intval($_GET['thang']) : date('n');
$nam = isset($_GET['nam']) ? intval($_GET['nam']) : date('Y');

// Chỉ lấy dữ liệu đã được tính trong bảng Lương
$sql = "
    SELECT 
        l.MaLuong,
        l.MaNV,
        nv.HoTen,
        pb.TenPhongBan,
        l.LuongCB,
        l.TongGioLam,
        l.TangCa,
        l.Thuong,
        l.PhuCap,
        l.KhauTru,
        l.TongLuong
    FROM Luong l
    LEFT JOIN NhanVien nv ON l.MaNV = nv.MaNV
    LEFT JOIN PhongBan pb ON nv.MaPB = pb.MaPB
    WHERE l.Thang = $thang AND l.Nam = $nam
    ORDER BY l.MaLuong ASC
";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
