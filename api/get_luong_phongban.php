<?php
include 'db.php';
header('Content-Type: application/json; charset=utf-8');

$thang = $_GET['Thang'] ?? '';
$nam = $_GET['Nam'] ?? '';
$maPB = $_GET['MaPB'] ?? '';

$where = "WHERE 1";
if (!empty($thang)) $where .= " AND l.Thang = " . intval($thang);
if (!empty($nam)) $where .= " AND l.Nam = " . intval($nam);
if (!empty($maPB)) $where .= " AND pb.MaPB = '" . $conn->real_escape_string($maPB) . "'";

//l.MaNV = nv.MaNV → lấy thông tin nhân viên tương ứng với dòng lương.
//nv.MaPB = pb.MaPB → từ nhân viên lấy được phòng ban.
//Sau đó mới GROUP BY theo pb.MaPB để gom nhóm lương theo phòng ban.

$sql = "
SELECT 
    pb.MaPB,
    pb.TenPhongBan,
    SUM(l.TongLuong) AS TongLuong,
    COUNT(l.MaNV) AS SoNhanVien
FROM 
    luong l
JOIN 
    nhanvien nv ON l.MaNV = nv.MaNV
JOIN 
    phongban pb ON nv.MaPB = pb.MaPB
$where
GROUP BY 
    pb.MaPB, pb.TenPhongBan
ORDER BY 
    TongLuong DESC
";

$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>
