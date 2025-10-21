<?php
include 'db.php';
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['MaNV'])) {
    // Lấy 1 nhân viên theo mã
    $maNV = intval($_GET['MaNV']);
    $sql = "SELECT nv.MaNV, nv.HoTen, nv.MaPB, pb.TenPhongBan, nv.LuongCB, 
                    nv.NgaySinh, nv.NgayVaoLam, nv.SDT, nv.TrangThai
            FROM NhanVien nv
            LEFT JOIN PhongBan pb ON nv.MaPB = pb.MaPB
            WHERE nv.MaNV = $maNV
            LIMIT 1";
} else {
    // Lấy tất cả nhân viên
    $sql = "SELECT nv.MaNV, nv.HoTen, pb.TenPhongBan, nv.LuongCB, 
                    nv.NgaySinh, nv.NgayVaoLam, nv.SDT, nv.TrangThai
            FROM NhanVien nv
            LEFT JOIN PhongBan pb ON nv.MaPB = pb.MaPB
            ORDER BY nv.MaNV ASC";
}

$result = $conn->query($sql);
$data = [];

if (isset($_GET['MaNV'])) {
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(["success" => true, "nhanvien" => $data], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["success" => false, "error" => "Không tìm thấy nhân viên"], JSON_UNESCAPED_UNICODE);
    }
} else {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["success" => true, "nhanvien" => $data], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
