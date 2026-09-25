<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* =========================================================
   QUERY HANYA MENGAMBIL KOLOM YANG ADA DI DATABASE
========================================================= */
$query = "SELECT 
            pb.id_peminjam,
            pb.id_user,
            u.nama_karyawan,
            b.kode_buku,
            b.judul,
            pb.jumlah_pinjam,
            pb.tanggal_pinjam,
            pb.tanggal_jatuh_tempo
          FROM pinjam_buku pb
          JOIN users u ON pb.id_user = u.id_user
          JOIN buku b ON pb.nomorbuku = b.nomorbuku
          ORDER BY pb.id_peminjam DESC";

$data = mysqli_query($conn, $query);

if (!$data) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peminjaman Buku</title>
    <link rel="stylesheet" href="../assets/sidebar.css">
    <link rel="stylesheet" href="../assets/peminjam.css">
</head>

<body>

    <button class="menu-btn" id="menuBtn">☰</button>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">📚 MENU</div>
        <div class="nav-item">
            <a href="../dashboard/pinjam_buku.php">📖 Pinjam Buku</a>
            <a href="daftar_pinjam.php" class="active">📋 Daftar Peminjaman</a>
            <a href="../auth/logout.php">🚪 Logout</a>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <main class="container">
        <section class="header">
            <div>
                <h1>📋 Daftar Peminjaman Buku</h1>
                <p>Data riwayat dan transaksi peminjaman buku</p>
            </div>
            <a href="pinjam_buku.php" class="btn-tambah" style="text-decoration: none; display: inline-block;">
                ➕ Pinjam Buku Baru
            </a>
        </section>

        <section class="card">
            <div class="card-header">
                <h2>📚 Transaksi Peminjaman</h2>
                <div class="search-box">
                    🔍 <input type="text" id="searchInput" placeholder="Cari peminjaman...">
                </div>
            </div>

            <div class="table-container">
                <table id="peminjamTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peminjam</th>
                            <th>Kode Buku</th>
                            <th>Judul Buku</th>
                            <th>Jumlah</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if (mysqli_num_rows($data) > 0):
                            while ($row = mysqli_fetch_assoc($data)):
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td class="nama"><?= htmlspecialchars($row['nama_karyawan']); ?></td>
                            <td><?= htmlspecialchars($row['kode_buku']); ?></td>
                            <td><?= htmlspecialchars($row['judul']); ?></td>
                            <td><?= htmlspecialchars($row['jumlah_pinjam']); ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pinjam']); ?></td>
                            <td><?= htmlspecialchars($row['tanggal_jatuh_tempo']); ?></td>
                           
                            <td class="aksi">
                                <?php if($row['id_user'] == $_SESSION['id_user']): ?>
                                    <a href="hapus_daftar_pinjam.php?id=<?= $row['id_peminjam']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus data peminjaman ini?');">
                                        🗑 Hapus
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="8" class="data-kosong">📭 Belum ada transaksi peminjaman buku</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="../assets/sidebar.js"></script>
    <script src="../assets/peminjam.js"></script>
</body>
</html>