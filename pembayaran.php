<?php
session_start();
if (!isset($_SESSION['pesanan_sementara'])) {
    header("Location: index.php");
    exit;
}

$pesanan = $_SESSION['pesanan_sementara'];
$total_belanja = $pesanan['total_belanja'];
$nama_pelanggan = $pesanan['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - KhaMelicious</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #ff69b4;
            margin: 0;
            font-size: 24px;
        }
        .customer-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #ff69b4;
        }
        .total-section {
            background: #fff3cd;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 25px;
            border: 2px dashed #ffc107;
        }
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            color: #d63384;
            margin: 10px 0;
        }
        .payment-methods {
            margin-bottom: 25px;
        }
        .payment-method {
            display: flex;
            align-items: center;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #ff69b4;
            background: #fff5f7;
        }
        .payment-method input {
            margin-right: 10px;
        }
        .payment-method label {
            flex: 1;
            cursor: pointer;
            font-weight: 500;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            outline: none;
            border-color: #ff69b4;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }
        .btn {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-bayar {
            background: #ff69b4;
            color: white;
        }
        .btn-bayar:hover {
            background: #e0559c;
            transform: translateY(-2px);
        }
        .btn-batal {
            background: #6c757d;
            color: white;
        }
        .btn-batal:hover {
            background: #5a6268;
        }
        .change-display {
            background: #d1ecf1;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
            color: #0c5460;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💵 PROSES PEMBAYARAN</h1>
            <p>KhaMelicious</p>
        </div>

        <div class="customer-info">
            <stronag>Pelanggan:</strong> <?= htmlspecialchars($nama_pelanggan) ?><br>
            <strong>Total Belanja:</strong> Rp <?= number_format($total_belanja, 0, ',', '.') ?>
        </div>

        <form action="proses_bayar.php" method="POST" id="formBayar">
            <div class="payment-methods">
                <h3>Metode Pembayaran:</h3>
                
                <div class="payment-method">
                    <input type="radio" id="tunai" name="metode_bayar" value="tunai" checked>
                    <label for="tunai">💵 Tunai</label>
                </div>
                
                <div class="payment-method">
                    <input type="radio" id="qris" name="metode_bayar" value="qris">
                    <label for="qris">📱 QRIS</label>
                </div>
                
                <div class="payment-method">
                    <input type="radio" id="debit" name="metode_bayar" value="debit">
                    <label for="debit">💳 Kartu Debit</label>
                </div>
                
                <div class="payment-method">
                    <input type="radio" id="kredit" name="metode_bayar" value="kredit">
                    <label for="kredit">💳 Kartu Kredit</label>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah_bayar">Jumlah Bayar:</label>
                <input type="number" id="jumlah_bayar" name="jumlah_bayar" class="form-control" 
                       placeholder="Masukkan jumlah bayar" min="<?= $total_belanja ?>" required
                       oninput="hitungKembalian()">
            </div>

            <div id="kembalianDisplay" class="change-display">
                Kembalian: Rp <span id="kembalianValue">0</span>
            </div>

            <div class="btn-group">
                <button type="button" class="btn btn-batal" onclick="batalTransaksi()">❌ Batal</button>
                <button type="submit" class="btn btn-bayar" id="btnBayar">💳 Proses Bayar</button>
            </div>
        </form>
    </div>

    <script>
        function hitungKembalian() {
            const totalBelanja = <?= $total_belanja ?>;
            const jumlahBayar = document.getElementById('jumlah_bayar').value;
            const kembalianDisplay = document.getElementById('kembalianDisplay');
            const kembalianValue = document.getElementById('kembalianValue');
            const btnBayar = document.getElementById('btnBayar');

            if (jumlahBayar >= totalBelanja) {
                const kembalian = jumlahBayar - totalBelanja;
                kembalianValue.textContent = new Intl.NumberFormat('id-ID').format(kembalian);
                kembalianDisplay.style.display = 'block';
                btnBayar.disabled = false;
                btnBayar.innerHTML = '💳 Proses Bayar';
            } else {
                kembalianDisplay.style.display = 'none';
                btnBayar.disabled = true;
                btnBayar.innerHTML = '❌ Jumlah Bayar Kurang';
            }
        }

        function batalTransaksi() {
            if (confirm('Apakah Anda yakin ingin membatalkan transaksi?')) {
                window.location.href = 'index.php';
            }
        }

        // Auto focus on jumlah bayar
        document.getElementById('jumlah_bayar').focus();

        // Handle form submission
        document.getElementById('formBayar').addEventListener('submit', function(e) {
            const jumlahBayar = document.getElementById('jumlah_bayar').value;
            const totalBelanja = <?= $total_belanja ?>;
            
            if (parseInt(jumlahBayar) < totalBelanja) {
                e.preventDefault();
                alert('Jumlah bayar tidak cukup!');
                document.getElementById('jumlah_bayar').focus();
            }
        });
    </script>
</body>
</html>