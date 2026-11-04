<?php
session_start();
include 'config/kuis_db.php';
include 'middleware/auth_check.php';
cek_login(); // Hanya yang sudah login bisa tambah project

if (isset($_POST['simpan'])) {
    $user_id  = $_SESSION['user_id'];
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $desk     = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_pub = mysqli_real_escape_string($koneksi, $_POST['link_publishing']);
    $link_demo= mysqli_real_escape_string($koneksi, $_POST['link_demo']);
    
    // Upload gambar (opsional)
    $gambar = "";
    if (!empty($_FILES['gambar']['name'])) {
        $gambar = time() . "_" . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], "assets/img/" . $gambar);
    }
    
    $query = "INSERT INTO projects (user_id, judul, deskripsi, gambar, link_publishing, link_demo) 
              VALUES ('$user_id', '$judul', '$desk', '$gambar', '$link_pub', '$link_demo')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Project berhasil ditambah!'); 
              window.location='index.php';</script>";
    } else {
        $error = "Gagal: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Project</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
    <div class="form-container">
        <h2>Tambah Project</h2>
    
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <label>Judul Project</label><br>
        <input type="text" name="judul" required><br><br>
        
        <label>Deskripsi</label><br>
        <textarea name="deskripsi" rows="5" required></textarea><br><br>
        
        <label>Gambar (Opsional)</label><br>
        <input type="file" name="gambar" accept="image/*"><br><br>
        
        <label>Link Publishing (Opsional)</label><br>
        <input type="text" name="link_publishing"><br><br>
        
        <label>Link Demo (Opsional)</label><br>
        <input type="text" name="link_demo"><br><br>
        
        <button type="submit" name="simpan">Simpan</button>
        <a href="index.php">Batal</a>
    </form>
    </div>
</body>
</html>