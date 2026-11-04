<?php
session_start();
include 'config/kuis_db.php';
include 'middleware/auth_check.php';
cek_login();

// Ambil ID project dari URL
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM projects WHERE id='$id'");
$data = mysqli_fetch_assoc($query);

// Kalau project tidak ditemukan
if (!$data) {
    echo "<script>alert('Project tidak ditemukan!'); 
          window.location='index.php';</script>";
    exit;
}

// Cek hak akses: hanya pemilik, admin, atau SA yang boleh hapus
if ($_SESSION['user_id'] != $data['user_id'] && !in_array($_SESSION['role'], ['sa','admin'])) {
    echo "<script>alert('Akses ditolak!'); 
          window.location='index.php';</script>";
    exit;
}

// Hapus file gambar dari folder kalau ada
if ($data['gambar'] && file_exists("assets/img/" . $data['gambar'])) {
    unlink("assets/img/" . $data['gambar']);
}

// Hapus data dari database
mysqli_query($koneksi, "DELETE FROM projects WHERE id='$id'");

echo "<script>alert('Project berhasil dihapus!'); 
      window.location='index.php';</script>";
?>