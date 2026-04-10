<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>📞 Bantuan Kasir Khawalicious</title>
<style>
body { font-family: Arial, sans-serif; background: #f0f8ff; margin: 0; padding: 20px; }
header { background: linear-gradient(90deg, #ff8cb8, #6ee3ff); padding: 20px; color: #fff; text-align: center; }
h1 { margin: 0; font-size: 24px; }
.container { margin-top: 30px; max-width: 600px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
label { display: block; margin: 12px 0 4px; font-weight: bold; }
input, textarea { width: 100%; padding: 10px; margin-bottom: 12px; border-radius: 6px; border: 1px solid #ccc; }
button { padding: 10px 20px; background: #ff69b4; color: #fff; border: none; border-radius: 6px; cursor: pointer; }
button:hover { background: #ff85c1; }
footer { text-align: center; margin-top: 40px; color: #777; font-size: 13px; }
</style>
</head>
<body>

<header>
  <h1>📞 Bantuan Kasir Khawalicious</h1>
  <p>Halo, <?= htmlspecialchars($username) ?>! Ada kendala? Hubungi kami di bawah 💌</p>
  <a href="dashboard.php" style="text-decoration:none; padding:8px 12px; background:#4CAF50; color:white; border-radius:5px;">🏠 Dashboard</a>
</header>

<div class="container">
  <form action="mailto:admin@example.com" method="post" enctype="text/plain">
    <label>Nama</label>
    <input type="text" name="nama" required placeholder="Masukkan nama Anda">

    <label>Email</label>
    <input type="email" name="email" required placeholder="Masukkan email Anda">

    <label>Pesan / Pertanyaan</label>
    <textarea name="pesan" rows="6" required placeholder="Tulis pertanyaan atau kendala Anda"></textarea>

    <button type="submit">Kirim</button>
  </form>
</div>

<footer>
  📞 Kasir Khawalicious — “Belanja Mudah, Untung Setiap Hari!” 💕
</footer>

</body>
</html>
