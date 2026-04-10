<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "db_kasir");
    if ($koneksi->connect_error) {
        die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data dari form
$nama = $_POST['nama'];
$id_produk = $_POST['id_produk'];
$qty = $_POST['qty'];
$catatan = isset($_POST['catatan']) ? $_POST['catatan'] : [];

// ===========================
// 1️⃣ HITUNG TOTAL BELANJA (TANPA SIMPAN KE DATABASE)
// ===========================
$total_belanja = 0;
$detail_pesanan = [];

foreach ($id_produk as $index => $id) {
    $produk = $koneksi->query("SELECT * FROM tb_produk WHERE id_produk='$id'")->fetch_assoc();
    if ($produk) {
        $harga_modal = isset($produk['harga_modal']) ? $produk['harga_modal'] : 0;
        $harga_jual  = $produk['harga_jual'];
        $jumlah      = $qty[$index];
        $total_harga = $harga_jual * $jumlah;
        $untung      = ($harga_jual - $harga_modal) * $jumlah;
        $total_belanja += $total_harga;

        $detail_pesanan[] = [
            'id_produk'   => $id,
            'kode_produk' => $produk['kode_produk'],
            'nama_produk' => $produk['nama_produk'],
            'harga_modal' => $harga_modal,
            'harga_jual'  => $harga_jual,
            'qty'         => $jumlah,
            'total_harga' => $total_harga,
            'untung'      => $untung
        ];
    }
}

// ===========================
// 2️⃣ SIMPAN DATA KE SESSION (BELUM SIMPAN DATABASE)
// ===========================
$_SESSION['pesanan_sementara'] = [
    'nama'           => $nama,
    'total_belanja'  => $total_belanja,
    'detail_pesanan' => $detail_pesanan,
    'id_produk'      => $id_produk,
    'qty'            => $qty
];

// ===========================
// 3️⃣ REDIRECT KE HALAMAN PEMBAYARAN
// ===========================
header("Location: pembayaran.php");
exit;
?>