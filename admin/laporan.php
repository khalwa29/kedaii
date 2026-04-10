<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION["username"];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Laporan Penjualan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

.container {
    padding: 30px 60px 70px;
}

.card {
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(255,182,193,0.25);
    overflow: hidden;
}

.card-header {
    background: linear-gradient(90deg, #ff8cb8, #6ee3ff);
    color: #fff;
    font-size: 18px;
    font-weight: 600;
}

.table thead {
    background: linear-gradient(90deg, #ff69b4, #77e3f0);
    color: #fff;
    text-align: center;
}

.table tbody tr td {
    vertical-align: middle;
    text-align: center;
}

#exportBtn {
    display: none;
    margin-top: 15px;
    border-radius: 10px;
    font-weight: 600;
}

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
        <span>Halo, <?= htmlspecialchars($username) ?> — siap pantau penjualan hari ini? 💸</span>
    </div>
    <a href="dashboard_admin.php" style="text-decoration:none; padding:8px 12px; background:#4CAF50; color:white; border-radius:5px; font-weight:600;">🏠 Dashboard</a>
</header>

<div class="container">
  <div class="card shadow mt-3">
    <div class="card-header">Laporan Penjualan</div>
    <div class="card-body">
      <form id="filterForm" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label">Tanggal Awal</label>
          <input type="date" id="tgl_awal" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal Akhir</label>
          <input type="date" id="tgl_akhir" class="form-control" required>
        </div>
        <div class="col-md-4">
          <button type="submit" class="btn btn-success w-100">Tampilkan</button>
        </div>
      </form>

      <hr>

      <div class="table-responsive">
        <table class="table table-bordered table-striped w-100 mt-3">
          <thead>
            <tr>
              <th>Tanggal Beli</th>
              <th>Nama Produk</th>
              <th>Harga Jual</th>
              <th>Qty</th>
              <th>Total Harga</th>
            </tr>
          </thead>
          <tbody id="tabelData">
            <tr><td colspan="5" class="text-center text-muted">Pilih rentang tanggal dan klik Tampilkan</td></tr>
          </tbody>
        </table>
      </div>

      <div class="text-center">
        <button id="exportBtn" class="btn btn-danger">Export PDF</button>
      </div>
    </div>
  </div>
</div>

<footer>
    🍜🥤🍪 Kasir Melwaa — “Belanja Mudah, Untung Setiap Hari!” 💕
</footer>

<script>
$(document).ready(function(){
  $('#filterForm').on('submit', function(e){
    e.preventDefault();
    let tgl_awal = $('#tgl_awal').val();
    let tgl_akhir = $('#tgl_akhir').val();

    $('#tabelData').html('<tr><td colspan="5" class="text-center text-secondary">Memuat data...</td></tr>');

    $.ajax({
      url: 'proses_laporan.php',
      type: 'POST',
      data: {tgl_awal, tgl_akhir},
      dataType: 'json',
      success: function(data){
        if(data.error){
          $('#tabelData').html(`<tr><td colspan="5" class="text-center text-danger">${data.error}</td></tr>`);
          $('#exportBtn').hide();
          return;
        }
        let html = '';
        if(data.length > 0){
          data.forEach(item => {
            html += `
              <tr>
                <td>${item.tanggal_beli}</td>
                <td>${item.nama_produk}</td>
                <td>Rp ${parseInt(item.harga_jual).toLocaleString('id-ID')}</td>
                <td>${item.qty}</td>
                <td>Rp ${parseInt(item.total_harga).toLocaleString('id-ID')}</td>
              </tr>`;
          });
          $('#exportBtn').show().data('tglAwal', tgl_awal).data('tglAkhir', tgl_akhir);
        } else {
          html = `<tr><td colspan="5" class="text-center text-muted">Tidak ada data untuk rentang tanggal tersebut</td></tr>`;
          $('#exportBtn').hide();
        }
        $('#tabelData').html(html);
      },
      error: function(xhr){
        $('#tabelData').html('<tr><td colspan="5" class="text-center text-danger">Terjadi kesalahan saat mengambil data.</td></tr>');
        console.error(xhr.responseText);
      }
    });
  });

  $('#exportBtn').on('click', function(){
    const tgl_awal = $(this).data('tglAwal');
    const tgl_akhir = $(this).data('tglAkhir');
    window.open(`export_pdf.php?tgl_awal=${tgl_awal}&tgl_akhir=${tgl_akhir}`, '_blank');
  });
});
</script>

</body>
</html>
