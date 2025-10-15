<?php
include 'db.php';

$sql = "SELECT nv.MaNV, nv.HoTen, pb.TenPhongBan, nv.LuongCB, nv.SDT, nv.TrangThai
        FROM NhanVien nv
        LEFT JOIN PhongBan pb ON nv.MaPB = pb.MaPB
        ORDER BY nv.MaNV ASC";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
