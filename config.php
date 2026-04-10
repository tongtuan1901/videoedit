<?php
$host = "localhost";     // đổi nếu deploy
$user = "root";
$pass = "";
$db   = "video_portfolio";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Kết nối lỗi: " . $conn->connect_error);
}
?>