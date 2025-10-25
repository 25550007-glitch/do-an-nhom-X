<?php
include 'db.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maNV = intval($_POST['MaNV']);
    $thuong = floatval($_POST['Thuong']);
    $phuCap = floatval($_POST['PhuCap']);
    $khauTru = floatval($_POST['KhauTru']);
    $thang = intval($_POST['Thang']);
    $nam = date('Y');

    // Cập nhật thưởng, phụ cấp, khấu trừ cho nhân viên trong tháng/năm hiện tại
    $sql = "
        UPDATE Luong
        SET 
            Thuong = $thuong,
            PhuCap = $phuCap,
            KhauTru = $khauTru
        WHERE MaNV = $maNV AND Thang = $thang AND Nam = $nam
    ";

    if ($conn->query($sql)) {
        // ✅ Gọi lại Stored Procedure để tính tổng lương mới
        $call = "CALL SP_TinhLuongThuong($thang, $nam)";
        $conn->query($call);

        echo json_encode([
            'success' => true,
            'message' => "Cập nhật thưởng/phụ cấp thành công tháng $thang/$nam!"
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '❌ Lỗi khi cập nhật thưởng/phụ cấp: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Phương thức không hợp lệ!'
    ]);
}
?>
