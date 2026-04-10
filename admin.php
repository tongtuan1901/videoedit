<?php 
session_start();
include 'config.php';

/* ================= LOGIN ================= */
if(!isset($_SESSION['admin'])){
    if(isset($_POST['password'])){
        if($_POST['password'] === "Tuan123456"){
            $_SESSION['admin'] = true;
        } else {
            $error = "Sai mật khẩu!";
        }
    }

    if(!isset($_SESSION['admin'])){
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body{
    background:#020617;
    color:white;
    font-family:Poppins;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.box{
    background:#0f172a;
    padding:30px;
    border-radius:15px;
    text-align:center;
}
input,button{
    padding:10px;
    margin-top:10px;
    width:100%;
    border:none;
    border-radius:8px;
}
button{background:#22c55e;color:white;}
</style>
</head>
<body>
<div class="box">
<h2>🔒 Admin Login</h2>
<form method="POST">
<input type="password" name="password" required>
<button>Đăng nhập</button>
<p style="color:red"><?= $error ?? "" ?></p>
</form>
</div>
</body>
</html>
<?php exit; } }

/* ================= DELETE ================= */
if(isset($_GET['delete_video'])){
    $id = intval($_GET['delete_video']);
    $conn->query("DELETE FROM videos WHERE id=$id");
}

if(isset($_GET['delete_cat'])){
    $id = intval($_GET['delete_cat']);

    $res = $conn->query("SELECT thumbnail FROM categories WHERE id=$id");
    if($row = $res->fetch_assoc()){
        if(file_exists($row['thumbnail'])) unlink($row['thumbnail']);
    }

    $conn->query("DELETE FROM videos WHERE category_id=$id");
    $conn->query("DELETE FROM categories WHERE id=$id");
}

/* ================= ADD CATEGORY ================= */
if(isset($_POST['add_cat'])){
    $name = $conn->real_escape_string($_POST['category']);

    $file = $_FILES['thumbnail'];
    $newName = time()."_".$file['name'];
    $path = "uploads/categories/".$newName;

    if(move_uploaded_file($file['tmp_name'],$path)){
        $conn->query("INSERT INTO categories(name,thumbnail) VALUES('$name','$path')");
    }
}

/* ================= VIDEO UTILS ================= */

function getYouTubeID($url){

    // watch?v=
    if(strpos($url,"watch?v=")){
        parse_str(parse_url($url, PHP_URL_QUERY), $vars);
        return $vars['v'] ?? "";
    }

    // youtu.be
    if(strpos($url,"youtu.be")){
        return basename(parse_url($url, PHP_URL_PATH));
    }

    // shorts
    if(strpos($url,"/shorts/")){
        $path = parse_url($url, PHP_URL_PATH);
        $parts = explode("/", trim($path,"/"));

        if(isset($parts[1])){
            return explode("?", $parts[1])[0]; // FIX query
        }
    }

    return "";
}

function convertVideo($url){

    // YouTube
    if(strpos($url,"youtube.com") || strpos($url,"youtu.be")){
        $id = getYouTubeID($url);
        if($id){
            return "https://www.youtube.com/embed/".$id;
        }
    }

    // TikTok
    if(strpos($url,"tiktok.com")){
        preg_match("/video\/(\d+)/", $url, $m);
        if(isset($m[1])){
            return "https://www.tiktok.com/embed/".$m[1];
        }
    }

    return $url;
}

function getThumbnail($url){

    if(strpos($url,"youtube.com") || strpos($url,"youtu.be")){
        $id = getYouTubeID($url);

        if($id){
            return "https://img.youtube.com/vi/".$id."/hqdefault.jpg";
        }
    }

    if(strpos($url,"tiktok.com")){
        return "https://via.placeholder.com/400x250?text=TikTok+Video";
    }

    return "https://via.placeholder.com/400x250?text=No+Thumbnail";
}

/* ================= ADD VIDEO ================= */
if(isset($_POST['add_video'])){

    if(empty($_POST['category_id'])){
        die("⚠️ Vui lòng chọn danh mục");
    }

    if(empty($_POST['video_url'])){
        die("⚠️ Vui lòng nhập link video");
    }

    $title = $conn->real_escape_string($_POST['title']);
    $cat = intval($_POST['category_id']);
    $raw_url = trim($_POST['video_url']);

    $video_url = convertVideo($raw_url);

    /* ===== FIX THUMBNAIL ===== */
    $thumbnail = "";

    // nếu upload ảnh
    if(!empty($_FILES['thumb_upload']['name'])){
        $file = $_FILES['thumb_upload'];
        $newName = time()."_".$file['name'];
        $path = "uploads/videos/".$newName;

        if(move_uploaded_file($file['tmp_name'], $path)){
            $thumbnail = $path;
        }
    }

    // nếu không upload → auto
    if(empty($thumbnail)){
        $thumbnail = getThumbnail($raw_url);
    }

    $video_url = $conn->real_escape_string($video_url);
    $thumbnail = $conn->real_escape_string($thumbnail);

    $conn->query("INSERT INTO videos(title,url,thumbnail,category_id) 
    VALUES('$title','$video_url','$thumbnail','$cat')");

    header("Location: ?filter_cat=".$cat);
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Admin</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{margin:0;font-family:Poppins;background:#020617;color:white;}
header{padding:20px;text-align:center;background:linear-gradient(90deg,#6366f1,#22c55e);}
.container{display:grid;grid-template-columns:1fr 1fr;gap:25px;padding:25px;}
.box{background:#0f172a;padding:20px;border-radius:15px;}
input,select{width:100%;padding:10px;margin-top:10px;border-radius:8px;border:none;background:#1e293b;color:white;}
button{margin-top:10px;padding:10px;width:100%;border:none;border-radius:8px;background:#22c55e;color:white;cursor:pointer;}
.cat-item{display:flex;align-items:center;gap:15px;background:#1e293b;padding:10px;border-radius:10px;margin-top:10px;}
.cat-item img{width:80px;height:80px;object-fit:cover;border-radius:10px;}
.video-item{background:#1e293b;padding:10px;border-radius:10px;margin-top:10px;}
.video-thumb{position:relative;cursor:pointer;}
.video-thumb img{width:100%;height:250px;object-fit:cover;border-radius:10px;}
.play-btn{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);font-size:40px;background:rgba(0,0,0,0.5);padding:10px 20px;border-radius:50%;}
.delete{color:#ef4444;cursor:pointer;}
iframe{width:100%;height:250px;border-radius:10px;}
</style>
</head>

<body>

<header>⚙️ ADMIN DASHBOARD</header>

<div class="container">

<!-- CATEGORY -->
<div class="box">
<h3>📂 Thêm danh mục</h3>

<form method="POST" enctype="multipart/form-data">
<input type="text" name="category" placeholder="Tên danh mục" required>
<input type="file" name="thumbnail" required>
<button name="add_cat">Thêm</button>
</form>

<hr>

<h4>📂 Danh sách danh mục</h4>

<?php
$res = $conn->query("SELECT * FROM categories ORDER BY id DESC");
while($c = $res->fetch_assoc()){
    echo "
    <div class='cat-item'>
        <img src='{$c['thumbnail']}' onerror=\"this.src='https://via.placeholder.com/80'\">
        <div><p>{$c['name']}</p></div>
        <a class='delete' href='?delete_cat={$c['id']}' onclick='return confirmDeleteCat()'>🗑</a>
    </div>";
}
?>
</div>

<!-- VIDEO -->
<div class="box">
<h3>🎬 Thêm video</h3>

<form method="POST" enctype="multipart/form-data">
<input type="text" name="title" placeholder="Tiêu đề">
<input type="text" name="video_url" placeholder="Link YouTube / Shorts / TikTok" required>
<input type="file" name="thumb_upload">
<select name="category_id" required>
<option value=''>Chọn danh mục</option>
<?php
$res = $conn->query("SELECT * FROM categories");
while($c = $res->fetch_assoc()){
 echo "<option value='{$c['id']}'>{$c['name']}</option>";
}
?>
</select>

<button name="add_video">Thêm video</button>
</form>

<hr>

<h4>📂 Chọn danh mục</h4>

<form method="GET">
<select name="filter_cat" onchange="this.form.submit()">
<option value="">-- Chọn danh mục --</option>
<?php
$res = $conn->query("SELECT * FROM categories");
while($c = $res->fetch_assoc()){
 $selected = (isset($_GET['filter_cat']) && $_GET['filter_cat']==$c['id']) ? "selected" : "";
 echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
}
?>
</select>
</form>

<?php
if(isset($_GET['filter_cat'])){
    $cat = intval($_GET['filter_cat']);
    $res = $conn->query("SELECT * FROM videos WHERE category_id=$cat ORDER BY id DESC");

    if($res->num_rows > 0){
        while($v = $res->fetch_assoc()){
            echo "
            <div class='video-item'>
                <div class='video-thumb' onclick=\"playVideo(this, '{$v['url']}')\">
                    <img src='{$v['thumbnail']}' onerror=\"this.src='https://via.placeholder.com/400x250?text=No+Image'\">
                    <div class='play-btn'>▶</div>
                </div>
                <p>{$v['title']}</p>
                <a class='delete' href='?filter_cat=$cat&delete_video={$v['id']}' onclick='return confirmDeleteVideo()'>
                   🗑 Xoá
                </a>
            </div>";
        }
    } else {
        echo "<p>Không có video</p>";
    }
}
?>

</div>

</div>

<script>
function playVideo(el, url){
    el.innerHTML = `<iframe src="${url}" frameborder="0" allowfullscreen></iframe>`;
}
function confirmDeleteVideo(){ return confirm("Bạn có chắc muốn xoá video này không?"); }
function confirmDeleteCat(){ return confirm("Xoá danh mục sẽ xoá luôn toàn bộ video bên trong. Bạn chắc chưa?"); }
</script>

</body>
</html>
