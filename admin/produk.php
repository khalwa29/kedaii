<?php
include '../koneksi.php';
session_start();

// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "db_kasir";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("<h3 style='color:pink;text-align:center;'>Koneksi gagal 💔 : " . $conn->connect_error . "</h3>");
}

$conn->set_charset("utf8");


// === TAMBAH PRODUK ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $kode_produk = $_POST['kode_produk'];
    $nama_produk = $_POST['nama_produk'];
    $kategori    = $_POST['kategori'];
    $harga_beli  = $_POST['harga_beli'];
    $harga_jual  = $_POST['harga_jual'];
    $stok        = $_POST['stok'];
    $satuan      = $_POST['satuan'];

    // Upload foto (jika ada)
    $foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $nama_file = time() . "_" . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $nama_file;
        
        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (in_array($_FILES['foto']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $foto = $nama_file;
            }
        } else {
            echo "<script>alert('❌ Format file harus JPG, PNG, atau GIF!');</script>";
        }
    }

    // Simpan ke tb_produk
    $stmt = $conn->prepare("INSERT INTO tb_produk (kode_produk, nama_produk, kategori, harga_beli, harga_jual, stok, satuan, foto)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) die("❌ Query gagal disiapkan: " . $conn->error);

    $stmt->bind_param("sssddiss", $kode_produk, $nama_produk, $kategori, $harga_beli, $harga_jual, $stok, $satuan, $foto);
    if (!$stmt->execute()) {
        die("❌ Gagal menyimpan produk: " . $stmt->error);
    }

    // Tambah ke tabel menu juga
    $stmt2 = $conn->prepare("INSERT INTO menu (menu, harga, foto) VALUES (?, ?, ?)");
    if ($stmt2) {
        $stmt2->bind_param("sds", $nama_produk, $harga_jual, $foto);
        $stmt2->execute();
        $stmt2->close();
    }
    
    $stmt->close();
    echo "<script>alert('✨ Produk baru berhasil ditambahkan 💕'); window.location='produk.php';</script>";
    exit;
}

// === HAPUS PRODUK ===
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    
    // Ambil nama foto sebelum dihapus
    $result = $conn->query("SELECT foto FROM tb_produk WHERE id_produk='$id'");
    if ($result && $row = $result->fetch_assoc()) {
        if ($row['foto'] && file_exists("uploads/" . $row['foto'])) {
            unlink("uploads/" . $row['foto']); // Hapus file foto
        }
    }
    
    $conn->query("DELETE FROM tb_produk WHERE id_produk='$id'");
    echo "<script>alert('🗑️ Produk berhasil dihapus!'); window.location='produk.php';</script>";
    exit;
}

// === EDIT PRODUK (ambil data) ===
$edit = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit = $conn->query("SELECT * FROM tb_produk WHERE id_produk='$id'")->fetch_assoc();
}

// === UPDATE PRODUK ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id          = $_POST['id_produk'];
    $kode_produk = $_POST['kode_produk'];
    $nama_produk = $_POST['nama_produk'];
    $kategori    = $_POST['kategori'];
    $harga_beli  = $_POST['harga_beli'];
    $harga_jual  = $_POST['harga_jual'];
    $stok        = $_POST['stok'];
    $satuan      = $_POST['satuan'];
    $foto        = $_POST['foto_lama']; // Default pakai foto lama

    // Cek foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $nama_file = time() . "_" . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $nama_file;

        // Validasi tipe file
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (in_array($_FILES['foto']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                // Hapus foto lama jika ada
                if ($foto && file_exists("uploads/" . $foto)) {
                    unlink("uploads/" . $foto);
                }
                $foto = $nama_file;
            }
        } else {
            echo "<script>alert('❌ Format file harus JPG, PNG, atau GIF!');</script>";
        }
    }

    $stmt = $conn->prepare("UPDATE tb_produk SET kode_produk=?, nama_produk=?, kategori=?, harga_beli=?, harga_jual=?, stok=?, satuan=?, foto=? WHERE id_produk=?");
    if (!$stmt) die("❌ Query gagal disiapkan: " . $conn->error);
    
    $stmt->bind_param("sssddissi", $kode_produk, $nama_produk, $kategori, $harga_beli, $harga_jual, $stok, $satuan, $foto, $id);
    if (!$stmt->execute()) {
        die("❌ Gagal update produk: " . $stmt->error);
    }
    
    $stmt->close();
    echo "<script>alert('✨ Produk berhasil diperbarui!'); window.location='produk.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manajemen Produk 💕</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #ffe6f2, #e0f7fa);
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 30px 20px;
  min-height: 100vh;
}
.container {
  background: #fff;
  padding: 30px 40px;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(255,182,193,0.25);
  width: 100%;
  max-width: 450px;
  margin-bottom: 40px;
}
h2 {
  color: #ff69b4;
  text-align: center;
  margin-bottom: 20px;
}
label {
  font-weight: 600;
  color: #555;
  display: block;
  margin-top: 12px;
  font-size: 14px;
}
input, select {
  width: 100%;
  padding: 10px;
  border: 2px solid #ffb6c1;
  border-radius: 10px;
  margin-top: 5px;
  outline: none;
  font-size: 14px;
}
input:focus, select:focus {
  border-color: #ff69b4;
  box-shadow: 0 0 8px rgba(255,105,180,0.3);
}
button {
  margin-top: 20px;
  width: 100%;
  padding: 12px;
  background-color: #ff69b4;
  border: none;
  border-radius: 12px;
  color: white;
  font-size: 16px;
  cursor: pointer;
  font-weight: 600;
  transition: 0.3s;
}
button:hover { 
  background-color: #ff85c1;
  transform: scale(1.02);
}
a.kembali {
  display: inline-block;
  margin-bottom: 15px;
  background: #00bcd4;
  color: white;
  padding: 8px 15px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  transition: 0.3s;
}
a.kembali:hover { 
  background: #0097a7;
  transform: scale(1.05);
}
.table-wrapper {
  width: 100%;
  max-width: 1200px;
  overflow-x: auto;
  margin-top: 20px;
}
table {
  border-collapse: collapse;
  width: 100%;
  background: #fff;
  box-shadow: 0 8px 20px rgba(255,182,193,0.25);
  border-radius: 15px;
  overflow: hidden;
}
th, td {
  padding: 12px;
  text-align: center;
  font-size: 14px;
}
th { 
  background-color: #ff69b4;
  color: #fff;
  font-weight: 600;
}
tr:nth-child(even) { background-color: #fdf1f7; }
tr:hover { background-color: #ffe6f2; }
img {
  border-radius: 10px;
  width: 60px;
  height: 60px;
  object-fit: cover;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
a.btn {
  text-decoration: none;
  padding: 6px 12px;
  border-radius: 8px;
  color: white;
  font-size: 13px;
  margin: 0 3px;
  display: inline-block;
  transition: 0.3s;
}
a.edit { background: #00bcd4; }
a.hapus { background: #f44336; }
a.edit:hover { 
  background: #0097a7;
  transform: scale(1.05);
}
a.hapus:hover { 
  background: #d32f2f;
  transform: scale(1.05);
}
.empty-state {
  text-align: center;
  padding: 40px;
  color: #999;
  font-style: italic;
}
</style>
</head>
<body>

<div class="container">
  <a href="dashboard_admin.php" class="kembali">⬅️ Kembali ke Dashboard</a>
  <h2><?= $edit ? 'Edit Produk ✏️' : 'Tambah Produk Baru 💖' ?></h2>
  <form method="POST" enctype="multipart/form-data">
    <?php if ($edit): ?>
      <input type="hidden" name="id_produk" value="<?= $edit['id_produk'] ?>">
      <input type="hidden" name="foto_lama" value="<?= $edit['foto'] ?>">
    <?php endif; ?>

    <label>Kode Produk *</label>
    <input type="text" name="kode_produk" value="<?= $edit['kode_produk'] ?? '' ?>" required>

    <label>Nama Produk *</label>
    <input type="text" name="nama_produk" value="<?= $edit['nama_produk'] ?? '' ?>" required>

    <label>Kategori *</label>
    <select name="kategori" required>
      <option value="">-- Pilih Kategori --</option>
      <?php
      $kategori_list = ['Makanan', 'Minuman', 'Snack', 'Lainnya'];
      foreach ($kategori_list as $kat) {
          $selected = ($edit && $edit['kategori'] == $kat) ? 'selected' : '';
          echo "<option value='$kat' $selected>$kat</option>";
      }
      ?>
    </select>

    <label>Harga Beli *</label>
    <input type="number" name="harga_beli" value="<?= $edit['harga_beli'] ?? '' ?>" min="0" required>

    <label>Harga Jual *</label>
    <input type="number" name="harga_jual" value="<?= $edit['harga_jual'] ?? '' ?>" min="0" required>

    <label>Stok *</label>
    <input type="number" name="stok" value="<?= $edit['stok'] ?? '' ?>" min="0" required>

    <label>Satuan *</label>
    <select name="satuan" required>
      <option value="">-- Pilih Satuan --</option>
      <?php
      $satuan_list = ['pcs', 'botol', 'bungkus', 'cup', 'porsi', 'kg', 'liter'];
      foreach ($satuan_list as $sat) {
          $selected = ($edit && $edit['satuan'] == $sat) ? 'selected' : '';
          echo "<option value='$sat' $selected>$sat</option>";
      }
      ?>
    </select>

  
    <input type="file" name="foto" accept="image/*">
    <?php if ($edit && $edit['foto']): ?>
      <div style="margin-top:10px;">
        <img src="uploads/<?= $edit['foto'] ?>" width="100">
        <p style="font-size:12px; color:#999;">Foto saat ini</p>
      </div>
    <?php endif; ?>

    <button type="submit" name="<?= $edit ? 'update' : 'tambah' ?>">
      <?= $edit ? '✏️ Update Produk' : '💾 Simpan Produk' ?>
    </button>
  </form>
</div>

<h2 style="color:#ff69b4;">📦 Daftar Produk</h2>
<div class="table-wrapper">
<table>
  <thead>
  <tr>
    <th>No</th>
    <th>Kode</th>
    <th>Nama</th>
    <th>Kategori</th>
    <th>Harga Jual</th>
    <th>Stok</th>
    <th>Foto</th>
    <th>Aksi</th>
  </tr>
  </thead>
  <tbody>
  <?php
  $no = 1;

  $result = $koneksi->query("SELECT * FROM tb_produk ORDER BY id_produk DESC");
  if ($result->num_rows > 0) 

  $result = $conn->query("SELECT * FROM tb_produk ORDER BY id_produk DESC");
  if ($result && $result->num_rows > 0) {

      while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$no}</td>
                  <td>{$row['kode_produk']}</td>
                  <td>{$row['nama_produk']}</td>
                  <td>{$row['kategori']}</td>
                  <td>Rp " . number_format($row['harga_jual'], 0, ',', '.') . "</td>
                  <td>{$row['stok']} {$row['satuan']}</td>
                  <td>";
          echo $row['foto'] ? "<img src='uploads/{$row['foto']}' alt='Foto Produk'>" : "<em style='color:#999;'>Tidak ada</em>";
          echo "</td>
                <td>
                  <a href='?edit={$row['id_produk']}' class='btn edit'>✏️ Edit</a>
                  <a href='?hapus={$row['id_produk']}' class='btn hapus' onclick='return confirm(\"⚠️ Yakin ingin hapus produk ini?\")'>🗑️ Hapus</a>
                </td>
              </tr>";
          $no++;
      }
  } else {
      echo "<tr><td colspan='8' class='empty-state'>😿 Belum ada produk ditambahkan</td></tr>";
  }
  ?>
  </tbody>
</table>
</div>

</body>
</html>
<?php $conn->close(); ?>