<?php
// Pengaturan koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "db_kasir";

// Membuat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("<h3 style='color:pink;text-align:center;'>
        💖 Koneksi ke database gagal: " . $koneksi->connect_error . "
    </h3>");
}

// Set charset UTF-8
$koneksi->set_charset("utf8");
?>
