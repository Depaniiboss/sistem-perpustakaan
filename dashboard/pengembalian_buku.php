<?php
session_start();
require_once '../config/koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}

// Simpan tanggal pengembalian tanpa menghapus riwayat transaksi peminjaman.
$buatTabel = mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS pengembalian_buku (
        id_pengembalian BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        id_peminjam BIGINT UNSIGNED NOT NULL,
        tanggal_kembali DATE NOT NULL,
        PRIMARY KEY (id_pengembalian),
        UNIQUE KEY unik_pengembalian_peminjaman (id_peminjam)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
");
if (!$buatTabel) {
    die('Gagal menyiapkan data pengembalian: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}

$pesan = '';
$tipePesan = '';
if (empty($_SESSION['csrf_pengembalian'])) {
    $_SESSION['csrf_pengembalian'] = bin2hex(random_bytes(32));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPeminjam = filter_input(INPUT_POST, 'id_peminjam', FILTER_VALIDATE_INT);
    $tanggalKembali = $_POST['tanggal_kembali'] ?? '';
    $tanggalValid = is_string($tanggalKembali) ? DateTime::createFromFormat('!Y-m-d', $tanggalKembali) : false;
    $valid = $tanggalValid && $tanggalValid->format('Y-m-d') === $tanggalKembali;
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf_pengembalian'], $token)) {
        $pesan = 'Permintaan tidak valid. Muat ulang halaman lalu coba kembali.';
        $tipePesan = 'error';
    } elseif (!$idPeminjam || !$valid) {
        $pesan = 'Pilih transaksi dan tanggal pengembalian yang valid.';
        $tipePesan = 'error';
    } elseif ($tanggalKembali > date('Y-m-d')) {
        $pesan = 'Tanggal pengembalian tidak boleh melebihi hari ini.';
        $tipePesan = 'error';
    } else {
        $cek = mysqli_prepare($conn, "
            SELECT pb.tanggal_pinjam
            FROM pinjam_buku pb
            LEFT JOIN pengembalian_buku pg ON pg.id_peminjam = pb.id_peminjam
            WHERE pb.id_peminjam = ? AND pg.id_peminjam IS NULL
        ");
        mysqli_stmt_bind_param($cek, 'i', $idPeminjam);
        mysqli_stmt_execute($cek);
        $transaksi = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));

        if (!$transaksi) {
            $pesan = 'Transaksi tidak ditemukan atau buku sudah dikembalikan.';
            $tipePesan = 'error';
        } elseif ($tanggalKembali < $transaksi['tanggal_pinjam']) {
            $pesan = 'Tanggal pengembalian tidak boleh sebelum tanggal peminjaman.';
            $tipePesan = 'error';
        } else {
            $simpan = mysqli_prepare($conn, 'INSERT INTO pengembalian_buku (id_peminjam, tanggal_kembali) VALUES (?, ?)');
            mysqli_stmt_bind_param($simpan, 'is', $idPeminjam, $tanggalKembali);
            if (mysqli_stmt_execute($simpan)) {
                header('Location: pengembalian_buku.php?status=success');
                exit;
            }
            $pesan = 'Pengembalian gagal disimpan. Silakan coba lagi.';
            $tipePesan = 'error';
            mysqli_stmt_close($simpan);
        }
        mysqli_stmt_close($cek);
    }
}

if (($_GET['status'] ?? '') === 'success') {
    $pesan = 'Pengembalian buku berhasil dicatat.';
    $tipePesan = 'sukses';
}

$pinjamanAktif = mysqli_query($conn, "
    SELECT pb.id_peminjam, u.nama_karyawan, b.kode_buku, b.judul,
           pb.jumlah_pinjam, pb.tanggal_pinjam, pb.tanggal_jatuh_tempo
    FROM pinjam_buku pb
    JOIN users u ON u.id_user = pb.id_user
    JOIN buku b ON b.nomorbuku = pb.nomorbuku
    LEFT JOIN pengembalian_buku pg ON pg.id_peminjam = pb.id_peminjam
    WHERE pg.id_peminjam IS NULL
    ORDER BY pb.tanggal_jatuh_tempo ASC, pb.id_peminjam DESC
");
$riwayat = mysqli_query($conn, "
    SELECT u.nama_karyawan, b.kode_buku, b.judul, pb.jumlah_pinjam,
           pb.tanggal_pinjam, pg.tanggal_kembali
    FROM pengembalian_buku pg
    JOIN pinjam_buku pb ON pb.id_peminjam = pg.id_peminjam
    JOIN users u ON u.id_user = pb.id_user
    JOIN buku b ON b.nomorbuku = pb.nomorbuku
    ORDER BY pg.tanggal_kembali DESC, pg.id_pengembalian DESC
");
if (!$pinjamanAktif || !$riwayat) {
    die('Gagal mengambil data pengembalian: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}
$jumlahAktif = mysqli_num_rows($pinjamanAktif);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku</title>
    <link rel="stylesheet" href="../assets/sidebar.css">
    <link rel="stylesheet" href="../assets/peminjam.css">
    <link rel="stylesheet" href="../assets/pengembalian_buku.css">
</head>
<body>
    <button class="menu-btn" id="menuBtn">☰</button>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">📚 MENU</div>
        <div class="nav-item">
            <a href="sub-index.php">🏠 Dashboard</a>
            <a href="pinjam_buku.php">📖 Pinjam Buku</a>
            <a href="daftar_pinjam.php">📋 Daftar Peminjaman</a>
            <a href="pengembalian_buku.php" class="active">↩️ Pengembalian Buku</a>
            <a href="../auth/logout.php">🚪 Logout</a>
        </div>
    </div>
    <div class="overlay" id="overlay"></div>

    <main class="container">
        <section class="header">
            <div>
                <h1>↩️ Pengembalian Buku</h1>
                <p>Catat buku yang sudah dikembalikan</p>
            </div>
        </section>
        <?php if ($pesan !== ''): ?>
            <div class="alert <?= htmlspecialchars($tipePesan, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <section class="card">
            <div class="card-header"><h2>📝 Form Pengembalian</h2></div>
            <form method="post" action="pengembalian_buku.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_pengembalian'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="form-group">
                    <label for="id_peminjam">Peminjaman Aktif</label>
                    <select id="id_peminjam" name="id_peminjam" required>
                        <option value="">-- Pilih peminjam dan buku --</option>
                        <?php while ($row = mysqli_fetch_assoc($pinjamanAktif)): ?>
                            <option value="<?= (int)$row['id_peminjam'] ?>">
                                <?= htmlspecialchars($row['nama_karyawan'], ENT_QUOTES, 'UTF-8') ?> —
                                <?= htmlspecialchars($row['kode_buku'], ENT_QUOTES, 'UTF-8') ?> ·
                                <?= htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8') ?>
                                (pinjam <?= htmlspecialchars($row['tanggal_pinjam'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?php if ($jumlahAktif === 0): ?>
                        <small>Tidak ada peminjaman aktif yang menunggu pengembalian.</small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="tanggal_kembali">Tanggal Pengembalian</label>
                    <input type="date" id="tanggal_kembali" name="tanggal_kembali" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-buttons">
                    <a class="btn-batal" href="daftar_pinjam.php">Kembali</a>
                    <button class="btn-simpan" type="submit" <?= $jumlahAktif === 0 ? 'disabled' : '' ?>>Catat Pengembalian</button>
                </div>
            </form>
        </section>

        <section class="card" style="margin-top: 25px;">
            <div class="card-header"><h2>📚 Riwayat Pengembalian</h2></div>
            <div class="table-container">
                <table>
                    <thead><tr><th>No</th><th>Peminjam</th><th>Kode Buku</th><th>Judul Buku</th><th>Jumlah</th><th>Tgl Pinjam</th><th>Tgl Kembali</th></tr></thead>
                    <tbody>
                    <?php if (mysqli_num_rows($riwayat) === 0): ?>
                        <tr><td colspan="7" class="data-kosong">📭 Belum ada pengembalian yang dicatat.</td></tr>
                    <?php else: $no = 1; while ($row = mysqli_fetch_assoc($riwayat)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_karyawan'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['kode_buku'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['judul'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int)$row['jumlah_pinjam'] ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pinjam'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['tanggal_kembali'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script src="../assets/sidebar.js"></script>
</body>
</html>
