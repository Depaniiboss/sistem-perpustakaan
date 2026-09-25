<?php
include '../config/koneksi.php';
include '../config/auth_check.php';

// Memastikan hanya Admin yang dapat mengakses halaman ini
check_access(['admin']);

// Proses update role jika form dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'])) {
    $id_user_target = intval($_POST['id_user']);
    $role_baru = $_POST['role'];

    // Validasi opsi role yang diperbolehkan
    $allowed_roles = ['admin', 'karyawan', 'pengguna'];
    
    if (in_array($role_baru, $allowed_roles)) {
        $stmt = mysqli_prepare($conn, "UPDATE users SET role = ? WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, "si", $role_baru, $id_user_target);
        
        if (mysqli_stmt_execute($stmt)) {
            $pesan_sukses = "Role berhasil diperbarui!";
        } else {
            $pesan_error = "Gagal memperbarui role: " . mysqli_error($conn);
        }
    }
}

// Mengambil seluruh data user dari database
$query = "SELECT id_user, username, nama_karyawan, role FROM users ORDER BY id_user ASC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Role User - Admin</title>
    <link rel="stylesheet" href="../assets/sidebar.css">
    <link rel="stylesheet" href="../assets/peminjam.css">
    <style>
        .role-select {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        .btn-update {
            background-color: #0288d1;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-update:hover {
            background-color: #01579b;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge-admin { background-color: #d32f2f; }
        .badge-karyawan { background-color: #f57c00; }
        .badge-pengguna { background-color: #388e3c; }
    </style>
</head>
<body>

    <button class="menu-btn" id="menuBtn">☰</button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">📚 MENU ADMIN</div>
        <div class="nav-item">
            <a href="index.php">🏠 Dashboard</a>
            <a href="kelola_user.php" class="active">👥 Kelola User</a>
            <a href="daftar_pinjam.php">📋 Daftar Peminjaman</a>
            <a href="../auth/logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <main class="container">
        <section class="header">
            <div>
                <h1>👥 Kelola User & Role</h1>
                <p>Ubah hak akses dan peranan pengguna dalam sistem</p>
            </div>
        </section>

        <section class="card">
            <?php if (isset($pesan_sukses)): ?>
                <div class="alert-success"><?= $pesan_sukses; ?></div>
            <?php endif; ?>

            <?php if (isset($pesan_error)): ?>
                <div class="alert-danger"><?= $pesan_error; ?></div>
            <?php endif; ?>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama Karyawan/User</th>
                            <th>Role Saat Ini</th>
                            <th>Ubah Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['nama_karyawan']); ?></td>
                            <td>
                                <span class="badge badge-<?= htmlspecialchars($row['role']); ?>">
                                    <?= strtoupper(htmlspecialchars($row['role'])); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: flex; gap: 8px; align-items: center;">
                                    <input type="hidden" name="id_user" value="<?= $row['id_user']; ?>">
                                    
                                    <select name="role" class="role-select">
                                        <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="karyawan" <?= $row['role'] === 'karyawan' ? 'selected' : ''; ?>>Karyawan</option>
                                        <option value="pengguna" <?= $row['role'] === 'pengguna' ? 'selected' : ''; ?>>Pengguna</option>
                                    </select>

                                    <button type="submit" name="update_role" class="btn-update">Simpan</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="../assets/sidebar.js"></script>
</body>
</html>