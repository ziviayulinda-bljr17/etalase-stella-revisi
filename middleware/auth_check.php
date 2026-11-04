<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi cek apakah user sudah login
function cek_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /etalase-stella/auth/login.php");
        exit;
    }
}

// Fungsi cek role tertentu (contoh pakai: cek_role(['sa','admin']))
function cek_role($roles) {
    cek_login();
    if (!in_array($_SESSION['role'], $roles)) {
        echo "<script>alert('Akses ditolak!'); 
              window.location='/etalase-stella/index.php';</script>";
        exit;
    }
}
?>