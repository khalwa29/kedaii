<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}

$koneksi = new mysqli("localhost", "root", "", "db_kasir");
if($koneksi->connect_error){ die("Koneksi gagal: ".$koneksi->connect_error); }

// Simpan perubahan jika form disubmit
if(isset($_POST['save'])){
    $koneksi->query("UPDATE pengaturan SET is_active=0");
    if(isset($_POST['menu'])){
        foreach($_POST['menu'] as $menu_name){
            $stmt = $koneksi->prepare("UPDATE pengaturan SET is_active=1 WHERE menu_name=?");
            $stmt->bind_param("s",$menu_name);
            $stmt->execute();
        }
    }
    $success = "Pengaturan berhasil disimpan!";
}

// Ambil data menu
$menu_result = $koneksi->query("SELECT * FROM pengaturan");
$menus = $menu_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>⚙️ Pengaturan Menu - Kasir Melwaa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

* { box-sizing: border-box; transition: all 0.3s ease; }

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #fff1f8, #e2f7ff);
    margin: 0;
    color: #333;
}

header {
    position: sticky;
    top: 0;
    background: linear-gradient(90deg, #ff8cb8, #6ee3ff);
    color: #fff;
    padding: 25px 40px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    z-index: 100;
}

header h1 { font-size: 22px; margin: 0; }
header span { font-size: 14px; opacity: 0.9; }

.container { padding: 30px 60px 70px; }

.card { border-radius: 20px; box-shadow: 0 8px 20px rgba(255,182,193,0.25); overflow: hidden; }

.card-header {
    background: linear-gradient(90deg, #ff8cb8, #6ee3ff);
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    text-align: center;
}

form label { display: block; margin: 12px 0; font-weight: 500; }
form input[type="checkbox"] { margin-right: 10px; }
button[type="submit"] {
    padding: 10px 20px;
    background: linear-gradient(90deg, #ff69b4, #77e3f0);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
}
button[type="submit"]:hover { opacity: 0.9; }

.success { color: green; margin-top: 10px; }

footer {
    background: #fff;
    text-align: center;
    padding: 15px;
    font-size: 13px;
    color: #777;
    border-top: 1px solid #ffd9ec;
    margin-top: 30px;
}
</style>
</head>
<body>

<header>
    <div>
        <h1>⚙️ Kedai Melwaa </h1>
        <span>Halo, <?= htmlspecialchars($_SESSION['username']) ?> — Atur menu dashboardmu</span>
    </div>
    <a href="dashboard_admin.php" style="text-decoration:none; padding:8px 12px; background:#4CAF50; color:white; border-radius:5px; font-weight:600;">🏠 Dashboard</a>
</header>

<div class="container">
  <div class="card shadow mt-3">
    <div class="card-header">Pengaturan Menu Dashboard</div>
    <div class="card-body">
        <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
        <form method="post">
            <?php foreach($menus as $menu): ?>
                <label>
                    <input type="checkbox" name="menu[]" value="<?= htmlspecialchars($menu['menu_name']) ?>"
                    <?= $menu['is_active'] ? 'checked' : '' ?>>
                    <?= htmlspecialchars($menu['menu_name']) ?>
                </label>
            <?php endforeach; ?>
            <button type="submit" name="save">Simpan</button>
        </form>
    </div>
  </div>
</div>

<footer>
    🍜🥤🍪 Kasir Melwaa — “Belanja Mudah, Untung Setiap Hari!” 💕
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
