<?php
include 'db.php';

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $maNV = $_POST['MaNV'] ?? '';
    $hoTen = $_POST['TenNV'] ?? '';
    $maPB = $_POST['MaPB'] ?? null;
    $luongCB = $_POST['LuongCoBan'] ?? 0;
    $ngaySinh = $_POST['NgaySinh'] ?? null;
    $ngayVaoLam = $_POST['NgayVaoLam'] ?? null;
    $sdt = $_POST['SoDienThoai'] ?? null;
    $trangThai = $_POST['TrangThai'] ?? '';

    // ✅ Nếu trống thì set NULL hoặc giá trị mặc định
    $ngaySinh = !empty($ngaySinh) ? $ngaySinh : null;
    $ngayVaoLam = !empty($ngayVaoLam) ? $ngayVaoLam : null;
    $trangThai = !empty($trangThai) ? $trangThai : 'Đang làm';

    if (empty($maNV) || empty($hoTen)) {
        echo json_encode(['success' => false, 'error' => 'Thiếu thông tin nhân viên!']);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            UPDATE nhanvien
            SET HoTen = ?, MaPB = ?, LuongCB = ?, NgaySinh = ?, NgayVaoLam = ?, SDT = ?, TrangThai = ?
            WHERE MaNV = ?
        ");

        // Gán kiểu dữ liệu phù hợp
        $stmt->bind_param(
            "sidsssss",
            $hoTen,
            $maPB,
            $luongCB,
            $ngaySinh,
            $ngayVaoLam,
            $sdt,
            $trangThai,
            $maNV
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật nhân viên thành công!']);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Phương thức không hợp lệ!']);
}
?>
