<?php
include 'db.php';

$quy = isset($_GET['quy']) ? intval($_GET['quy']) : 0;
$nam = isset($_GET['nam']) ? intval($_GET['nam']) : date('Y');

// Xác định các tháng trong quý
if ($quy > 0 && $quy <= 4) {
    $startMonth = ($quy - 1) * 3 + 1;
    $endMonth = $startMonth + 2;
} else {
    $startMonth = 1;
    $endMonth = 12;
}

$sql = "
    SELECT 
        l.Thang,
        SUM(l.TongLuong) AS TongLuongThang
    FROM Luong l
    WHERE l.Nam = $nam AND l.Thang BETWEEN $startMonth AND $endMonth
    GROUP BY l.Thang
    ORDER BY l.Thang ASC
";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>
