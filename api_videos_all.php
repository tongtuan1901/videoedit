<?php
include 'config.php';

$sql = "SELECT * FROM videos ORDER BY id DESC";
$result = $conn->query($sql);

$data = [];

if($result){
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
}

echo json_encode($data);
?>
