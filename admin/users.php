<?php
session_start();
include '../config/kuis_db.php';
include '../middleware/auth_check.php';
cek_role(['sa']); // HANYA Super Admin yang boleh masuk

// Proses hapus user
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Jangan biarkan SA menghapus dirinya sendiri
    if ($id != $_SESSION['user_id']) {
        mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");
    }
    header("Location: users.php");
}

// Proses ubah role
if (isset($_POST['ubah_role'])) {
    $id   = $_POST['id'];
    $role = $_POST['role'];
    // Cek agar tidak menurunkan role diri sendiri (opsional)
    if ($id != $_SESSION['user_id']) {
        mysqli_query($koneksi, "UPDATE users SET role='$role' WHERE id='$id'");
    }
    header("Location: users.php");
}

$users = mysqli_query($koneksi, "SELECT * FROM users ORDER BY role, id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Users - Super Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
    <h2>Kelola Users (Super Admin)</h2>
    <a href="../index.php" class="back">← Kembali ke Etalase</a>
    
    <table border="1" cellpadding="10" style="margin-top:20px; width:100%">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama Lengkap</th>
            <th>Role</th>
            <th>Aksi</th>
        </tr>
        <?php while($u = mysqli_fetch_assoc($users)): ?>
        <tr>
            <td><?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
            <td>
                <form method="POST" style="display:inline">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <select name="role">
                        <option value="siswa" <?= $u['role']=='siswa'?'selected':'' ?>>Siswa</option>
                        <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Admin</option>
                        <option value="sa"    <?= $u['role']=='sa'?'selected':'' ?>>SA</option>
                    </select>
                    <button type="submit" name="ubah_role">Ubah</button>
                </form>
            </td>
            <td>
                <?php if($u['id'] != $_SESSION['user_id']): ?>
                    <a href="?hapus=<?= $u['id'] ?>" 
                       onclick="return confirm('Yakin hapus user ini?')">Hapus</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>
</body>
</html>