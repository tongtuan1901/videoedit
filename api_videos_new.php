<?php
include 'config.php';

header('Content-Type: application/json');

// lấy 10 video mới nhất (THÊM thumbnail)
$sql = "SELECT id, title, url, thumbnail 
        FROM videos 
        ORDER BY id DESC 
        LIMIT 10";

$res = $conn->query($sql);

$data = [];

while($row = $res->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
?>
