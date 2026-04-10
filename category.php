<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Danh sách video</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
*{ box-sizing:border-box; }

body{
    margin:0;
    font-family:Poppins;
    background:#020617;
    color:white;
}

/* HEADER */
header{
    height:30vh;
    background:url('https://images.unsplash.com/photo-1524985069026-dd778a71c7b4') center/cover;
    display:flex;
    align-items:center;
    justify-content:center;
    text-align:center;
    padding:10px;
}
header h1{
    background:rgba(0,0,0,0.6);
    padding:12px 20px;
    border-radius:12px;
    font-size:28px;
}

/* MOBILE HEADER */
@media(max-width:600px){
    header{
        height:22vh;
    }
    header h1{
        font-size:20px;
        padding:10px 15px;
    }
}

/* BACK BUTTON */
.back-btn{
    position:fixed;
    top:15px;
    left:15px;
    z-index:1000;
}
.back-btn a{
    background:rgba(0,0,0,0.5);
    padding:8px 12px;
    border-radius:10px;
    color:white;
    text-decoration:none;
    font-size:14px;
}

/* CONTAINER */
.container{
    padding:20px;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(5, 1fr);
    gap:18px;
}

@media(max-width:1024px){
    .grid{ grid-template-columns:repeat(3, 1fr); }
}
@media(max-width:600px){
    .grid{ grid-template-columns:repeat(2, 1fr); gap:12px; }
}

/* CARD */
.card{
    position:relative;
    border-radius:16px;
    overflow:hidden;
    background:#0f172a;
    cursor:pointer;
    transition:0.25s;
    box-shadow:0 6px 15px rgba(0,0,0,0.3);
}

.card:hover{
    transform:translateY(-5px) scale(1.02);
}

/* THUMBNAIL 16:9 */
.card img{
    width:100%;
    aspect-ratio:16/9;
    object-fit:cover;
}

/* OVERLAY */
.overlay{
    position:absolute;
    bottom:0;
    width:100%;
    padding:10px;
    background:linear-gradient(to top, rgba(0,0,0,0.95), transparent);
}

.overlay h3{
    margin:0;
    font-size:14px;
    line-height:1.3;
}

/* PLAY BUTTON */
.play-btn{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%, -50%);
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(6px);
    border:none;
    color:white;
    font-size:18px;
    padding:10px 14px;
    border-radius:50%;
}

/* MODAL */
#videoModal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:black;
    z-index:999;
    justify-content:center;
    align-items:center;
}

/* VIDEO */
#modalVideo{
    width:80%;
    height:80%;
    border-radius:12px;
}

/* MOBILE VIDEO FULL */
@media(max-width:600px){
    #modalVideo{
        width:100%;
        height:40%;
        border-radius:0;
    }
}

</style>
</head>
<body>

<div class="back-btn">
    <a href="index.php">← Trang chủ</a>
</div>

<header>
<h1>🎬 Danh sách video</h1>
</header>

<div class="container">

<?php
$id = intval($_GET['id']);
$sql = "SELECT * FROM videos WHERE category_id=$id ORDER BY id DESC";
$result = $conn->query($sql);
?>

<div class="grid">

<?php
if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){

        $thumb = !empty($row['thumbnail']) 
            ? $row['thumbnail'] 
            : "https://via.placeholder.com/400x250?text=No+Thumbnail";
?>

<div class="card" onclick="openVideo('<?= $row['url'] ?>')">
    <img src="<?= $thumb ?>">
    
    <div class="overlay">
        <h3><?= $row['title'] ?></h3>
    </div>

    <button class="play-btn">▶</button>
</div>

<?php 
    }
} else { 
    echo "<p>Không có video</p>"; 
}
?>

</div>
</div>

<!-- MODAL -->
<div id="videoModal">
    <iframe id="modalVideo"
        frameborder="0"
        allow="autoplay; encrypted-media"
        allowfullscreen>
    </iframe>
</div>

<script>
function openVideo(url){
    const modal = document.getElementById("videoModal");
    const video = document.getElementById("modalVideo");

    video.src = url + "?autoplay=1";
    modal.style.display = "flex";
}

document.getElementById("videoModal").onclick = function(){
    this.style.display = "none";
    document.getElementById("modalVideo").src = "";
};
</script>

</body>
</html>
