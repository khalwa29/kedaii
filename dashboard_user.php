<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard User 💕</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #fff1f8, #e2f7ff);
    padding: 40px 20px;
    margin: 0;
}

/* CARD UTAMA */
.dashboard-card {
    max-width: 750px;
    margin: auto;
    background: #ffffffee;
    padding: 50px 40px;
    border-radius: 25px;
    box-shadow: 0 10px 30px rgba(255,182,193,0.28);
    text-align: center;
    backdrop-filter: blur(5px);
    animation: fadeIn 1s ease;
}

/* ANIMASI MASUK */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

h1 {
    font-size: 32px;
    color: #ff69b4;
    margin-bottom: 15px;
    font-weight: 600;
}

.subtitle {
    font-size: 17px;
    color: #444;
    margin-bottom: 35px;
}

/* WRAPPER BUTTONS */
.btn-group {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 18px;
}

/* BUTTON STYLE */
.btn {
    background: linear-gradient(90deg, #ff69b4, #77e3f0);
    padding: 13px 30px;
    border-radius: 14px;
    border: none;
    color: white;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(255,105,180,0.22);
    transition: 0.3s;
}

.btn:hover {
    transform: translateY(-4px) scale(1.03);
    box-shadow: 0 10px 20px rgba(255,105,180,0.33);
}

/* FOOTER */
footer {
    margin-top: 25px;
    color: #777;
    font-size: 13px;
}
</style>
</head>

<body>

<div class="dashboard-card">

    <h1>Selamat Datang di Kedai Melwaa!</h1>
    <p class="subtitle">Nikmatin harimu dengan santapan yang nikmat 💕</p>

    <div class="btn-group">
        <button class="btn" onclick="window.location.href='produk.php'">📋 Lihat Menu</button>
        <button class="btn" onclick="window.location.href='beli.php'">🛍️ Pesan Sekarang</button>
        <button class="btn" onclick="window.location.href='index.php'">🏠 Kembali ke Beranda</button>
    </div>

    <footer>© Kedai Melwaa — “Nikmatin harimu dengan santapan yang nikmat” 💕</footer>
</div>

</body>
</html>
