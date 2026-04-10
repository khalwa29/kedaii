<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke index.php yang ada di folder user (root)
header("Location: ../index.php");
exit;
?>