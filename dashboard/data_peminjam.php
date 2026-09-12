<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$data = mysqli_query($conn, "
    SELECT *
    FROM peminjam
    ORDER BY id_peminjam DESC
");

if (!$data) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Peminjam</title>

    <link rel="stylesheet" href="../assets/sidebar.css">
    <link rel="stylesheet" href="../assets/style_pinjam.css">
</head>

<body>

    <!-- ================= SIDEBAR ================= -->

    <button class="menu-btn" id="menuBtn">
        ☰
    </button>

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-header">
            📚 <span>MENU</span>
        </div>

        <nav class="nav-menu">

            <a href="../dashboard/index.php">
                🏠
                <span>Dashboard</span>
            </a>

            <a href="../dashboard/pinjam_buku.php">
                📖
                <span>Pinjam Buku</span>
            </a>

            <a href="peminjam.php" class="active">
                👤
                <span>Data Peminjam</span>
            </a>

            <a href="#">
                ⚙️
                <span>Pengaturan</span>
            </a>

            <a href="../auth/logout.php" class="logout">
                🚪
                <span>Logout</span>
            </a>

        </nav>

    </aside>

    <div class="overlay" id="overlay"></div>


    <!-- ================= MAIN ================= -->

    <main class="container">

        <!-- HEADER -->

        <section class="page-header">

            <div class="title">

                <h1>
                    👤 Data Peminjam
                </h1>

                <p>
                    Kelola data orang yang meminjam buku
                </p>

            </div>

            <button
                class="btn-tambah"
                id="btnTambah">

                ➕ Tambah Peminjam

            </button>

        </section>


        <!-- ================= CARD ================= -->

        <section class="card">

            <div class="card-header">

                <div>

                    <h2>
                        📋 Daftar Peminjam
                    </h2>

                    <p>
                        Data peminjam yang terdaftar
                    </p>

                </div>


                <!-- SEARCH -->

                <div class="search-box">

                    <span>🔍</span>

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Cari nama peminjam...">

                </div>

            </div>


            <!-- ================= TABLE ================= -->

            <div class="table-container">

                <table id="peminjamTable">

                    <thead>

                        <tr>

                            <th width="60">
                                No
                            </th>

                            <th>
                                Nama Peminjam
                            </th>

                            <th>
                                Kelas
                            </th>

                            <th>
                                No. Telepon
                            </th>

                            <th>
                                Alamat
                            </th>

                            <th width="160">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php

                        $no = 1;

                        if (mysqli_num_rows($data) > 0):

                            while ($row = mysqli_fetch_assoc($data)):

                        ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>


                            <td>

                                <div class="nama-wrapper">

                                    <div class="avatar">
                                        <?= strtoupper(substr($row['nama_peminjam'], 0, 1)); ?>
                                    </div>

                                    <span class="nama">

                                        <?= htmlspecialchars(
                                            $row['nama_peminjam']
                                        ); ?>

                                    </span>

                                </div>

                            </td>


                            <td>

                                <span class="badge-kelas">

                                    <?= htmlspecialchars(
                                        $row['kelas']
                                    ); ?>

                                </span>

                            </td>


                            <td>

                                <?= !empty($row['no_telp'])
                                    ? htmlspecialchars($row['no_telp'])
                                    : '<span class="kosong">-</span>';
                                ?>

                            </td>


                            <td>

                                <?= !empty($row['alamat'])
                                    ? htmlspecialchars($row['alamat'])
                                    : '<span class="kosong">-</span>';
                                ?>

                            </td>


                            <td>

                                <div class="aksi">

                                    <a
                                        href="edit_peminjam.php?id=<?= $row['id_peminjam']; ?>"
                                        class="btn-edit">

                                        📝 Edit

                                    </a>


                                    <a
                                        href="hapus_peminjam.php?id=<?= $row['id_peminjam']; ?>"
                                        class="btn-delete"
                                        onclick="return confirm('Yakin ingin menghapus <?= htmlspecialchars($row['nama_peminjam']); ?>?');">

                                        🗑

                                    </a>

                                </div>

                            </td>

                        </tr>

                        <?php

                            endwhile;

                        else:

                        ?>

                        <tr>

                            <td
                                colspan="6"
                                class="data-kosong">

                                <div class="empty-icon">
                                    📭
                                </div>

                                <strong>
                                    Belum ada data peminjam
                                </strong>

                                <p>
                                    Klik tombol "Tambah Peminjam"
                                    untuk menambahkan data.
                                </p>

                            </td>

                        </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </main>


    <!-- ================= MODAL ================= -->

    <div
        class="modal"
        id="modalTambah">

        <div class="modal-content">


            <!-- MODAL HEADER -->

            <div class="modal-header">

                <div>

                    <h2>
                        👤 Tambah Peminjam
                    </h2>

                    <p>
                        Tambahkan data peminjam baru
                    </p>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    id="btnClose">

                    ×

                </button>

            </div>


            <!-- FORM -->

            <form
                id="formPeminjam"
                method="POST"
                action="tambah_peminjam.php">


                <!-- NAMA -->

                <div class="form-group">

                    <label>
                        Nama Peminjam
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="nama_peminjam"
                        placeholder="Contoh: Gede Parel Gaudyas"
                        required>

                </div>


                <!-- KELAS -->

                <div class="form-group">

                    <label>
                        Kelas
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        name="kelas"
                        placeholder="Contoh: X RPL 1"
                        required>

                </div>


                <!-- TELEPON -->

                <div class="form-group">

                    <label>
                        No. Telepon
                    </label>

                    <input
                        type="text"
                        name="no_telp"
                        placeholder="Contoh: 081234567890">

                </div>


                <!-- ALAMAT -->

                <div class="form-group">

                    <label>
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        rows="3"
                        placeholder="Masukkan alamat peminjam"></textarea>

                </div>


                <!-- BUTTON -->

                <div class="form-buttons">

                    <button
                        type="button"
                        class="btn-batal"
                        id="btnBatal">

                        Batal

                    </button>


                    <button
                        type="submit"
                        class="btn-simpan">

                        💾 Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- JAVASCRIPT -->

    <script src="../assets/sidebar.js"></script>
    <script src="../assets/peminjam.js"></script>

</body>

</html>