<?php
include 'db.php';

// Lấy danh sách chấm công, join để hiển thị tên nhân viên
$sql = "SELECT cc.Ngay, cc.MaNV, nv.HoTen, cc.GioVao, cc.GioRa, cc.GioLam, 
               cc.LoaiCong, cc.TrangThai, cc.GhiChu
        FROM ChamCong cc
        LEFT JOIN NhanVien nv ON cc.MaNV = nv.MaNV
        ORDER BY cc.Ngay DESC";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
