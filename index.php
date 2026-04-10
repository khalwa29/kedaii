<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kedai Melwaa 💕</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; transition: all 0.3s ease; }

body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #fff1f8, #e2f7ff);
}

.container {
    text-align: center;
    background: #fff;
    padding: 60px 40px;
    border-radius: 0;
    box-shadow: none;
    width: 100%;
    min-height: 100vh;
}


h1 {
    font-size: 28px;
    color: #ff69b4;
    margin-bottom: 15px;
}

p {
    font-size: 15px;
    color: #555;
    margin-bottom: 30px;
}

button {
    padding: 12px 25px;
    font-size: 16px;
    font-weight: 600;
    border: none;
    border-radius: 10px;
    margin: 10px;
    cursor: pointer;
    color: #fff;
    background: linear-gradient(90deg, #ff69b4, #77e3f0);
}

button:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(119,227,240,0.3);
}

/* Popup styling */
.popup {
    display: none;
    position: fixed;
    z-index: 10;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.4);
}

.popup-content {
    background: #fff;
    border-radius: 20px;
    padding: 30px 20px;
    text-align: center;
    width: 300px;
    margin: 15% auto;
    box-shadow: 0 8px 20px rgba(255,182,193,0.4);
    position: relative;
}

.popup-content h2 {
    color: #ff69b4;
    font-size: 22px;
    margin-bottom: 20px;
}

.popup-content button {
    display: block;
    width: 100%;
    margin: 10px 0;
    background: linear-gradient(90deg, #ff69b4, #77e3f0);
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 0;
    font-size: 16px;
    cursor: pointer;
}

.popup-content button:hover {
    transform: scale(1.05);
}

.close-btn {
    position: absolute;
    top: 10px; right: 15px;
    font-size: 20px;
    color: #888;
    cursor: pointer;
}
</style>
</head>
<body>

<div class="container">
    <h1>Kedai Melwaa ☕</h1>
    <p>Selamat datang di Kedai Melwaa </p>

    <button id="loginBtn">Login 🍰</button>
    <footer style="margin-top:25px; font-size:13px; color:#777;">
    <iframe
      src="https://lookerstudio.google.com/embed/reporting/0a2a65b8-2c58-49e9-bb92-1a1db72cd9e4/page/lqOjF" 
      width="100%"
      height="700"
      style="border:0; margin-top:40px;"
      allowfullscreen>
    </iframe>

    </footer>
</div>


<!-- Popup pilihan -->
<div class="popup" id="loginPopup">
  <div class="popup-content">
    <span class="close-btn" onclick="closePopup()">✖</span>
    <h2>Pilih Peranmu 💫</h2>
    <button onclick="window.location.href='dashboard_user.php'">Masuk Sebagai User 👩‍🍳</button>
    <button onclick="window.location.href='login.php'">Masuk Sebagai Admin 🔐</button>
  </div>
</div>

<script>
const popup = document.getElementById('loginPopup');
const loginBtn = document.getElementById('loginBtn');

loginBtn.addEventListener('click', () => {
    popup.style.display = 'block';
});

function closePopup() {
    popup.style.display = 'none';
}

window.onclick = function(event) {
    if (event.target === popup) {
        popup.style.display = 'none';
    }
};
</script>

</body>
</html>
