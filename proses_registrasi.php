<?php
session_start();

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "db_kasir");
if ($conn->connect_error) {
    die("<h3 style='color:pink;text-align:center;'>Koneksi gagal 💔 : " . $conn->connect_error . "</h3>");
}

// Proses registrasi kalau form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if (empty($username) || empty($email) || empty($password)) {
        echo "<script>alert('😿 Semua field harus diisi~');</script>";
    } elseif ($password !== $confirm_password) {
        echo "<script>alert('😿 Password dan konfirmasi password tidak cocok~');</script>";
    } elseif (strlen($password) < 6) {
        echo "<script>alert('😿 Password minimal 6 karakter~');</script>";
    } else {
        // Cek apakah email sudah terdaftar
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('😿 Email sudah terdaftar, gunakan email lain~');</script>";
        } else {
            // Hash password dan simpan ke database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            
            if ($stmt->execute()) {
                echo "<script>
                        alert('🎀 Registrasi berhasil! Silakan login ya~ 💕');
                        window.location='login.php';
                      </script>";
                exit;
            } else {
                echo "<script>alert('😿 Terjadi kesalahan, coba lagi nanti~');</script>";
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrasi Kasir 💕</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: linear-gradient(135deg, #ffe6f2, #e0f7fa);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0;
      padding: 20px;
      box-sizing: border-box;
    }
    .container {
      background-color: #fff;
      border-radius: 20px;
      padding: 30px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
      border: 3px solid #ffd6e7;
      position: relative;
    }
    h2 { 
        color: #ff69b4; 
        font-size: 24px; 
        margin-bottom: 20px; 
    }
    label { 
        display: block; 
        text-align: left; 
        margin-top: 15px; 
        color: #444; 
        font-weight: 600; 
    }
    input { 
        width: 100%; 
        padding: 12px; 
        margin-top: 5px; 
        border: 2px solid #ffb6c1; 
        border-radius: 10px; 
        outline: none; 
        transition: 0.3s; 
        font-size: 14px; 
        box-sizing: border-box;
    }
    input:focus { 
        border-color: #ff69b4; 
        box-shadow: 0 0 8px rgba(255,182,193,0.6); 
    }
    button { 
        background-color: #ff69b4; 
        color: white; 
        border: none; 
        border-radius: 12px; 
        padding: 12px 0; 
        width: 100%; 
        margin-top: 25px; 
        font-size: 16px; 
        cursor: pointer; 
        transition: 0.3s; 
        box-shadow: 0 4px 8px rgba(255,105,180,0.3); 
        font-weight: bold;
    }
    button:hover { 
        background-color: #ff85c1; 
        transform: scale(1.03); 
    }
    p { 
        margin-top: 20px; 
        font-size: 14px; 
        color: #666;
    }
    a { 
        color: #ff69b4; 
        text-decoration: none; 
        font-weight: bold; 
    }
    a:hover { 
        text-decoration: underline; 
    }
    .emoji { 
        font-size: 40px; 
        position: absolute; 
        top: -20px; 
        right: -10px; 
    }
    .back-btn { 
        background-color: #ffb6c1; 
        margin-top: 15px; 
        box-shadow: 0 4px 8px rgba(255,182,193,0.3);
    }
    .back-btn:hover { 
        background-color: #ffb6c1; 
    }
    .form-group {
        margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="emoji">🌺</div>
    <h2>Daftar Akun Baru 💫</h2>
    <form method="POST" autocomplete="off">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="Buat username unik" required>
      </div>
      
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="Masukkan email aktif" required>
      </div>
      
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Minimal 6 karakter" required>
      </div>
      
      <div class="form-group">
        <label>Konfirmasi Password</label>
        <input type="password" name="confirm_password" placeholder="Ulangi password" required>
      </div>
      
      <button type="submit">Daftar Sekarang </button>
    </form>
    
    <button class="back-btn" onclick="window.location.href='login.php'">Kembali </button>
    
    <p>Sudah punya akun? <a href="login.php">Login di sini </a></p>
  </div>
</body>
</html>