<?php
include 'db.php';

$sql = "SELECT MaPB, TenPhongBan, MoTa, NgayTao FROM PhongBan ORDER BY TenPhongBan";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
