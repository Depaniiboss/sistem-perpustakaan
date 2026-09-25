<?php
session_start();
include '../config/koneksi.php';

// Proteksi Halaman: Hanya Role 'admin' yang boleh mengakses
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$pesan = "";
$tipe_pesan = "";

// 1. PROSES EDIT / UPDATE ROLE USER
if (isset($_POST['update_role'])) {
    $id_user = (int)$_POST['id_user'];
    $role_baru = $_POST['role'];

    // Mencegah admin mengubah role-nya sendiri menjadi non-admin
    if ($id_user === (int)$_SESSION['id_user']) {
        $pesan = "Anda tidak dapat mengubah role Akun Anda sendiri!";
        $tipe_pesan = "error";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE users SET role = ? WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, "si", $role_baru, $id_user);
        
        if (mysqli_stmt_execute($stmt)) {
            $pesan = "Role user berhasil diperbarui!";
            $tipe_pesan = "sukses";
        } else {
            $pesan = "Gagal memperbarui role user!";
            $tipe_pesan = "error";
        }
    }
}

// 2. PROSES HAPUS USER
if (isset($_GET['hapus'])) {
    $id_user = (int)$_GET['hapus'];

    if ($id_user === (int)$_SESSION['id_user']) {
        $pesan = "Anda tidak dapat menghapus akun Anda sendiri!";
        $tipe_pesan = "error";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id_user = ?");
        mysqli_stmt_bind_param($stmt, "i", $id_user);
        
        if (mysqli_stmt_execute($stmt)) {
            $pesan = "User berhasil dihapus!";
            $tipe_pesan = "sukses";
        } else {
            $pesan = "Gagal menghapus user!";
            $tipe_pesan = "error";
        }
    }
}

// 3. AMBIL DATA SELURUH USER
$query = mysqli_query($conn, "SELECT id_user, username, nama_karyawan, role FROM users ORDER BY id_user DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Inventory Buku</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: 
                linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)),
                url("https://images.unsplash.com/photo-1568667256549-094345857637?fm=jpg&q=60&w=3000&auto=format&fit=crop")
                center/cover no-repeat fixed;
            padding: 30px 20px;
            color: #fff;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header h2 {
            font-size: 1.6rem;
        }

        .btn-kembali {
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-kembali:hover {
            background: rgba(255, 255, 255, 0.35);
        }

        /* Notifikasi */
        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        .alert.sukses {
            background: rgba(46, 125, 50, 0.85);
            border: 1px solid #81c784;
        }
        .alert.error {
            background: rgba(211, 47, 47, 0.85);
            border: 1px solid #e57373;
        }

        /* Tabel Data */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        th {
            background: rgba(0, 0, 0, 0.3);
            font-weight: 600;
            color: #4CAF50;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* Badge Role */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }
        .badge.admin { background: #1976d2; }
        .badge.karyawan { background: #388e3c; }
        .badge.pengguna { background: #76757a; }

        /* Form Perubahan Role */
        .form-role {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        select {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            background: rgba(0, 0, 0, 0.4);
            color: #fff;
            outline: none;
            cursor: pointer;
        }

        select option {
            background: #222;
            color: #fff;
        }

        .btn-simpan {
            padding: 8px 14px;
            background: #2e7d32;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-simpan:hover {
            background: #1b5e20;
        }

        .btn-hapus {
            padding: 8px 12px;
            background: #c62828;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: 0.2s;
        }

        .btn-hapus:hover {
            background: #b71c1c;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>👥 Kelola Hak Akses / Role User</h2>
        <a href="../admin/admin-dashboard.php" class="btn-kembali">← Kembali ke Dashboard</a>
    </div>

    <?php if (!empty($pesan)) : ?>
        <div class="alert <?= $tipe_pesan ?>">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama User / Karyawan</th>
                    <th>Username</th>
                    <th>Role Saat Ini</th>
                    <th>Ubah Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($query)) : 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_karyawan']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <span class="badge <?= htmlspecialchars($row['role']) ?>">
                                <?= htmlspecialchars($row['role']) ?>
                            </span>
                        </td>
                        <td>
                            <!-- Form Ubah Role Langsung di Tabel -->
                            <form method="POST" class="form-role">
                                <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">
                                
                                <select name="role">
                                    <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="karyawan" <?= $row['role'] === 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
                                    <option value="pengguna" <?= $row['role'] === 'pengguna' ? 'selected' : '' ?>>Pengguna</option>
                                </select>
                                
                                <button type="submit" name="update_role" class="btn-simpan">Simpan</button>
                            </form>
                        </td>
                        <td>
                            <?php if ($row['id_user'] != $_SESSION['id_user']) : ?>
                                <a href="kelola_user.php?hapus=<?= $row['id_user'] ?>" 
                                   class="btn-hapus" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                   Hapus
                                </a>
                            <?php else : ?>
                                <small style="color: #aaa;">(Akun Anda)</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>