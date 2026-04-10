<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Tất cả video</title>

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
    height:25vh;
    background:url('https://images.unsplash.com/photo-1492724441997-5dc865305da7') center/cover;
    display:flex;
    align-items:center;
    justify-content:center;
    position:relative;
}

header::after{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(to bottom, rgba(0,0,0,0.4), rgba(2,6,23,1));
}

header h1{
    position:relative;
    background:rgba(0,0,0,0.6);
    padding:12px 20px;
    border-radius:12px;
    z-index:2;
}

/* BACK */
.back-btn{
    position:fixed;
    top:15px;
    left:15px;
    z-index:10;
}
.back-btn a{
    background:rgba(0,0,0,0.5);
    padding:8px 12px;
    border-radius:10px;
    color:white;
    text-decoration:none;
}

/* GRID */
.container{
    padding:20px;
}
.grid{
    display:grid;
    grid-template-columns:repeat(4, 1fr);
    gap:20px;
}

/* Tablet */
@media(max-width:1024px){
    .grid{ grid-template-columns:repeat(3, 1fr); }
}

/* Mobile */
@media(max-width:600px){
    .grid{ grid-template-columns:repeat(2, 1fr); }
}

/* Mobile nhỏ */
@media(max-width:400px){
    .grid{ grid-template-columns:1fr; }
}

@media(max-width:1024px){
    .grid{ grid-template-columns:repeat(3, 1fr); }
}
@media(max-width:600px){
    .grid{ grid-template-columns:repeat(2, 1fr); }
}

/* CARD */
.card{
    position:relative;
    border-radius:16px;
    overflow:hidden;
    cursor:pointer;
    transition:0.3s;
    background:#0f172a;
    box-shadow:0 10px 25px rgba(0,0,0,0.4);
}

.card:hover{
    transform:translateY(-8px) scale(1.03);
    box-shadow:0 20px 40px rgba(0,0,0,0.6);
}

.card img{
    width:100%;
    aspect-ratio:16/9;
    object-fit:cover;
    transition:0.3s;
}

.card:hover img{
    transform:scale(1.08);
}

/* OVERLAY */
.overlay{
    position:absolute;
    bottom:0;
    width:100%;
    padding:12px;
    background:linear-gradient(to top, rgba(0,0,0,0.95), transparent);
}

.overlay p{
    margin:0;
    font-size:13px;
    font-weight:500;
}

.play-btn{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%) scale(0.9);
    background:rgba(255,0,0,0.85); /* đỏ */
    backdrop-filter:blur(6px);
    border:none;
    color:white;
    padding:12px 16px;
    border-radius:50%;
    opacity:0;
    transition:0.3s;
}

/* Hover hiện + sáng hơn */
.card:hover .play-btn{
    opacity:1;
    transform:translate(-50%,-50%) scale(1.1);
    background:#ff0000; /* đỏ đậm */
}


/* MODAL */
#videoModal{
    display:none;
    position:fixed;
    width:100%;
    height:100%;
    background:black;
    top:0;
    left:0;
    justify-content:center;
    align-items:center;
    z-index:999;
}

#modalVideo{
    width:80%;
    height:80%;
}

/* PAGINATION */
.pagination{
    display:flex;
    justify-content:center;
    align-items:center;
    gap:6px;
    margin-top:40px;
}

.pagination a{
    padding:8px 12px;
    border-radius:8px;
    text-decoration:none;
    background:transparent;
    color:#9ca3af;
    font-size:14px;
    transition:0.25s;
}

/* Hover nhẹ */
.pagination a:hover{
    background:#1f2937;
    color:white;
}

/* Trang hiện tại */
.pagination .active{
    background:white;
    color:black;
    font-weight:600;
}

</style>
</head>

<body>

<div class="back-btn">
    <a href="index.php">← Trang chủ</a>
</div>

<header>
    <h1>🎬 Tất cả video</h1>
</header>

<div class="container">
<div class="grid">

<?php
$limit = 12;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// tổng số video
$total_sql = "SELECT COUNT(*) as total FROM videos";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_videos = $total_row['total'];

$total_pages = ceil($total_videos / $limit);

// query chính
$sql = "SELECT * FROM videos ORDER BY id DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if($result && $result->num_rows > 0){
    while($row = $result->fetch_assoc()){
        $thumb = !empty($row['thumbnail']) 
            ? $row['thumbnail'] 
            : "https://via.placeholder.com/400x250";
?>

<div class="card" onclick="openVideo('<?= $row['url'] ?>')">
    <img src="<?= $thumb ?>">
    <div class="overlay">
        <p><?= $row['title'] ?></p>
    </div>
    <button class="play-btn">▶</button>
</div>

<?php } } ?>

</div>

<!-- PAGINATION -->
<div class="pagination">

<?php if($page > 1){ ?>
    <a href="?page=<?= $page-1 ?>">←</a>
<?php } ?>

<?php for($i = 1; $i <= $total_pages; $i++){ ?>
    <a href="?page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>">
        <?= $i ?>
    </a>
<?php } ?>

<?php if($page < $total_pages){ ?>
    <a href="?page=<?= $page+1 ?>">→</a>
<?php } ?>

</div>

</div>

<!-- MODAL -->
<div id="videoModal">
    <iframe id="modalVideo" allow="autoplay" frameborder="0"></iframe>
</div>

<script>
function openVideo(url){
    modalVideo.src = url + "?autoplay=1";
    videoModal.style.display = "flex";
}

videoModal.onclick = function(){
    this.style.display = "none";
    modalVideo.src = "";
};
</script>

</body>
</html>
