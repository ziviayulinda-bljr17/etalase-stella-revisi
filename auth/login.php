<?php
session_start();
include '../config/kuis_db.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    $user = mysqli_fetch_assoc($query);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        
        header("Location: ../index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - E-Talase Stella</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="auth-container">
        <h2>Login E-Talase Stella</h2>
    
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    
    <form method="POST">
        <label>Username</label><br>
        <input type="text" name="username" placeholder="Masukkan username..." required><br><br>
        
        <label>Password</label><br>
        <input type="password" name="password" placeholder= "Masukkan password..." required><br><br>
        
        <button type="submit" name="login">Login</button>
    </form>
    
    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>
</html>