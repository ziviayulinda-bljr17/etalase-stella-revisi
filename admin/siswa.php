<?php
session_start();
include '../config/kuis_db.php';
include '../middleware/auth_check.php';
cek_role(['sa', 'admin']); // Hanya SA & Admin yang boleh masuk

// Proses hapus siswa
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM users WHERE id='$id' AND role='siswa'");
    header("Location: siswa.php");
}

// Proses reset password jadi "12345"
if (isset($_GET['reset'])) {
    $id = $_GET['reset'];
    $pass_baru = password_hash('12345', PASSWORD_DEFAULT);
    mysqli_query($koneksi, "UPDATE users SET password='$pass_baru' WHERE id='$id' AND role='siswa'");
    header("Location: siswa.php");
}

// Ambil semua user dengan role 'siswa'
$siswa = mysqli_query($koneksi, "SELECT * FROM users WHERE role='siswa' ORDER BY id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Data Siswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <h2>Kelola Data Siswa</h2>
        <a href="../index.php" class="back">← Kembali ke Etalase</a>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
            </tr>
            <?php while($s = mysqli_fetch_assoc($siswa)): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['username']) ?></td>
                <td><?= htmlspecialchars($s['nama_lengkap']) ?></td>
                <td><?= date('d M Y', strtotime($s['created_at'])) ?></td>
                <td>
                    <a href="?reset=<?= $s['id'] ?>" 
                       onclick="return confirm('Reset password siswa ini jadi 12345?')">Reset Password</a>
                    &nbsp;|&nbsp;
                    <a href="?hapus=<?= $s['id'] ?>" 
                       onclick="return confirm('Yakin hapus siswa ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <?php if(mysqli_num_rows($siswa) == 0): ?>
            <p style="text-align:center; margin-top:20px; color:#8a6b78;">
                Belum ada siswa yang terdaftar.
            </p>
        <?php endif; ?>
    </div>
</body>
</html> 