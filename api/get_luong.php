<?php
include 'db.php';

$thang = isset($_GET['thang']) ? intval($_GET['thang']) : date('n');
$nam = isset($_GET['nam']) ? intval($_GET['nam']) : date('Y');
$mode = isset($_GET['mode']) ? $_GET['mode'] : ''; // nếu = secure thì dùng view

if ($mode === 'secure') {
    // Dữ liệu bảo mật (ẩn lương từng người)
    $sql = "
        SELECT 
            l.MaLuong,
            l.MaNV,
            nv.HoTen,
            pb.TenPhongBan,
            l.Thang,
            l.Nam,
            CONCAT('***', RIGHT(l.LuongCB, 3)) AS LuongCB,
            l.TongGioLam,
            '***' AS TangCa,
            '***' AS Thuong,
            '***' AS PhuCap,
            '***' AS KhauTru,
            CONCAT('***', RIGHT(l.TongLuong, 3)) AS TongLuong
        FROM Luong l
        LEFT JOIN NhanVien nv ON l.MaNV = nv.MaNV
        LEFT JOIN PhongBan pb ON nv.MaPB = pb.MaPB
        WHERE l.Thang = $thang AND l.Nam = $nam
        ORDER BY l.MaLuong ASC
    ";

    $data = [];
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    // 🧮 Lấy tổng thật (vì view bị ẩn rồi)
    $sqlSum = "
        SELECT 
            SUM(LuongCB) AS TongLuongCB,
            SUM(TangCa) AS TongTangCa,
            SUM(Thuong) AS TongThuong,
            SUM(PhuCap) AS TongPhuCap,
            SUM(KhauTru) AS TongKhauTru,
            SUM(TongLuong) AS TongTongLuong
        FROM Luong
        WHERE Thang = $thang AND Nam = $nam
    ";
    $sumResult = $conn->query($sqlSum);
    $tong = $sumResult->fetch_assoc();

    echo json_encode([
        "secure" => true,
        "data" => $data,
        "tong" => $tong
    ], JSON_UNESCAPED_UNICODE);
    exit;
} else {
    // Dùng bảng thật
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
}

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
