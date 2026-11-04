<?php
session_start();
include 'config/kuis_db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

// 1. LOGIKA PENCARIAN
$keyword = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';

if ($keyword != '') {
    $query = "SELECT projects.*, users.nama_lengkap, users.role 
              FROM projects 
              JOIN users ON projects.user_id = users.id 
              WHERE projects.judul LIKE '%$keyword%' 
              OR users.nama_lengkap LIKE '%$keyword%' 
              ORDER BY projects.created_at DESC";
} else {
    $query = "SELECT projects.*, users.nama_lengkap, users.role 
              FROM projects 
              JOIN users ON projects.user_id = users.id 
              ORDER BY projects.created_at DESC";
}
$result = mysqli_query($koneksi, $query);
// Hitung total semua project di database
$query_total = mysqli_query($koneksi, "SELECT id FROM projects");
$total_karya = mysqli_num_rows($query_total);
// Cari 3 Siswa Paling Aktif (banyak upload project)
$query_top = "SELECT users.nama_lengkap, users.username, COUNT(projects.id) AS jumlah_project 
              FROM users 
              JOIN projects ON users.id = projects.user_id 
              WHERE users.role = 'siswa' 
              GROUP BY users.id 
              ORDER BY jumlah_project DESC 
              LIMIT 3";
$result_top = mysqli_query($koneksi, $query_top);

$foto_navbar = null;
$inisial_navbar = '';
if (isset($_SESSION['user_id'])) {
    $id_login = $_SESSION['user_id'];
    $q_user = mysqli_query($koneksi, "SELECT nama_lengkap, foto FROM users WHERE id='$id_login'");
    $data_user = mysqli_fetch_assoc($q_user);
    
    // Bikin inisial dari huruf depan
    $inisial_navbar = strtoupper(substr($data_user['nama_lengkap'], 0, 1));
    
    // Cek apakah punya foto
    if (!empty($data_user['foto']) && file_exists("assets/img/" . $data_user['foto'])) {
        $foto_navbar = $data_user['foto'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>E-Talase Digital Stella</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <nav class="navbar">
        <h1>
            <img src="assets/img/logo-telkom.png" alt="Logo Telkom" style="height: 35px; vertical-align: middle; margin-right: 12px; background: white; padding: 3px 8px; border-radius: 5px;">
            E-Talase Digital Stella
        </h1>
        
        <div class="nav-menu">
            <?php if(isset($_SESSION['user_id'])): ?>
               <a href="profil.php" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
                <!-- Avatar Kecil -->
                <div class="avatar-navbar">
                    <?php if($foto_navbar): ?>
                        <img src="assets/img/<?= $foto_navbar ?>" alt="Profil">
                    <?php else: ?>
                        <?= $inisial_navbar ?>
                    <?php endif; ?>
                </div>
                <!-- Teks Halo -->
                <span class="user-badge" style="cursor: pointer;">Halo, <?= $_SESSION['nama'] ?> (<?= $_SESSION['role'] ?>)</span>
                </a>
                <a href="project_tambah.php">+ Tambah Project</a>
                <?php if($_SESSION['role'] == 'sa'): ?>
                    <a href="admin/users.php">Kelola Users</a>
                <?php endif; ?>
                <?php if(in_array($_SESSION['role'], ['sa','admin'])): ?>
                    <a href="admin/siswa.php">Kelola Siswa</a>
                <?php endif; ?>
                <a href="auth/logout.php" class="btn-nav-logout">Logout</a>
            <?php else: ?>
                <a href="auth/login.php" class="btn-login">Login</a>
                <a href="auth/register.php" class="btn-register">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-banner">
            <div class="banner-content">
                <h2> Selamat Datang di E-Talase Digital Stella! </h2>
                <p>Tempat berkarya dan berbagi project keren para siswa.</p>
            </div>
        </div> 

        <h2>Etalase Project Siswa</h2>

        <!-- TOP 3 SISWA PALING AKTIF -->
        <div class="top-siswa-container">
            <h3>🏆 Siswa Paling Aktif</h3>
            <div class="top-siswa-grid">
                <?php 
                $rank = 1;
                while($top = mysqli_fetch_assoc($result_top)): 
                    // Bikin inisial
                    $inisial_top = strtoupper(substr($top['nama_lengkap'], 0, 1));
                ?>
                    <div class="top-card">
                        <div class="medal">#<?= $rank ?></div>
                        <div class="avatar-top"><?= $inisial_top ?></div>
                        <p style="font-weight: bold; margin-top: 5px;"><?= htmlspecialchars($top['nama_lengkap']) ?></p>
                        <small><?= $top['jumlah_project'] ?> Project</small>
                    </div>
                <?php 
                $rank++;
                endwhile; 
                
                // Kalau belum ada project sama sekali
                if($rank == 1) echo "<p style='color: #555;'>Belum ada data siswa yang aktif.</p>";
                ?>
            </div>
        </div>

        <div style ="margin-bottom: 20px;">
            <div class="total-badge">
                Total: <span><?= $total_karya ?></span> Karya Siswa
            </div>
        </div>    

        <!-- 2. FORM PENCARIAN DITARUH DI SINI -->
        <form method="GET" action="index.php" style="margin-bottom: 20px;">
            <input type="text" name="cari" placeholder="Cari judul project atau nama pembuat..." 
                   value="<?= isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : '' ?>"
                   style="padding: 10px 15px; width: 350px; border: 1px solid #ccc; border-radius: 5px; outline: none;">
            <button type="submit" style="padding: 10px 20px; background: #ED1E28; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer;">Cari</button>
            <?php if(isset($_GET['cari']) && $_GET['cari'] != ''): ?>
                <a href="index.php" style="margin-left: 10px; color: #55565B; text-decoration: none; font-size: 14px;">❌ Hapus Pencarian</a>
            <?php endif; ?>
        </form>

        <div class="project-grid">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="project-card">
                    
                    <!-- Gambar (Cek apakah file beneran ada) -->
                    <?php 
                    $file_gambar = "assets/img/" . $row['gambar'];
                    if($row['gambar'] && file_exists($file_gambar)): ?>
                        <a href="project_detail.php?id=<?= $row['id'] ?>">
                            <img src="<?= $file_gambar ?>" alt="Gambar Project">
                        </a>
                    <?php endif; ?>

                    <!-- Judul -->
                    <h3>
                        <a href="project_detail.php?id=<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['judul']) ?>
                        </a>
                    </h3>
                    
                    <p><?= htmlspecialchars($row['deskripsi']) ?></p>
                    <small>Oleh: <?= $row['nama_lengkap'] ?></small><br>
                    
                    <!-- TOMBOL-TOMBOL -->
                    <?php if($row['link_publishing']): ?>
                        <a href="<?= $row['link_publishing'] ?>" target="_blank" class="btn-action">🔗 Link</a>
                    <?php endif; ?>
                    <?php if($row['link_demo']): ?>
                        <a href="<?= $row['link_demo'] ?>" target="_blank" class="btn-action">🎬 Demo</a>
                    <?php endif; ?>
                    
                    <?php 
                    $boleh_edit = false;
                    if (isset($_SESSION['user_id'])) {
                        if ($_SESSION['user_id'] == $row['user_id'] || in_array($_SESSION['role'], ['sa','admin'])) {
                            $boleh_edit = true;
                        }
                    }
                    if ($boleh_edit): ?>
                        <a href="project_edit.php?id=<?= $row['id'] ?>" class="btn-action btn-edit">Edit</a>
                        <a href="project_hapus.php?id=<?= $row['id'] ?>" class="btn-action btn-hapus" onclick="return confirm('Yakin hapus?')">Hapus</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer class="footer">
        <p> E-Talase Digital Stella - Wadah Karya Siswa Hebat </p>
        <p>© 2026 - Dibuat oleh Zivia Yulinda</p>
    </footer>
</body>
</html>