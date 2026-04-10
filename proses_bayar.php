<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "db_kasir");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Cek jika tidak ada data pesanan sementara
if (!isset($_SESSION['pesanan_sementara'])) {
    header("Location: index.php");
    exit;
}

// Ambil data dari form pembayaran
$metode_bayar = $_POST['metode_bayar'] ?? 'tunai';
$jumlah_bayar = $_POST['jumlah_bayar'];

// Ambil data dari session
$pesanan = $_SESSION['pesanan_sementara'];
$nama = $pesanan['nama'];
$total_belanja = $pesanan['total_belanja'];
$detail_pesanan = $pesanan['detail_pesanan'];
$id_produk = $pesanan['id_produk'];
$qty = $pesanan['qty'];

// Hitung kembalian
$kembalian = $jumlah_bayar - $total_belanja;

// ===========================
// 1️⃣ BUAT NOMOR FAKTUR UNIK
// ===========================
$tanggal = date('Ymd');
$q_counter = $koneksi->query("SELECT COUNT(*) as total_hari_ini FROM tb_jual WHERE DATE(tanggal_beli) = CURDATE()");
$d_counter = $q_counter->fetch_assoc();
$counter = $d_counter['total_hari_ini'] + 1;
$nomor_faktur = "FAK" . $tanggal . "-" . str_pad($counter, 4, "0", STR_PAD_LEFT);

// Cek duplikasi
$cek_duplikat = $koneksi->query("SELECT nomor_faktur FROM tb_jual WHERE nomor_faktur = '$nomor_faktur'");
if ($cek_duplikat->num_rows > 0) {
    $nomor_faktur = "FAK" . $tanggal . "-" . str_pad($counter, 4, "0", STR_PAD_LEFT) . "-" . mt_rand(100, 999);
}

// ===========================
// 2️⃣ SIMPAN KE TB_JUAL (DENGAN METODE_BAYAR)
// ===========================
// Cek struktur tabel terlebih dahulu
$check_columns = $koneksi->query("SHOW COLUMNS FROM tb_jual LIKE 'metode_bayar'");
if ($check_columns->num_rows == 0) {
    // Jika kolom tidak ada, buat tanpa metode_bayar
    $insert_jual = $koneksi->query("INSERT INTO tb_jual 
        (nomor_faktur, tanggal_beli, total_belanja, total_bayar, kembalian)
        VALUES 
        ('$nomor_faktur', NOW(), '$total_belanja', '$jumlah_bayar', '$kembalian')");
} else {
    // Jika kolom ada, gunakan dengan metode_bayar
    $insert_jual = $koneksi->query("INSERT INTO tb_jual 
        (nomor_faktur, tanggal_beli, total_belanja, total_bayar, kembalian, metode_bayar)
        VALUES 
        ('$nomor_faktur', NOW(), '$total_belanja', '$jumlah_bayar', '$kembalian', '$metode_bayar')");
}

if (!$insert_jual) {
    die("Error menyimpan data penjualan: " . $koneksi->error);
}

// ===========================
// 3️⃣ SIMPAN KE RINCI_JUAL
// ===========================
foreach ($detail_pesanan as $detail) {
    $kode_produk = $koneksi->real_escape_string($detail['kode_produk']);
    $nama_produk = $koneksi->real_escape_string($detail['nama_produk']);
    
    $insert_rinci = $koneksi->query("INSERT INTO rinci_jual 
        (nomor_faktur, kode_produk, nama_produk, harga_modal, harga_jual, qty, total_harga, untung)
        VALUES 
        ('$nomor_faktur', '$kode_produk', '$nama_produk', 
         '{$detail['harga_modal']}', '{$detail['harga_jual']}', '{$detail['qty']}', 
         '{$detail['total_harga']}', '{$detail['untung']}')");
         
    if (!$insert_rinci) {
        $koneksi->query("DELETE FROM tb_jual WHERE nomor_faktur = '$nomor_faktur'");
        die("Error menyimpan detail penjualan: " . $koneksi->error);
    }
}

// ===========================
// 4️⃣ UPDATE STOK PRODUK
// ===========================
foreach ($id_produk as $index => $id) {
    $jumlah = $qty[$index];
    $koneksi->query("UPDATE tb_produk SET stok = stok - $jumlah WHERE id_produk = '$id' AND stok >= $jumlah");
}

// ===========================
// 5️⃣ SIMPAN KE SESSION UNTUK STRUK
// ===========================
$_SESSION['pesanan_selesai'] = [
    'nama' => $nama,
    'nomor_faktur' => $nomor_faktur,
    'total_belanja' => $total_belanja,
    'jumlah_bayar' => $jumlah_bayar,
    'kembalian' => $kembalian,
    'metode_bayar' => $metode_bayar
];

// Hapus data sementara
unset($_SESSION['pesanan_sementara']);

// ===========================
// 6️⃣ REDIRECT KE STRUK
// ===========================
header("Location: struk.php?nomor_faktur=" . urlencode($nomor_faktur));
exit;
?>