<?php
include "koneksi.php";

if (!isset($_GET['nomor_faktur'])) {
    die("Nomor faktur tidak ditemukan.");
}

$faktur = $_GET['nomor_faktur'];

/* -----------------------------
   AMBIL DATA TRANSAKSI
------------------------------*/
$query = $koneksi->prepare("
    SELECT *
    FROM tb_jual
    WHERE nomor_faktur = ?
");
$query->bind_param("s", $faktur);
$query->execute();
$transaksi = $query->get_result()->fetch_assoc();

if (!$transaksi) {
    die("Data transaksi tidak ditemukan.");
}

/* -----------------------------
   AMBIL DETAIL TRANSAKSI
------------------------------*/
$qDetail = $koneksi->prepare("
    SELECT nama_produk, harga_jual, qty, total_harga
    FROM rinci_jual
    WHERE nomor_faktur = ?
");
$qDetail->bind_param("s", $faktur);
$qDetail->execute();
$detail = $qDetail->get_result();

/* -----------------------------
   FUNGSI FORMAT RUPIAH
------------------------------*/
function rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Struk <?= $faktur ?></title>

<style>
    /* ==============================
       STYLE STRUK 80mm
    ===============================*/
    body {
        width: 80mm;
        margin: 0 auto;
        padding: 0;
        font-family: "Courier New", monospace;
        font-size: 14px;
    }

    .center { text-align: center; }
    .line { 
        border-top: 1px dashed black; 
        margin: 8px 0; 
    }
    .row {
        display: flex;
        justify-content: space-between;
        width: 100%;
    }

    /* ==============================
       MODE CETAK THERMAL
    ===============================*/
    @media print {

        html, body {
            width: 80mm !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        @page {
            size: 80mm auto !important;
            margin: 0 !important;
        }

        body {
            zoom: 140%; /* PERBESAR HASIL CETAK */
        }
    }
</style>

</head>

<body>

<!-- HEADER TOKO -->
<div class="center">
    <div style="font-size:18px; font-weight:bold;">KEDAI MELWAA</div>
    <div>Jl. Merdekaan No.123, Bumijaya</div>
    <div>Telp: 0896-xxxx-xxxx</div>
</div>

<div class="line"></div>

<!-- INFO TRANSAKSI -->
<div class="row"><span>Faktur</span><span><?= $faktur ?></span></div>
<div class="row"><span>Tanggal</span><span><?= date("d/m/Y", strtotime($transaksi['tanggal_beli'])) ?></span></div>
<div class="row"><span>Kasir</span><span>-</span></div>

<div class="line"></div>

<!-- DETAIL PRODUK -->
<?php 
$total_item = 0;
while ($row = $detail->fetch_assoc()):
?>
    <div class="row">
        <span><?= $row['nama_produk'] ?></span>
        <span><?= rupiah($row['total_harga']) ?></span>
    </div>
    <div style="margin-left: 15px; font-size:12px;">
        <?= rupiah($row['harga_jual']) ?> × <?= $row['qty'] ?>
    </div>
<?php 
$total_item += $row['qty'];
endwhile;
?>

<div class="line"></div>

<!-- TOTAL -->
<div class="row"><span>Total Item</span><span><?= $total_item ?> pcs</span></div>
<div class="row"><span>Total Belanja</span><span><?= rupiah($transaksi['total_belanja']) ?></span></div>
<div class="row"><span>Metode Bayar</span><span><?= strtoupper($transaksi['metode_bayar']) ?></span></div>

<?php if ($transaksi['metode_bayar'] == "tunai"): ?>
<div class="row"><span>Bayar</span><span><?= rupiah($transaksi['total_bayar']) ?></span></div>
<div class="row"><span>Kembalian</span><span><?= rupiah($transaksi['kembalian']) ?></span></div>
<?php endif; ?>

<div class="line"></div>

<!-- FOOTER -->
<div class="center" style="font-size:12px;">
    Terima kasih telah berbelanja 💕<br>
    Sampai jumpa kembali!
</div>

<script>
    window.print();
</script>

</body>
</html>
