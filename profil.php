<?php
session_start();
include 'config/kuis_db.php';
include 'middleware/auth_check.php';
cek_login(); // Wajib login untuk lihat profil

$user_id = $_SESSION['user_id'];

// Ambil data user dari database
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($query);

// LOGIKA INISIAL OTOMATIS
$nama_lengkap = $user['nama_lengkap'];
$inisial = strtoupper(substr($nama_lengkap, 0, 1)); // Ambil huruf pertama

// Cek apakah user punya foto di database DAN file fotonya ada di folder
$punya_foto = false;
if (!empty($user['foto']) && file_exists("assets/img/" . $user['foto'])) {
    $punya_foto = true;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profil Saya - E-Talase Digital Stella</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <!-- CSS Khusus buat profil (kita bikin nanti) -->
    <style>
        .profil-container {
            max-width: 500px;
            margin: 50px auto;
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(15px);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
        }
        
        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #ED1E28; /* Merah Telkom */
            color: white;
            font-size: 40px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            box-shadow: 0 4px 10px rgba(237, 30, 40, 0.3);
        }

        .avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .profil-container h2 {
            color: #ED1E28;
            margin-bottom: 5px;
        }

        .info-box {
            background: rgba(255, 255, 255, 0.6);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            text-align: left;
        }

        .info-box label {
            font-size: 12px;
            color: #777;
            display: block;
            margin-bottom: 3px;
        }

        .info-box p {
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>
            <img src="assets/img/logo-telkom.png" alt="Logo Telkom" style="height: 35px; vertical-align: middle; margin-right: 12px; background: white; padding: 3px 8px; border-radius: 5px;">
            Profil Saya
        </h1>
        <div class="nav-menu">
            <a href="index.php">← Kembali ke Etalase</a>
        </div>
    </nav>

    <div class="profil-container">
        <!-- TAMPILAN AVATAR (FOTO ATAU INISIAL) -->
        <div class="avatar">
            <?php if($punya_foto): ?>
                <img src="assets/img/<?= $user['foto'] ?>" alt="Foto Profil">
            <?php else: ?>
                <?= $inisial ?>
            <?php endif; ?>
        </div>

        <h2><?= htmlspecialchars($user['nama_lengkap']) ?></h2>
        <p style="color: #55565B; margin-bottom: 30px;">@<?= htmlspecialchars($user['username']) ?></p>

        <div class="info-box">
            <label>Role / Hak Akses</label>
            <p><?= strtoupper($user['role']) ?></p>
        </div>

        <div class="info-box">
            <label>Tanggal Bergabung</label>
            <p><?= date('d F Y', strtotime($user['created_at'])) ?></p>
        </div>

        <!-- Tombol Kembali -->
        <a href="index.php" style="display: inline-block; margin-top: 20px; background: #ED1E28; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold;">Kembali</a>
    </div>
</body>
</html>