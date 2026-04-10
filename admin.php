<?php 
session_start();
include 'config.php';

// LOGIN
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
<input type="password" name="password" placeholder="Nhập mật khẩu">
<button>Đăng nhập</button>
<p style="color:red"><?= $error ?? "" ?></p>
</form>
</div>
</body>
</html>
<?php exit; } }

// DELETE VIDEO
if(isset($_GET['delete_video'])){
    $id = intval($_GET['delete_video']);

    $res = $conn->query("SELECT url FROM videos WHERE id=$id");
    if($row = $res->fetch_assoc()){
        if(file_exists($row['url'])) unlink($row['url']);
    }

    $conn->query("DELETE FROM videos WHERE id=$id");
}

// ADD CATEGORY
if(isset($_POST['add_cat'])){
    $name = $conn->real_escape_string($_POST['category']);

    $file = $_FILES['thumbnail'];
    $newName = time()."_".$file['name'];
    $path = "uploads/categories/".$newName;

    if(move_uploaded_file($file['tmp_name'],$path)){
        $conn->query("INSERT INTO categories(name,thumbnail) VALUES('$name','$path')");
    }
}

// ADD VIDEO
if(isset($_POST['add_video'])){
    $title = $conn->real_escape_string($_POST['title']);
    $cat = intval($_POST['category_id']);

    $file = $_FILES['video'];
    $newName = time()."_".$file['name'];
    $path = "uploads/".$newName;

    if(move_uploaded_file($file['tmp_name'],$path)){
        $conn->query("INSERT INTO videos(title,url,category_id) VALUES('$title','$path','$cat')");
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Admin</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">

<style>
body{
    margin:0;
    font-family:Poppins;
    background:#020617;
    color:white;
}

header{
    padding:20px;
    text-align:center;
    background:linear-gradient(90deg,#6366f1,#22c55e);
}

.container{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:25px;
    padding:25px;
}

.box{
    background:#0f172a;
    padding:20px;
    border-radius:15px;
}

input,select{
    width:100%;
    padding:10px;
    margin-top:10px;
    border-radius:8px;
    border:none;
    background:#1e293b;
    color:white;
}

button{
    margin-top:10px;
    padding:10px;
    width:100%;
    border:none;
    border-radius:8px;
    background:#22c55e;
    color:white;
    cursor:pointer;
}

/* LIST VIDEO */
.item{
    background:#1e293b;
    padding:10px;
    border-radius:10px;
    margin-top:10px;
}

.item video{
    width:100%;
    border-radius:10px;
}

.delete{
    color:#ef4444;
    cursor:pointer;
}
</style>
</head>

<body>

<header>⚙️ ADMIN DASHBOARD</header>

<div class="container">

<!-- CATEGORY -->
<div class="box">
<h3>📂 Thêm danh mục</h3>

<form method="POST" enctype="multipart/form-data">
<input type="text" name="category" placeholder="Tên danh mục">
<input type="file" name="thumbnail" required>
<button name="add_cat">Thêm</button>
</form>
</div>

<!-- VIDEO -->
<div class="box">
<h3>🎬 Upload video</h3>

<form method="POST" enctype="multipart/form-data">
<input type="text" name="title" placeholder="Tiêu đề">
<input type="file" name="video" required>

<select name="category_id">
<option value=''>Chọn danh mục</option>
<?php
$res = $conn->query("SELECT * FROM categories");
while($c = $res->fetch_assoc()){
 echo "<option value='{$c['id']}'>{$c['name']}</option>";
}
?>
</select>

<button name="add_video">Upload</button>
</form>

<hr>

<!-- FILTER CATEGORY -->
<h4>📂 Chọn danh mục để xoá video</h4>

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

<!-- VIDEO LIST -->
<?php
if(isset($_GET['filter_cat'])){
    $cat = intval($_GET['filter_cat']);
    $res = $conn->query("SELECT * FROM videos WHERE category_id=$cat ORDER BY id DESC");

    if($res->num_rows > 0){
        while($v = $res->fetch_assoc()){
            echo "
            <div class='item'>
                <video src='{$v['url']}' controls></video>
                <p>{$v['title']}</p>
                <a class='delete' 
                   href='?filter_cat=$cat&delete_video={$v['id']}' 
                   onclick='return confirmDelete()'>
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
function confirmDelete(){
    return confirm("Bạn có chắc muốn xoá video này không?");
}
</script>

</body>
</html>