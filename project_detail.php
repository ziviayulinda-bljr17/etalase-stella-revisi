<?php
session_start();
include 'config/kuis_db.php';

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT projects.*, users.nama_lengkap, users.role 
                                 FROM projects 
                                 JOIN users ON projects.user_id = users.id 
                                 WHERE projects.id='$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Project tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($data['judul']) ?> - E-Talase Digital Stella</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/form.css">
</head>
<body>
    <nav class="navbar">
        <h1>
            <a href="index.php" style="color: white; text-decoration: none; font-size: 18px;">← Kembali ke Etalase</a>
        </h1>
        <div class="nav-menu">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="user-badge">Halo, <?= $_SESSION['nama'] ?></span>
                <a href="auth/logout.php" class="btn-nav-logout">Logout</a>
            <?php else: ?>
                <a href="auth/login.php" class="btn-login">Login</a>
                <a href="auth/register.php" class="btn-register">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container" style="max-width: 800px;">
        <div class="form-container" style="text-align: left; padding: 30px; background: white; border-radius: 10px; border: 1px solid #ddd; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
            
            <h2 style="color: #ED1E28; font-size: 28px; margin-bottom: 5px; border-bottom: none; text-align: left;"><?= htmlspecialchars($data['judul']) ?></h2>
            
            <p style="color: #55565B; font-size: 14px; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 15px;">
                Dibuat oleh: <strong><?= htmlspecialchars($data['nama_lengkap']) ?></strong> 
                (<?= $data['role'] ?>) | 
                Tanggal: <?= date('d M Y', strtotime($data['created_at'])) ?>
            </p>

            <?php if($data['gambar']): ?>
                <img src="assets/img/<?= $data['gambar'] ?>" alt="Gambar Project" style="width: 100%; max-height: 450px; object-fit: cover; border-radius: 10px; margin-bottom: 20px; border: 1px solid #ddd;">
            <?php endif; ?>

            <h3 style="color: #333; margin-bottom: 10px;">Deskripsi Project</h3>
            <p style="color: #55565B; line-height: 1.8; margin-bottom: 30px; white-space: pre-wrap;"><?= htmlspecialchars($data['deskripsi']) ?></p>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <?php if($data['link_publishing']): ?>
                    <a href="<?= $data['link_publishing'] ?>" target="_blank" class="btn-login" style="border-color: #ED1E28; color: #ED1E28; text-decoration: none; padding: 10px 20px; font-weight: bold; border-radius: 5px;">🔗 Buka Website</a>
                <?php endif; ?>
                
                <?php if($data['link_demo']): ?>
                    <a href="<?= $data['link_demo'] ?>" target="_blank" class="btn-register" style="text-decoration: none; padding: 10px 20px; font-weight: bold; border-radius: 5px;">🎬 Lihat Video Demo</a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</body>
</html>