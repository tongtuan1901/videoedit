<?php
include 'config.php';

$sql = "
SELECT c.*, 
COUNT(v.id) as total
FROM categories c
LEFT JOIN videos v ON c.id = v.category_id
GROUP BY c.id
";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);