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

// Cek hak akses: hanya pemilik, admin, atau SA yang boleh edit
if ($_SESSION['user_id'] != $data['user_id'] && !in_array($_SESSION['role'], ['sa','admin'])) {
    echo "<script>alert('Akses ditolak!'); 
          window.location='index.php';</script>";
    exit;
}

if (isset($_POST['update'])) {
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $desk     = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link_pub = mysqli_real_escape_string($koneksi, $_POST['link_publishing']);
    $link_demo= mysqli_real_escape_string($koneksi, $_POST['link_demo']);
    
    // Kalau user upload gambar baru
    if (!empty($_FILES['gambar']['name'])) {
        // Hapus gambar lama
        if ($data['gambar'] && file_exists("assets/img/" . $data['gambar'])) {
            unlink("assets/img/" . $data['gambar']);
        }
        $gambar = time() . "_" . basename($_FILES['gambar']['name']);
        move_uploaded_file($_FILES['gambar']['tmp_name'], "assets/img/" . $gambar);
        mysqli_query($koneksi, "UPDATE projects SET gambar='$gambar' WHERE id='$id'");
    }
    
    $q = "UPDATE projects SET judul='$judul', deskripsi='$desk', 
          link_publishing='$link_pub', link_demo='$link_demo' WHERE id='$id'";
    
    if (mysqli_query($koneksi, $q)) {
        echo "<script>alert('Project berhasil diupdate!'); 
              window.location='index.php';</script>";
    } else {
        $error = "Gagal update: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Project</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
    <div class="form-container">
        <h2>Edit Project</h2>
    
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <label>Judul</label><br>
        <input type="text" name="judul" value="<?= htmlspecialchars($data['judul']) ?>" required><br><br>
        
        <label>Deskripsi</label><br>
        <textarea name="deskripsi" rows="5" required><?= htmlspecialchars($data['deskripsi']) ?></textarea><br><br>
        
        <label>Gambar Baru (Kosongkan jika tidak ganti)</label><br>
        <?php if($data['gambar']): ?>
            <img src="assets/img/<?= $data['gambar'] ?>" width="150"><br>
        <?php endif; ?>
        <input type="file" name="gambar" accept="image/*"><br><br>
        
        <label>Link Publishing</label><br>
        <input type="text" name="link_publishing" value="<?= htmlspecialchars($data['link_publishing']) ?>"><br><br>
        
        <label>Link Demo</label><br>
        <input type="text" name="link_demo" value="<?= htmlspecialchars($data['link_demo']) ?>"><br><br>
        
        <button type="submit" name="update">Update</button>
        <a href="index.php">Batal</a>
    </form>
    </div>
</body>
</html>