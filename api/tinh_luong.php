<?php
include 'db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $thang = intval($_POST['Thang']);
    $nam = intval($_POST['Nam']);

    // Số giờ làm chuẩn trong tháng (23 ngày làm việc x 8 tiếng)
    $gioChuan = 184;

    // Xóa dữ liệu cũ để tính lại
    $conn->query("DELETE FROM Luong WHERE Thang = $thang AND Nam = $nam");

    // Tính và chèn dữ liệu mới vào bảng Lương
    $sql = "
        INSERT INTO Luong (MaNV, Thang, Nam, LuongCB, TongGioLam, TangCa, Thuong, PhuCap, KhauTru, TongLuong)
        SELECT 
            nv.MaNV,
            $thang AS Thang,
            $nam AS Nam,
            nv.LuongCB,

            /* Tổng giờ làm trong tháng */
            ROUND(IFNULL(AVG(cc.GioLam), 0) * 23, 2) AS TongGioLam,

            0 AS TangCa, 
            0 AS Thuong,
            0 AS PhuCap,

            /* Khấu trừ theo yêu cầu: nếu thiếu giờ thì (gioChuan - TongGioLam) * (LuongCB / gioChuan) else 0 */
            CASE
                WHEN ROUND(IFNULL(AVG(cc.GioLam), 0) * 23, 2) < $gioChuan
                THEN ROUND(
                    ($gioChuan - (IFNULL(AVG(cc.GioLam), 0) * 23)) * (nv.LuongCB / $gioChuan), 2
                )
                ELSE 0
            END AS KhauTru,

            /* Tổng lương = Lương cơ bản - Khấu trừ + Tăng ca + Thưởng + Phụ cấp */
            ROUND(
                nv.LuongCB
                - CASE
                    WHEN ROUND(IFNULL(AVG(cc.GioLam), 0) * 23, 2) < $gioChuan
                    THEN (($gioChuan - (IFNULL(AVG(cc.GioLam), 0) * 23)) * (nv.LuongCB / $gioChuan))
                    ELSE 0 END
                + 0 + 0 + 0
            , 2) AS TongLuong

        FROM NhanVien nv
        LEFT JOIN ChamCong cc 
            ON nv.MaNV = cc.MaNV 
            AND MONTH(cc.Ngay) = $thang 
            AND YEAR(cc.Ngay) = $nam
        GROUP BY nv.MaNV, nv.LuongCB
    ";

    if ($conn->query($sql) === TRUE) {
        echo json_encode([
            "status" => "success",
            "message" => "✅ Đã tính lương tháng $thang/$nam thành công cho toàn bộ nhân viên!"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "❌ Lỗi khi tính lương: " . $conn->error
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Phương thức không hợp lệ."
    ], JSON_UNESCAPED_UNICODE);
}
?>
