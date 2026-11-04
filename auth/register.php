<?php
session_start();
include '../config/kuis_db.php';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Cek apakah username sudah dipakai
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($cek) > 0) {
        $error = "Username sudah dipakai!";
    } else {
        $query = "INSERT INTO users (username, password, nama_lengkap, role) 
                  VALUES ('$username', '$password', '$nama', 'siswa')";
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Registrasi berhasil! Silakan login.'); 
                  window.location='login.php';</script>";
        } else {
            $error = "Gagal registrasi: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - E-Talase Stella</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <h2>Daftar Akun Siswa</h2>
    
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    
    <form method="POST">
        <label>Username</label><br>
        <input type="text" name="username" placeholder= "Buat username unik..." required><br><br>
        
        <label>Nama Lengkap</label><br>
        <input type="text" name="nama" placeholder= "Masukkan nama lengkap..." required><br><br>
        
        <label>Password</label><br>
        <input type="password" name="password" placeholder= "Buat password..." required><br><br>
        
        <button type="submit" name="register">Daftar</button>
    </form>
    
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</body>
</html>