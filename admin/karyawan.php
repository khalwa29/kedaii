<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION["username"];

$koneksi = new mysqli("localhost", "root", "", "db_kasir");

// Tambah user
if (isset($_POST['tambah'])) {
    $username_input = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = $koneksi->query("INSERT INTO users (username, email, password) 
                              VALUES ('$username_input', '$email', '$password')");

    if ($query) {
        echo "<script>alert('User berhasil ditambahkan!');document.location='karyawan.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah user!');</script>";
    }
}

// Hapus user
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM users WHERE id='$id'");
    echo "<script>alert('User berhasil dihapus!');document.location='karyawan.php';</script>";
}

// Ambil data user
$data = $koneksi->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Users - Kasir Khawalicious</title>
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

.table thead { background: linear-gradient(90deg, #ff69b4, #77e3f0); color: #fff; text-align: center; }
.table tbody tr td { vertical-align: middle; text-align: center; }

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
        <h1>🍜🥤🍪 Kasir Melwaa 🍜🥤🍪</h1>
        <span>Halo, <?= htmlspecialchars($username) ?> — Kelola data karyawan hari ini 💼</span>
    </div>
    <a href="dashboard_admin.php" style="text-decoration:none; padding:8px 12px; background:#4CAF50; color:white; border-radius:5px; font-weight:600;">🏠 Dashboard</a>
</header>

<div class="container">
  <div class="card shadow mt-3">
    <div class="card-header">Data Users</div>
    <div class="card-body">

      <!-- Tombol Tambah -->
      <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">+ Tambah User</button>

      <!-- Tabel Data -->
      <div class="table-responsive">
      <table class="table table-bordered table-striped">
          <thead>
              <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Dibuat Pada</th>
                  <th>Aksi</th>
              </tr>
          </thead>
          <tbody>
          <?php 
          if ($data->num_rows > 0) {
              while ($row = $data->fetch_assoc()) { ?>
                  <tr>
                      <td><?= $row['id']; ?></td>
                      <td><?= htmlspecialchars($row['username']); ?></td>
                      <td><?= htmlspecialchars($row['email']); ?></td>
                      <td><?= $row['created_at']; ?></td>
                      <td>
                          <a href="?hapus=<?= $row['id']; ?>" 
                             onclick="return confirm('Yakin ingin menghapus user ini?')"
                             class="btn btn-sm btn-danger">Hapus</a>
                      </td>
                  </tr>
          <?php } 
          } else {
              echo '<tr><td colspan="5" class="text-center text-muted">Belum ada data user.</td></tr>';
          }
          ?>
          </tbody>
      </table>
      </div>

    </div>
  </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5 class="modal-title">Tambah User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
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
