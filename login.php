<?php
session_start();

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "db_kasir");
if ($conn->connect_error) {
    die("<h3 style='color:pink;text-align:center;'>Koneksi gagal 💔 : " . $conn->connect_error . "</h3>");
}

// Proses login kalau form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Ambil data user berdasarkan email
    $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password menggunakan hash
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];

            // Redirect ke dashboard admin
            echo "<script>
                    alert('🎀 Selamat datang kembali, " . addslashes($user['username']) . " 💕');
                    window.location='admin/dashboard_admin.php';
                  </script>";
            exit;
        } else {
            echo "<script>alert('😿 Password salah, coba lagi ya~');</script>";
        }
    } else {
        echo "<script>alert('😿 Email tidak ditemukan~');</script>";
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Kasir 💕</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background: linear-gradient(135deg, #ffe6f2, #e0f7fa);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .container {
      background-color: #fff;
      border-radius: 20px;
      padding: 30px;
      width: 360px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
      border: 3px solid #ffd6e7;
      position: relative;
    }
    h2 { color: #ff69b4; font-size: 24px; margin-bottom: 20px; }
    label { display: block; text-align: left; margin-top: 10px; color: #444; font-weight: 600; }
    input { width: 100%; padding: 10px; margin-top: 5px; border: 2px solid #ffb6c1; border-radius: 10px; outline: none; transition: 0.3s; font-size: 14px; }
    input:focus { border-color: #ff69b4; box-shadow: 0 0 8px rgba(255,182,193,0.6); }
    button { background-color: #ff69b4; color: white; border: none; border-radius: 12px; padding: 10px 0; width: 100%; margin-top: 20px; font-size: 16px; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 8px rgba(255,105,180,0.3); }
    button:hover { background-color: #ff85c1; transform: scale(1.03); }
    p { margin-top: 15px; font-size: 14px; }
    a { color: #ff69b4; text-decoration: none; font-weight: bold; }
    a:hover { text-decoration: underline; }
    .emoji { font-size: 40px; position: absolute; top: -20px; right: -10px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="emoji">🌸</div>
    <h2>Login Kasir 💖</h2>
    <form method="POST" autocomplete="off">
      <label>Email</label>
      <input type="email" name="email" placeholder="Masukkan email kamu" required>
      <label>Password</label>
      <input type="password" name="password" placeholder="Masukkan password rahasia" required>
      <button type="submit">Masuk 💌</button>
    </form>
    <p>Belum punya akun? <a href="proses_registrasi.php">Daftar di sini 💫</a></p>
  </div>
</body>
</html>