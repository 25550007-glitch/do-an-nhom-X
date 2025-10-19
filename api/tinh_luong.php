<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $thang = $_POST['Thang'];
    $nam = $_POST['Nam'];
    $luongTheoGio = $_POST['LuongTheoGio'];

    $query = "
        SELECT 
            nv.MaNV,
            nv.HoTen,
            pb.TenPhongBan,
            nv.LuongCB,
            IFNULL(SUM(cc.GioLam * ?), 0) AS TheoGio,
            0 AS Thuong,
            0 AS PhuCap,
            0 AS KhauTru,
            (nv.LuongCB + IFNULL(SUM(cc.GioLam * ?), 0)) AS TongLuong
        FROM NhanVien nv
        LEFT JOIN ChamCong cc ON nv.MaNV = cc.MaNV
        LEFT JOIN PhongBan pb ON nv.MaPB = pb.MaPB
        WHERE MONTH(cc.Ngay) = ? AND YEAR(cc.Ngay) = ?
        GROUP BY nv.MaNV, nv.HoTen, pb.TenPhongBan, nv.LuongCB
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ddii", $luongTheoGio, $luongTheoGio, $thang, $nam);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

      // 🔥 Xoá dữ liệu lương cũ của tháng này để đảm bảo cập nhật lại sạch
    $deleteStmt = $conn->prepare("DELETE FROM Luong WHERE Thang = ?");
    $deleteStmt->bind_param("i", $thang);
    $deleteStmt->execute();


    // ✅ Cập nhật bảng Luong sau khi tính
    foreach ($rows as $r) {
        $stmtInsert = $conn->prepare("
            REPLACE INTO Luong (MaNV, Thang, LuongCB, TheoGio, TongLuong)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmtInsert->bind_param(
            "siddd",
            $r['MaNV'],
            $thang,
            $r['LuongCB'],
            $r['TheoGio'],
            $r['TongLuong']
        );
        $stmtInsert->execute();
    }

    echo json_encode([
        "status" => "success",
        "message" => "✅ Đã tính lương tháng $thang/$nam thành công!",
        "data" => $rows
    ]);
}
?>
