<?php
session_start();

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "db_kasir");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Hapus session keranjang sebelumnya jika datang dari tombol "Belanja Lagi"
if (isset($_GET['belanja_lagi'])) {
    unset($_SESSION['keranjang']);
    echo "<script>alert('🛒 Mari mulai belanja lagi!');</script>";
}

// Ambil semua data produk
$result = $koneksi->query("SELECT * FROM tb_produk WHERE stok > 0 ORDER BY kategori, nama_produk ASC");

// Simpan data produk ke array untuk JS
$produk_array = [];
while ($row = $result->fetch_assoc()) {
    $produk_array[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Form Pesanan - Kedai Melwaa 💕</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body { 
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #ffe6f2 0%, #e0f7fa 100%);
    min-height: 100vh;
    padding: 20px;
}

.container {
    max-width: 800px;
    margin: 0 auto;
}

header {
    background: linear-gradient(135deg, #ff69b4, #ff8cb8);
    color: white;
    padding: 30px 25px;
    border-radius: 25px 25px 0 0;
    text-align: center;
    box-shadow: 0 8px 25px rgba(255,105,180,0.3);
}

header h1 {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 5px;
}

header p {
    font-size: 14px;
    opacity: 0.9;
}

.form-container {
    background: white;
    border-radius: 0 0 25px 25px;
    padding: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.customer-info {
    background: linear-gradient(135deg, #fff5f8, #f0f9ff);
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 25px;
    border: 2px dashed #ffb6c1;
}

.customer-info label {
    display: block;
    font-weight: 600;
    color: #ff1493;
    margin-bottom: 8px;
    font-size: 14px;
}

.customer-info input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #ffb6c1;
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s;
}

.customer-info input:focus {
    outline: none;
    border-color: #ff69b4;
    box-shadow: 0 0 0 3px rgba(255,105,180,0.1);
}

.order-items-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 25px 0 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #ffe6f2;
}

.order-items-title h3 {
    color: #ff1493;
    font-size: 18px;
}

.item-count {
    background: linear-gradient(135deg, #ff69b4, #ff8cb8);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

#order-items {
    margin-bottom: 20px;
}

.order-item {
    background: white;
    border: 2px solid #ffe6f2;
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 15px;
    position: relative;
    transition: all 0.3s;
}

.order-item:hover {
    border-color: #ffb6c1;
    box-shadow: 0 5px 15px rgba(255,105,180,0.15);
}

.item-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px dashed #ffe6f2;
}

.item-number {
    background: linear-gradient(135deg, #ff69b4, #ff8cb8);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
}

.remove-btn {
    background: linear-gradient(135deg, #f44336, #e53935);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 6px 12px;
    cursor: pointer;
    font-weight: 600;
    font-size: 12px;
    transition: all 0.3s;
}

.remove-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(244,67,54,0.3);
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: #555;
    margin-bottom: 8px;
    font-size: 13px;
}

.form-group select,
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e8e8e8;
    border-radius: 10px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s;
}

.form-group select:focus,
.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #ff69b4;
    box-shadow: 0 0 0 3px rgba(255,105,180,0.1);
}

.form-group textarea {
    resize: vertical;
}

.qty-row {
    display: grid;
    grid-template-columns: 3fr 1fr;
    gap: 10px;
}

.btn-add {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #00bcd4, #0097a7);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 15px;
}

.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,188,212,0.3);
}

.btn-submit {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #ff69b4, #ff8cb8);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 15px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255,105,180,0.4);
}

.btn-back {
    display: block;
    text-align: center;
    padding: 14px;
    background: white;
    color: #666;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 600;
    border: 2px solid #e8e8e8;
    transition: all 0.3s;
}

.btn-back:hover {
    background: #f8f8f8;
    border-color: #ddd;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #999;
    font-style: italic;
}

/* Notifikasi struk terakhir */
.last-order-notif {
    background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
    border: 2px solid #bbdefb;
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 20px;
    text-align: center;
    font-size: 14px;
    color: #5d4037;
}

.last-order-notif strong {
    color: #ff69b4;
}

@media (max-width: 600px) {
    body {
        padding: 10px;
    }

    header h1 {
        font-size: 22px;
    }

    .form-container {
        padding: 20px;
    }

    .qty-row {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<div class="container">
    <header>
        <h1>🍰 Form Pesanan Menu</h1>
        <p>Kedai Melwaa - Maniskan Harimu Setiap Saat 💕</p>
    </header>

    <div class="form-container">
        <?php if (isset($_SESSION['pesanan_selesai'])): ?>
        <div class="last-order-notif">
            🎉 Pesanan terakhir: <strong><?= htmlspecialchars($_SESSION['pesanan_selesai']['nama'] ?? 'Pelanggan') ?></strong> 
            - No. Faktur: <strong><?= htmlspecialchars($_SESSION['pesanan_selesai']['nomor_faktur'] ?? '') ?></strong>
        </div>
        <?php endif; ?>

        <form action="proses_beli.php" method="POST" id="order-form">
            <div class="customer-info">
                <label>👤 Nama Pemesan *</label>
                <input type="text" name="nama" required placeholder="Siapa nama Anda?" 
                       value="<?= htmlspecialchars($_SESSION['pesanan_selesai']['nama'] ?? '') ?>">
            </div>

            <div class="order-items-title">
                <h3>📋 Daftar Pesanan</h3>
                <span class="item-count">Total: <span id="item-total">0</span> item</span>
            </div>

            <div id="order-items">
                <div class="empty-state">Klik tombol "Tambah Menu" untuk mulai memesan 🛒</div>
            </div>

            <button type="button" class="btn-add" onclick="addMenuItem()">
                ➕ Tambah Menu
            </button>

            <button type="submit" class="btn-submit">
                🛒 Pesan Sekarang
            </button>

            <a href="dashboard_user.php" class="btn-back">⬅ Kembali ke Dashboard</a>
        </form>
    </div>
</div>

<script>
// Data produk dari PHP
const produkArray = <?php echo json_encode($produk_array); ?>;
let itemCount = 0;

function addMenuItem() {
    itemCount++;
    const container = document.getElementById('order-items');
    
    // Hapus empty state jika ada
    const emptyState = container.querySelector('.empty-state');
    if (emptyState) {
        emptyState.remove();
    }

    const div = document.createElement('div');
    div.className = 'order-item';
    div.id = 'item-' + itemCount;

    let options = '<option value="">-- Pilih Menu --</option>';
    produkArray.forEach(p => {
        options += `<option value="${p.id_produk}" data-kategori="${p.kategori}" data-stok="${p.stok}" data-harga="${p.harga_jual}">${p.nama_produk} - Rp ${parseInt(p.harga_jual).toLocaleString('id-ID')}</option>`;
    });

    div.innerHTML = `
        <div class="item-header">
            <div class="item-number">${itemCount}</div>
            <button type="button" class="remove-btn" onclick="removeItem(${itemCount})">🗑️ Hapus</button>
        </div>

        <div class="form-group">
            <label>🍽️ Pilih Menu *</label>
            <select name="id_produk[]" onchange="updateKategori(this)" required>${options}</select>
        </div>

        <div class="qty-row">
            <div class="form-group">
                <label>📦 Kategori</label>
                <input type="text" name="kategori[]" readonly placeholder="Otomatis terisi">
            </div>

            <div class="form-group">
                <label>🔢 Jumlah *</label>
                <select name="qty[]" required>
                    <option value="1">1</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>📝 Catatan (Opsional)</label>
            <textarea name="catatan[]" rows="2" placeholder="Contoh: kurang pedas, tambah es, tanpa bawang..."></textarea>
        </div>
    `;

    container.appendChild(div);
    updateItemCount();
}

function removeItem(id) {
    const el = document.getElementById('item-' + id);
    el.remove();
    updateItemCount();

    // Tampilkan empty state jika tidak ada item
    const container = document.getElementById('order-items');
    if (container.children.length === 0) {
        container.innerHTML = '<div class="empty-state">Klik tombol "Tambah Menu" untuk mulai memesan 🛒</div>';
    }
}

function updateKategori(selectElem) {
    const selectedOption = selectElem.options[selectElem.selectedIndex];
    const kategori = selectedOption.getAttribute('data-kategori');
    const stok = parseInt(selectedOption.getAttribute('data-stok')) || 1;

    const parent = selectElem.closest('.order-item');
    parent.querySelector('input[name="kategori[]"]').value = kategori || '';

    const qtySelect = parent.querySelector('select[name="qty[]"]');
    qtySelect.innerHTML = '';
    for (let i = 1; i <= stok; i++) {
        const opt = document.createElement('option');
        opt.value = i;
        opt.textContent = i;
        qtySelect.appendChild(opt);
    }
}

function updateItemCount() {
    const count = document.querySelectorAll('.order-item').length;
    document.getElementById('item-total').textContent = count;
}

// Tambahkan baris pertama otomatis
addMenuItem();

// Auto focus ke input nama
document.querySelector('input[name="nama"]').focus();
</script>

</body>
</html>