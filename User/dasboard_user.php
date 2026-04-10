<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}

$koneksi = new mysqli("localhost", "root", "", "db_kasir");
if($koneksi->connect_error){ die("Koneksi gagal: ".$koneksi->connect_error); }

$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>🍜 Dashboard Pembeli - Kasir Khawalicious</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    
    * {
      box-sizing: border-box;
      transition: all 0.3s ease;
    }

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

    header h1 {
      font-size: 22px;
      margin: 0;
    }

    header span {
      font-size: 14px;
      opacity: 0.9;
    }

    .logout {
      background: #fff;
      color: #ff69b4;
      padding: 8px 18px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
    }

    .logout:hover {
      background: #ffe0ef;
      transform: scale(1.05);
    }

    .welcome {
      text-align: center;
      padding: 40px 15px 10px;
      font-size: 22px;
      color: #555;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 25px;
      padding: 30px 60px 70px;
    }

    .card {
      background: white;
      border-radius: 20px;
      padding: 25px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(255, 182, 193, 0.25);
      position: relative;
      overflow: hidden;
    }

    .card::before {
      content: "";
      position: absolute;
      width: 150%;
      height: 150%;
      background: linear-gradient(135deg, rgba(255,105,180,0.15), rgba(119,227,240,0.15));
      top: -25%;
      left: -25%;
      transform: rotate(10deg);
      transition: 0.4s ease;
    }

    .card:hover::before {
      transform: rotate(0deg);
      background: linear-gradient(135deg, rgba(255,105,180,0.25), rgba(119,227,240,0.25));
    }

    .icon {
      font-size: 48px;
      margin-bottom: 15px;
      position: relative;
      z-index: 2;
    }

    .card h3 {
      font-size: 18px;
      color: #ff69b4;
      z-index: 2;
      position: relative;
    }

    .card p {
      font-size: 14px;
      color: #666;
      margin: 10px 0 15px;
      z-index: 2;
      position: relative;
    }

    .card a {
      display: inline-block;
      background: linear-gradient(90deg, #ff69b4, #77e3f0);
      color: #fff;
      padding: 8px 14px;
      border-radius: 8px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
      z-index: 2;
      position: relative;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 25px rgba(255, 182, 193, 0.4);
    }

    footer {
      background: #fff;
      text-align: center;
      padding: 15px;
      font-size: 13px;
      color: #777;
      border-top: 1px solid #ffd9ec;
    }
  </style>
</head>
<body>
  <header>
    <div>
      <h1>🍜 Khawalicious Cafe & Snack</h1>
      <span>Selamat datang, <?= htmlspecialchars($username) ?>! 💖</span>
    </div>
    <a href="logout.php" class="logout">Logout 🚪</a>
  </header>

  <div class="welcome">✨ Pilih menu di bawah untuk mulai memesan 🍰</div>

  <div class="grid">
    <div class="card">
      <div class="icon">📋</div>
      <h3>Lihat Menu</h3>
      <p>Lihat semua daftar makanan dan minuman yang tersedia hari ini.</p>
      <a href="menu.php">Lihat Menu</a>
    </div>

    <div class="card">
      <div class="icon">🛍️</div>
      <h3>Pesan Sekarang</h3>
      <p>Pilih makanan favoritmu dan buat pesanan langsung dari sini!</p>
      <a href="pesan.php">Pesan Sekarang</a>
    </div>

    <div class="card">
      <div class="icon">📦</div>
      <h3>Pesanan Saya</h3>
      <p>Cek status pesanan kamu yang sedang diproses.</p>
      <a href="pesanan_saya.php">Lihat Pesanan</a>
    </div>

    <div class="card">
      <div class="icon">📞</div>
      <h3>Hubungi Kami</h3>
      <p>Butuh bantuan atau saran? Hubungi admin toko.</p>
      <a href="bantuan.php">Hubungi</a>
    </div>
  </div>

  <footer>
    🍜 Khawalicious — “Belanja Nikmat, Harga Hemat!” 💕
  </footer>
</body>
</html>
