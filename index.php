<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Video edit của tui</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:Poppins;
    background:#020617;
    color:#fff;
}

/* NAVBAR */
.navbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:15px 20px;
}

.logo{
    font-size:20px;
    font-weight:bold;
    color:#22c55e;
}

/* HERO */
.hero{
    height:60vh;
    background:url('https://images.unsplash.com/photo-1489599849927-2ee91cede3ba') center/cover;
    display:flex;
    align-items:flex-end;
    padding:25px;
}

.hero h1{
    font-size:36px;
    background:rgba(0,0,0,0.6);
    padding:12px 18px;
    border-radius:10px;
}

/* MOBILE HERO */
@media(max-width:768px){
    .hero{
        height:45vh;
        padding:15px;
    }

    .hero h1{
        font-size:22px;
        padding:10px 12px;
    }
}

/* SECTION */
.container{
    padding:20px;
}

.section-title{
    font-size:20px;
    margin:20px 0 10px;
}

/* ROW */
.row{
    display:flex;
    gap:12px;
    overflow-x:auto;
    padding-bottom:5px;
}
.row::-webkit-scrollbar{display:none;}

/* CATEGORY */
.card{
    min-width:160px;
    height:150px;
    border-radius:12px;
    overflow:hidden;
    position:relative;
    cursor:pointer;
    flex-shrink:0;
}

.card img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.card h3{
    position:absolute;
    bottom:8px;
    left:10px;
    font-size:14px;
}

/* VIDEO CARD */
.video-card{
    min-width:200px;
    height:130px;
    border-radius:12px;
    overflow:hidden;
    position:relative;
    cursor:pointer;
    transition:0.2s;
    flex-shrink:0;
}

.video-card img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* TẮT hover trên mobile */
@media(hover:hover){
    .video-card:hover{
        transform:scale(1.05);
    }
}

.video-card::after{
    content:"";
    position:absolute;
    bottom:0;
    width:100%;
    height:60%;
    background:linear-gradient(to top, rgba(0,0,0,0.9), transparent);
}

.video-card p{
    position:absolute;
    bottom:8px;
    left:10px;
    font-size:12px;
}

/* PLAY BUTTON */
.play-btn{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%, -50%);
    background:rgba(0,0,0,0.6);
    border:none;
    color:white;
    font-size:18px;
    padding:8px 12px;
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

#modalVideo{
    width:80%;
    height:80%;
    border-radius:10px;
}

/* MOBILE VIDEO FULL */
@media(max-width:768px){
    #modalVideo{
        width:95%;
        height:30%;
    }
}

/* MUSIC BUTTON */
#musicBtn{
    position:fixed;
    bottom:20px;
    right:20px;
    background:linear-gradient(135deg,#22c55e,#6366f1);
    border:none;
    padding:14px;
    border-radius:50%;
    font-size:20px;
    cursor:pointer;
}

/* TOOLTIP */
#musicTooltip{
    position:fixed;
    bottom:80px;
    right:20px;
    background:#111827;
    padding:8px 12px;
    border-radius:8px;
    font-size:13px;
    opacity:0;
    transform:translateY(10px);
    transition:0.3s;
}
#musicTooltip.show{
    opacity:1;
    transform:translateY(0);
}
</style>
</head>

<body>

<div class="navbar">
    <div class="logo">🎬 Video edit của tui</div>
</div>

<div class="hero">
    <h1>Thế giới video của tôi</h1>
</div>

<div class="container">
    <div class="section-title">🔥 Danh mục nổi bật</div>
    <div class="row" id="categories"></div>

    <div class="section-title">🆕 Video mới đăng</div>
    <div class="row" id="newVideos"></div>
   <div class="section-title">
    🎬 Tất cả video 
    <a href="all.php" style="font-size:12px; color:#22c55e; margin-left:10px;">
        Xem tất cả →
    </a>
</div>
<div class="row" id="allVideos"></div>
</div>

<!-- MODAL -->
<div id="videoModal">
    <iframe id="modalVideo"
        frameborder="0"
        allow="autoplay; encrypted-media"
        allowfullscreen>
    </iframe>
</div>

<!-- MUSIC -->
<iframe id="ytMusic" width="0" height="0" src="" allow="autoplay"></iframe>

<button id="musicBtn">🔇</button>
<div id="musicTooltip">🎧 Bạn muốn nghe nhạc? Click vào đây</div>

<script>
// LOAD CATEGORY
fetch("api.php")
.then(res => res.json())
.then(data => {
    let html = "";
    data.forEach(cat => {
        html += `
        <div class="card" onclick="location.href='category.php?id=${cat.id}'">
            <img src="${cat.thumbnail}">
            <h3>${cat.name}</h3>
        </div>`;
    });
    categories.innerHTML = html;
});

// LOAD VIDEO
fetch("api_videos_new.php")
.then(res => res.json())
.then(data => {
    let html = "";
    data.forEach(v => {
        let thumb = v.thumbnail || "https://via.placeholder.com/400x250";

        html += `
        <div class="video-card" onclick="openVideo('${v.url}')">
            <img src="${thumb}">
            <p>${v.title}</p>
            <button class="play-btn">▶</button>
        </div>`;
    });

    newVideos.innerHTML = html;
});

// TOOLTIP
const tooltip = document.getElementById("musicTooltip");
function showTooltip(text){
    tooltip.innerText = text;
    tooltip.classList.add("show");
    setTimeout(()=>tooltip.classList.remove("show"),4000);
}

// MUSIC
let isPlaying = false;
const iframe = document.getElementById("ytMusic");
const btn = document.getElementById("musicBtn");

function playMusic(){
    iframe.src = "https://www.youtube.com/embed/_yTP_L8fC-k?autoplay=1&loop=1&playlist=_yTP_L8fC-k&controls=0";
    isPlaying = true;
    btn.innerText = "🔊";
}

function stopMusic(){
    iframe.src = "";
    isPlaying = false;
    btn.innerText = "🔇";
}

btn.onclick = function(e){
    e.stopPropagation();
    isPlaying ? stopMusic() : playMusic();
};

// VIDEO
function openVideo(url){
    modalVideo.src = url + "?autoplay=1";
    videoModal.style.display = "flex";
    stopMusic();
}

videoModal.onclick = function(){
    this.style.display = "none";
    modalVideo.src = "";
};

// INIT
setTimeout(() => {
    showTooltip("🎧 Bật nhạc nền nè");
}, 1000);
</script>

</body>
</html>
