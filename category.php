<?php include 'config.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh sách video</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:Poppins;
    background:#020617;
    color:white;
}

/* HEADER */
header{
    height:40vh;
    background:url('https://images.unsplash.com/photo-1524985069026-dd778a71c7b4') center/cover;
    display:flex;
    align-items:center;
    justify-content:center;
}
header h1{
    background:rgba(0,0,0,0.6);
    padding:15px 30px;
    border-radius:10px;
    font-size:36px;
}

/* GRID */
.container{ padding:30px; }

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:25px;
}

/* CARD */
.card{
    position:relative;
    border-radius:15px;
    overflow:hidden;
    background:#0f172a;
    transition:0.3s;
}

.card:hover{
    transform:scale(1.05);
}

/* VIDEO */
video{
    width:100%;
    height:200px;
    object-fit:cover;
}

/* OVERLAY */
.overlay{
    position:absolute;
    bottom:0;
    width:100%;
    padding:15px;
    background:linear-gradient(to top, rgba(0,0,0,0.9), transparent);
}

.overlay h3{
    margin:0;
}

/* FULLSCREEN BUTTON */
.controls{
    position:absolute;
    bottom:10px;
    right:10px;
    opacity:0;
    transition:0.25s;
}

.card:hover .controls{
    opacity:1;
}

.btn{
    width:40px;
    height:40px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:10px;
    background:rgba(0,0,0,0.5);
    backdrop-filter:blur(6px);
    cursor:pointer;
    transition:0.2s;
}

.btn:hover{
    background:rgba(255,255,255,0.2);
    transform:scale(1.1);
}

.btn svg{
    width:20px;
    height:20px;
    fill:white;
}

/* TRƯỜNG HỢP 1 VIDEO */
.grid.single{
    display:flex;
    justify-content:center;
}

.grid.single .card{
    width:600px;
    max-width:90%;
    box-shadow:0 20px 50px rgba(0,0,0,0.8);
}

.grid.single video{
    height:350px;
}
</style>

</head>
<body>

<header>
<h1>🎬 Danh sách video</h1>
</header>

<div class="container">

<?php
$id = intval($_GET['id']);
$sql = "SELECT * FROM videos WHERE category_id=$id";
$result = $conn->query($sql);

// ✅ đếm sau khi query
$count = ($result) ? $result->num_rows : 0;
$gridClass = ($count == 1) ? "grid single" : "grid";
?>

<div class="<?= $gridClass ?>">

<?php
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
?>

<div class="card">

    <video id="video<?= $row['id'] ?>" muted playsinline>
        <source src="<?= $row['url'] ?>" type="video/mp4">
    </video>

    <div class="overlay">
        <h3><?= $row['title'] ?></h3>
    </div>

    <div class="controls">
        <div class="btn" onclick="goFull('video<?= $row['id'] ?>', event)">
            <svg viewBox="0 0 24 24">
                <path d="M7 14H5v5h5v-2H7v-3zm12 5h-5v-2h3v-3h2v5zM7 7h3V5H5v5h2V7zm12 3h-2V7h-3V5h5v5z"/>
            </svg>
        </div>
    </div>

</div>

<?php 
    }
} else { 
    echo "<p>Không có video</p>"; 
}
?>

</div>
</div>

<script>
// hover preview
document.querySelectorAll("video").forEach(video => {
    video.addEventListener("mouseenter", () => video.play());
    video.addEventListener("mouseleave", () => {
        video.pause();
        video.currentTime = 0;
    });
});

// fullscreen
function goFull(id, event){
    event.stopPropagation();

    const video = document.getElementById(id);

    video.muted = false;
    video.play();

    if(video.requestFullscreen){
        video.requestFullscreen();
    } 
    else if(video.webkitRequestFullscreen){
        video.webkitRequestFullscreen();
    } 
    else if(video.msRequestFullscreen){
        video.msRequestFullscreen();
    }
}

// pause khi thoát fullscreen
document.addEventListener("fullscreenchange", () => {
    if(!document.fullscreenElement){
        document.querySelectorAll("video").forEach(v => {
            v.pause();
            v.currentTime = 0;
        });
    }
});
</script>

</body>
</html>