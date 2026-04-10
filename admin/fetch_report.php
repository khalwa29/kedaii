<?php
$koneksi = new mysqli("localhost", "root", "", "db_kasir");

$tgl_awal  = $_POST['tgl_awal'] ?? '';
$tgl_akhir = $_POST['tgl_akhir'] ?? '';
$data = [];

if ($tgl_awal && $tgl_akhir) {
    $query = "
        SELECT 
            j.tanggal_beli,
            r.nama_produk,
            r.harga_jual,
            r.qty,
            r.total_harga
        FROM tb_jual j
        INNER JOIN rinci_jual r ON j.nomor_faktur = r.nomor_faktur
        WHERE DATE(j.tanggal_beli) BETWEEN '$tgl_awal' AND '$tgl_akhir'
        ORDER BY j.tanggal_beli ASC
    ";
    $result = $koneksi->query($query);
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
echo json_encode($data);
?>
