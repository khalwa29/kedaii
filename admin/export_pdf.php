<?php
ob_start();

/* =========================
   LOAD FPDF (AMAN)
========================= */
require_once __DIR__ . '/../fpdf186/fpdf.php';

/* =========================
   KONEKSI DATABASE
========================= */
$koneksi = new mysqli(
    "localhost",
    "root",
    "",
    "db_kasir"
);

if ($koneksi->connect_error) {
    die("Koneksi database gagal");
}

/* =========================
   PARAMETER TANGGAL
========================= */
$tgl_awal  = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

if ($tgl_awal == '' || $tgl_akhir == '') {
    die("Parameter tanggal tidak lengkap");
}

/* =========================
   QUERY DATA
========================= */
$query = "
    SELECT 
        j.tanggal_beli,
        r.nama_produk,
        r.harga_jual,
        r.qty,
        r.total_harga
    FROM tb_jual j
    INNER JOIN rinci_jual r 
        ON j.nomor_faktur = r.nomor_faktur
    WHERE DATE(j.tanggal_beli) 
        BETWEEN '$tgl_awal' AND '$tgl_akhir'
    ORDER BY j.tanggal_beli ASC
";

$result = $koneksi->query($query);
if (!$result) {
    die("Query gagal: " . $koneksi->error);
}

/* =========================
   BUAT PDF
========================= */
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

/* Judul */
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'LAPORAN PENJUALAN', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 7, "Periode: $tgl_awal s/d $tgl_akhir", 0, 1, 'C');
$pdf->Ln(5);

/* Header Tabel */
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 7, 'Tanggal', 1);
$pdf->Cell(55, 7, 'Nama Produk', 1);
$pdf->Cell(30, 7, 'Harga', 1);
$pdf->Cell(15, 7, 'Qty', 1);
$pdf->Cell(30, 7, 'Total', 1);
$pdf->Ln();

/* Isi Tabel */
$pdf->SetFont('Arial', '', 9);
$total = 0;

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 7, $row['tanggal_beli'], 1);
    $pdf->Cell(55, 7, $row['nama_produk'], 1);
    $pdf->Cell(30, 7, 'Rp ' . number_format($row['harga_jual'], 0, ',', '.'), 1);
    $pdf->Cell(15, 7, $row['qty'], 1);
    $pdf->Cell(30, 7, 'Rp ' . number_format($row['total_harga'], 0, ',', '.'), 1);
    $pdf->Ln();

    $total += $row['total_harga'];
}

/* Total */
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(130, 7, 'TOTAL', 1, 0, 'C');
$pdf->Cell(30, 7, 'Rp ' . number_format($total, 0, ',', '.'), 1);

/* =========================
   OUTPUT PDF
========================= */
ob_end_clean();
$pdf->Output(
    'D',
    "laporan_penjualan_{$tgl_awal}_{$tgl_akhir}.pdf"
);
exit;
