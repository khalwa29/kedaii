<?php
session_start();

// Jika belum ada keranjang, buat kosong
$keranjang = $_SESSION['keranjang'] ?? [];
$total = 0;

// Hitung total belanja
foreach ($keranjang as $item) {
    $total += $item['harga'] * $item['qty'];
}

// Hapus item dari keranjang
if (isset($_GET['hapus'])) {
    $hapusId = intval($_GET['hapus']);
    foreach ($keranjang as $key => $item) {
        if ($item['id_produk'] == $hapusId) {
            unset($keranjang[$key]);
            $_SESSION['keranjang'] = array_values($keranjang);
            header("Location: keranjang.php?msg=" . urlencode($item['nama_produk'] . " telah dihapus dari keranjang."));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keranjang Belanja - Kedai Melwaa 💕</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #fff1f8, #e2f7ff);
    margin: 0;
    padding: 20px;
}
h1 {
    text-align: center;
    color: #ff4b9d;
    margin-bottom: 10px;
}
.msg {
    text-align: center;
    background: #e0ffe9;
    color: #2d7a46;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 20px;
    width: 60%;
    margin-left: auto;
    margin-right: auto;
}
.table-container {
    width: 90%;
    margin: 0 auto;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(255,182,193,0.3);
    overflow-x: auto;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th, td {
    padding: 12px 15px;
    text-align: center;
    border-bottom: 1px solid #eee;
}
th {
    background: linear-gradient(90deg, #ff8cb8, #6ee3ff);
    color: white;
}
tr:hover {
    background-color: #fdf1f7;
}
img {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
}
.total {
    text-align: right;
    padding: 15px;
    font-size: 18px;
    color: #444;
    font-weight: 600;
}
.btn {
    background: linear-gradient(90deg, #ff69b4, #77e3f0);
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}
.btn:hover {
    transform: scale(1.05);
}
.btn-danger {
    background: #ff6b81;
}
.btn-danger:hover {
    background: #ff7b8d;
}
footer {
    text-align: center;
    margin-top: 25px;
    color: #777;
}
.actions {
    display: flex;
    gap: 10px;
    justify-content: center;
}
a.back {
    display: inline-block;
    margin-top: 25px;
    text-decoration: none;
    color: white;
    background: #ff69b4;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
}
a.back:hover {
    background: #ff85c1;
}
</style>
</head>
<body>

<h1>🛍️ Keranjang Belanja Kamu 💕</h1>

<?php if (isset($_GET['msg'])): ?>
    <div class="msg"><?= htmlspecialchars($_GET['msg']) ?></div>
<?php endif; ?>

<div class="table-container">
    <?php if (empty($keranjang)): ?>
        <p style="text-align:center; padding:20px;">Keranjang kamu masih kosong 😢<br><br>
        <a href="beli.php" class="back">🍽️ Lihat Menu</a></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($keranjang as $item): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><img src="uploads/<?= htmlspecialchars($item['foto']) ?>" alt="<?= htmlspecialchars($item['nama_produk']) ?>"></td>
                        <td><?= htmlspecialchars($item['nama_produk']) ?></td>
                        <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td><?= $item['qty'] ?></td>
                        <td><b>Rp <?= number_format($item['harga'] * $item['qty'], 0, ',', '.') ?></b></td>
                        <td class="actions">
                            <a href="keranjang.php?hapus=<?= $item['id_produk'] ?>" class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="total">Total: <span style="color:#ff4b9d;">Rp <?= number_format($total, 0, ',', '.') ?></span></div>
        <div style="text-align:center; margin-bottom:20px;">
            <button class="btn" onclick="alert('Fitur pembayaran belum aktif 💳')">Bayar Sekarang</button>
        </div>
    <?php endif; ?>
</div>

<a href="beli.php" class="back">⬅ Kembali ke Menu</a>

<footer>💖 Kedai Melwaa — “Maniskan Harimu Setiap Saat.” 💖</footer>

</body>
</html>
