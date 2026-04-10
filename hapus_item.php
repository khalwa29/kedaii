<?php
session_start();

if (isset($_POST['index'])) {
    $index = intval($_POST['index']);
    if (isset($_SESSION['keranjang'][$index])) {
        unset($_SESSION['keranjang'][$index]);
        $_SESSION['keranjang'] = array_values($_SESSION['keranjang']); // reindex array
    }
}

header("Location: keranjang.php");
exit;
?>
