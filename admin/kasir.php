<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: login.php");
    exit;
}
$username = $_SESSION["username"] ?? 'Kasir';

// --- KONEKSI DATABASE ---
$koneksi = new mysqli("localhost", "root", "", "db_kasir");
if ($koneksi->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Gagal terhubung ke database"]);
    exit;
}

// --- API MODE (untuk AJAX) ---
if (isset($_GET['action'])) {
    header('Content-Type: application/json; charset=UTF-8');
    $action = $_GET['action'];

    // === LIST PRODUK ===
    if ($action === 'list_products') {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $search = trim($_GET['search'] ?? '');

        if ($search !== '') {
            $like = "%{$search}%";
            $stmt = $koneksi->prepare("SELECT COUNT(*) FROM tb_produk WHERE nama_produk LIKE ? OR kode_produk LIKE ?");
            $stmt->bind_param('ss', $like, $like);
            $stmt->execute();
            $stmt->bind_result($totalRows);
            $stmt->fetch();
            $stmt->close();

            $stmt = $koneksi->prepare("
                SELECT id_produk, kode_produk, nama_produk, kategori, harga_jual, stok, satuan
                FROM tb_produk
                WHERE nama_produk LIKE ? OR kode_produk LIKE ?
                ORDER BY id_produk DESC LIMIT ? OFFSET ?
            ");
            $stmt->bind_param('ssii', $like, $like, $perPage, $offset);
        } else {
            $res = $koneksi->query("SELECT COUNT(*) as cnt FROM tb_produk");
            $row = $res->fetch_assoc();
            $totalRows = (int)$row['cnt'];

            $stmt = $koneksi->prepare("
                SELECT id_produk, kode_produk, nama_produk, kategori, harga_jual, stok, satuan
                FROM tb_produk ORDER BY id_produk DESC LIMIT ? OFFSET ?
            ");
            $stmt->bind_param('ii', $perPage, $offset);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        echo json_encode([
            "page" => $page,
            "perPage" => $perPage,
            "total" => $totalRows,
            "products" => $products
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // === PROSES CHECKOUT PEMBELIAN ===
    if ($action === 'checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            echo json_encode(["error" => "Data input tidak valid"]);
            exit;
        }

        $cart = $input['cart'] ?? [];
        $total = floatval($input['total'] ?? 0);
        $bayar = floatval($input['bayar'] ?? 0);
        $kembalian = floatval($input['kembalian'] ?? 0);
        $metode_bayar = $input['metode_bayar'] ?? 'Tunai';

        if (empty($cart) || $total <= 0) {
            echo json_encode(["error" => "Keranjang kosong atau total tidak valid"]);
            exit;
        }

        $nomorFaktur = 'PBL' . date('YmdHis') . rand(100, 999);

        $koneksi->begin_transaction();
        try {
            // Simpan ke tb_jual
            $stmt = $koneksi->prepare("
                INSERT INTO tb_jual 
                (nomor_faktur, tanggal_beli, total_belanja, total_bayar, kembalian, metode_bayar)
                VALUES (?, NOW(), ?, ?, ?, ?)
            ");
            $stmt->bind_param('sddds', $nomorFaktur, $total, $bayar, $kembalian, $metode_bayar);
            $stmt->execute();
            $stmt->close();

            // Simpan ke rinci_jual
            $stmtDetail = $koneksi->prepare("
                INSERT INTO rinci_jual 
                (nomor_faktur, kode_produk, nama_produk, harga_jual, qty, total_harga)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            // UPDATE STOK - TAMBAH stok produk (karena PEMBELIAN)
            $stmtUpdateStok = $koneksi->prepare("
                UPDATE tb_produk SET stok = stok - ? WHERE id_produk = ?
            ");

            foreach ($cart as $item) {
                $id_produk = $item['id_produk'];
                $kode_produk = $item['id_produk'];
                $nama_produk = $item['nama_produk'];
                $harga_jual = floatval($item['harga_jual']);
                $qty = intval($item['qty']);
                $total_harga = $harga_jual * $qty;

                // Simpan detail transaksi
                $stmtDetail->bind_param(
                    'sssdid',
                    $nomorFaktur,
                    $kode_produk,
                    $nama_produk,
                    $harga_jual,
                    $qty,
                    $total_harga
                );
                $stmtDetail->execute();

                // TAMBAH stok produk (karena PEMBELIAN/restock)
                $stmtUpdateStok->bind_param('ii', $qty, $id_produk);
                $stmtUpdateStok->execute();
            }
            $stmtDetail->close();
            $stmtUpdateStok->close();

            $koneksi->commit();
            echo json_encode(["success" => true, "nomor_faktur" => $nomorFaktur]);
            exit;
        } catch (Exception $e) {
            $koneksi->rollback();
            echo json_encode(["error" => "Gagal menyimpan transaksi: " . $e->getMessage()]);
            exit;
        }
    }

    echo json_encode(["error" => "Aksi tidak dikenali"]);
    exit;
}
?>

<!-- =============== HALAMAN KASIR PEMBELIAN =============== -->
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Kasir Pembelian - Kedai Melwaa</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body{font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#fff1f8,#e2f7ff);margin:0;color:#333}
header{background:linear-gradient(90deg,#ff8cb8,#6ee3ff);color:#fff;padding:18px 24px;display:flex;justify-content:space-between;align-items:center}
header a{background:#fff;color:#ff69b4;padding:8px 12px;border-radius:10px;text-decoration:none;font-weight:600}
.wrap{max-width:1150px;margin:28px auto;padding:20px;background:#fff;border-radius:16px;box-shadow:0 8px 25px rgba(0,0,0,0.06)}
h2{color:#ff69b4;margin:0 0 12px}
.card{background:#fff;padding:12px;border-radius:12px;box-shadow:0 4px 14px rgba(255,182,193,0.15)}
table{width:100%;border-collapse:collapse}
th,td{padding:10px;border-bottom:1px solid #f6dfe9;text-align:left}
thead th{background:linear-gradient(90deg,#ffb6c1,#a3f3ff);color:#fff}
.add-btn{background:#4CAF50;color:#fff;padding:6px 10px;border-radius:8px;border:none;cursor:pointer}
.pay-btn{background:linear-gradient(90deg,#4CAF50,#77e3f0);color:#fff;padding:10px 14px;border:none;border-radius:10px;cursor:pointer}
.cart-total{background:#f0fff0;padding:15px;border-radius:10px;margin-top:15px;border:2px solid #4CAF50}
.cart-total h3{color:#2e7d32;margin:0 0 10px 0}
.payment-section{display:flex;flex-wrap:wrap;gap:15px;align-items:center;margin-top:15px}
.payment-section label{display:flex;align-items:center;gap:5px;font-weight:600}
.payment-section input, .payment-section select{padding:8px;border:2px solid #4CAF50;border-radius:8px;font-size:14px}
.metode-bayar{background:#e6f7ff;padding:8px 12px;border-radius:8px;border:2px solid #6ee3ff;font-weight:bold;color:#0066cc}
.jenis-transaksi{background:#e8f7e8;padding:8px 12px;border-radius:8px;border:2px solid #4caf50;font-weight:bold;color:#2e7d32}
.qris-section{display:none;background:#f0fff0;padding:15px;border-radius:10px;border:2px solid #4caf50;margin-top:10px;text-align:center}
.qris-code{font-size:24px;font-weight:bold;color:#2e7d32;margin:10px 0}
.loading{display:none;text-align:center;padding:10px;color:#4CAF50}
</style>
</head>
<body>
<header>
  <div>
    <h1 style="margin:0"> Kedai Melwaa </h1>
    <div style="font-size:14px;margin-top:6px">Hai, <?= htmlspecialchars($username) ?></div>
  </div>
  <a href="dashboard_admin.php">🏠 Dashboard</a>
</header>

<div class="wrap">
  <h2>📦 Daftar Produk Tersedia</h2>
  <div class="loading" id="loadingProducts">Memuat data produk...</div>
  <div class="card">
    <table id="productTable">
      <thead><tr><th>No</th><th>Kode</th><th>Nama</th><th>Harga Beli</th><th>Stok Saat Ini</th><th>Satuan</th><th>Aksi</th></tr></thead>
      <tbody id="productBody"></tbody>
    </table>
  </div>

  <h2 style="margin-top:20px;">🛒 Keranjang Pembelian</h2>
  <div class="card">
    <table id="cartTable">
      <thead><tr><th>No</th><th>Nama Produk</th><th>Harga Beli</th><th>Qty Beli</th><th>Total</th><th>Aksi</th></tr></thead>
      <tbody id="cartBody"></tbody>
    </table>

    <!-- TOTAL & PEMBAYARAN SECTION -->
    <div class="cart-total">
      <h3>💰 Total Pembelian: Rp <span id="totalBelanja">0</span></h3>
      
      <div class="payment-section">
        <label>
          <span class="jenis-transaksi">📥 Jenis: PEMBELIAN </span>
        </label>
        
        <label>
          <span class="metode-bayar">💳 Metode Bayar:</span>
          <select id="selectMetodeBayar" style="width:150px">
            <option value="Tunai">Tunai</option>
            <option value="QRIS">QRIS</option>
            <option value="Kartu Kredit">Kartu Kredit</option>
            <option value="Kartu Debit">Kartu Debit</option>
          </select>
        </label>
        
        <div id="tunaiSection">
          <label>💵 Jumlah Bayar: Rp 
            <input id="inputBayar" type="number" min="0" step="1000" value="0" placeholder="0" style="width:120px">
          </label>
          
          <label> Kembalian: Rp 
            <input id="inputKembali" type="text" readonly value="0" style="background:#f0f0f0;font-weight:bold;color:#4CAF50;width:120px">
          </label>
        </div>
        
        <button id="btnBayar" class="pay-btn">💳 Proses Bayar</button>
      </div>

      <!-- QRIS SECTION -->
      <div id="qrisSection" class="qris-section">
        <div style="font-weight:bold;color:#2e7d32;margin-bottom:10px;">📱 Scan QR Code Below</div>
        <div class="qris-code">🔄 Generating QR Code...</div>
        <div style="font-size:12px;color:#666;margin-top:10px;">Total: Rp <span id="qrisTotal">0</span></div>
      </div>
    </div>
  </div>
</div>

<script>
const apiUrl = 'kasir.php';
let cart = {};

function formatRupiah(num){
    return 'Rp ' + (Number(num) || 0).toLocaleString('id-ID');
}

function showLoading(show) {
    document.getElementById('loadingProducts').style.display = show ? 'block' : 'none';
}

// Toggle antara Tunai dan QRIS
function togglePaymentMethod() {
    const metode = document.getElementById('selectMetodeBayar').value;
    const tunaiSection = document.getElementById('tunaiSection');
    const qrisSection = document.getElementById('qrisSection');
    const total = Number(document.getElementById('totalBelanja').textContent.replace(/[^\d]/g, '') || 0);
    
    if (metode === 'QRIS') {
        tunaiSection.style.display = 'none';
        qrisSection.style.display = 'block';
        document.getElementById('qrisTotal').textContent = total.toLocaleString('id-ID');
        
        // Generate kode QRIS sederhana (simulasi)
        document.querySelector('.qris-code').textContent = 'QRIS-' + Date.now();
        
        // Auto-set bayar sama dengan total untuk QRIS
        document.getElementById('inputBayar').value = total;
        recalcKembali();
    } else {
        tunaiSection.style.display = 'flex';
        qrisSection.style.display = 'none';
    }
}

async function fetchProducts(page = 1){
    showLoading(true);
    try {
        const res = await fetch(`${apiUrl}?action=list_products&page=${page}`);
        const data = await res.json();
        const tbody = document.getElementById('productBody');
        tbody.innerHTML = '';
        
        let no = 1;
        for(const p of data.products){
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${no++}</td>
                <td>${p.kode_produk}</td>
                <td>${p.nama_produk}</td>
                <td>${formatRupiah(p.harga_jual)}</td>
                <td>${p.stok}</td>
                <td>${p.satuan}</td>
                <td>
                    <button class='add-btn' onclick='addToCart(${p.id_produk},"${p.nama_produk}",${p.harga_jual})'>
                        ➕ Tambah
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire("Error", "Gagal memuat data produk", "error");
    } finally {
        showLoading(false);
    }
}

function addToCart(id, nama, harga_jual){
    if(!cart[id]) {
        cart[id] = {
            id_produk: id,
            nama_produk: nama,
            harga_jual: harga_jual,
            qty: 1
        };
    } else {
        cart[id].qty++;
    }
    renderCart();
}

function renderCart(){
    const tbody = document.getElementById('cartBody');
    tbody.innerHTML = '';
    let total = 0;
    let i = 1;
    
    if (Object.keys(cart).length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:20px;">Keranjang kosong</td></tr>';
    } else {
        for(const id in cart){
            const item = cart[id];
            const subTotal = item.harga_jual * item.qty;
            total += subTotal;
            
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${i++}</td>
                <td>${item.nama_produk}</td>
                <td>${formatRupiah(item.harga_jual)}</td>
                <td>
                    <input type='number' min='1' value='${item.qty}' 
                           onchange='updateQty(${id}, this.value)' 
                           style='width: 60px; padding: 5px; border: 1px solid #4CAF50; border-radius: 5px;'>
                </td>
                <td>${formatRupiah(subTotal)}</td>
                <td>
                    <button onclick='removeItem(${id})' 
                            style='background: #ff4444; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;'>
                        🗑️ Hapus
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        }
    }
    
    // Update total belanja
    document.getElementById('totalBelanja').textContent = total.toLocaleString('id-ID');
    document.getElementById('inputBayar').dataset.total = total;
    document.getElementById('qrisTotal').textContent = total.toLocaleString('id-ID');
    
    // Recalculate change
    recalcKembali();
}

function updateQty(id, val){
    const qty = parseInt(val);
    if(qty <= 0){
        delete cart[id];
    } else {
        cart[id].qty = qty;
    }
    renderCart();
}

function removeItem(id){
    delete cart[id];
    renderCart();
}

function recalcKembali(){
    const total = Number(document.getElementById('inputBayar').dataset.total || 0);
    const bayar = Number(document.getElementById('inputBayar').value || 0);
    const kembalian = Math.max(0, bayar - total);
    
    document.getElementById('inputKembali').value = formatRupiah(kembalian);
}

// Event Listeners
document.getElementById('inputBayar').addEventListener('input', recalcKembali);
document.getElementById('selectMetodeBayar').addEventListener('change', togglePaymentMethod);

document.getElementById('btnBayar').addEventListener('click', async function(){
    const metodeBayar = document.getElementById('selectMetodeBayar').value;
    const total = Number(document.getElementById('totalBelanja').textContent.replace(/[^\d]/g, '') || 0);
    
    let bayar, kembalian;
    
    if (metodeBayar === 'QRIS') {
        bayar = total;
        kembalian = 0;
    } else {
        bayar = Number(document.getElementById('inputBayar').value || 0);
        kembalian = Math.max(0, bayar - total);
    }

    // Validasi
    if(Object.keys(cart).length === 0) {
        Swal.fire("Keranjang Kosong", "Tambahkan produk terlebih dahulu", "warning");
        return;
    }
    
    if(metodeBayar === 'Tunai' && bayar < total) {
        Swal.fire("Pembayaran Kurang", `Total: ${formatRupiah(total)}\nBayar: ${formatRupiah(bayar)}\nKurang: ${formatRupiah(total - bayar)}`, "error");
        return;
    }

    const payload = {
        cart: Object.values(cart),
        total: total,
        bayar: bayar,
        kembalian: kembalian,
        metode_bayar: metodeBayar
    };

    try{
        const res = await fetch(`${apiUrl}?action=checkout`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const data = await res.json();

        if(data.success){
            let successMessage = `
                Nomor Faktur: <b>${data.nomor_faktur}</b><br>
                Total Pembelian: <b>${formatRupiah(total)}</b><br>
                Metode Bayar: <b>${metodeBayar}</b><br>
            `;
            
            if (metodeBayar === 'Tunai') {
                successMessage += `
                    Bayar: <b>${formatRupiah(bayar)}</b><br>
                    Kembalian: <b>${formatRupiah(kembalian)}</b><br>
                `;
            }
            
            successMessage += `<small>Stok produk berhasil ditambahkan</small>`;
            
            await Swal.fire({
                title: "✅ Pembelian Berhasil!",
                html: successMessage,
                icon: "success",
                confirmButtonText: "OK"
            });
            
            window.open("struk.php?faktur=" + data.nomor_faktur, "_blank");

            // Reset cart dan refresh data produk
            cart = {};
            renderCart();
            document.getElementById('inputBayar').value = 0;
            document.getElementById('selectMetodeBayar').value = 'Tunai';
            togglePaymentMethod();
            recalcKembali();
            
            // Refresh data produk untuk melihat stok terbaru
            fetchProducts();
            
        } else {
            Swal.fire("Gagal", data.error || "Gagal menyimpan transaksi", "error");
        }
    } catch(err) {
        Swal.fire("Kesalahan", "Tidak dapat terhubung ke server", "error");
        console.error(err);
    }
});

// Initial load
document.addEventListener('DOMContentLoaded', function() {
    fetchProducts();
    togglePaymentMethod(); // Set initial state
});
</script>
</body>
</html>