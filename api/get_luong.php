<?php
include 'db.php';

// Lấy danh sách lương, join bảng nhân viên và phòng ban
$sql = " SELECT 
        l.MaLuong,
        l.MaNV,
        nv.HoTen,
        pb.TenPhongBan,
        l.LuongCB,
        l.TheoGio,
        l.TangCa,
        l.Thuong,
        l.PhuCap,
        l.KhauTru,
        (l.LuongCB + l.TheoGio + l.TangCa + l.Thuong + l.PhuCap - l.KhauTru) AS TongLuong
    FROM Luong l
    LEFT JOIN NhanVien nv ON l.MaNV = nv.MaNV
    LEFT JOIN PhongBan pb ON nv.MaPB = pb.MaPB
    ORDER BY l.MaLuong ASC";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
