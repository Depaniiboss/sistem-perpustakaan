<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$data = mysqli_query($conn, "
    SELECT *
    FROM buku
    ORDER BY nomorbuku ASC
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
    <link rel="icon" type="image/jpeg" href="../assets/icon.jpeg">

    <title>Dashboard Inventory Buku</title>

    <!-- CSS Sidebar -->
    <link rel="stylesheet" href="../assets/sidebar.css">

    <!-- CSS Dashboard -->
    <link rel="stylesheet" href="../assets/dashboard.css">
</head>

<body>

    <!-- Tombol Menu -->
    <button class="menu-btn" id="menuBtn">☰</button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">

        <div class="sidebar-header">
            📚 MENU
        </div>

        <div class="nav-item">

            <a href="pinjam_buku.php">
                📚 Pinjam Buku
            </a>

            <a href="../dashboard/daftar_pinjam.php">
                📖 Daftar Peminjaman
            </a>

            <a href="../auth/logout.php">
                🚪 Logout
            </a>

        </div>

    </div>

    <!-- Overlay -->
    <div class="overlay" id="overlay"></div>

    <!-- Container -->
    <div class="container">

        <!-- Header -->
        <div class="header">

            <div>

                <h1>Liblary</h1>

                <p>
                    Selamat datang,
                    <strong>
                        <?= htmlspecialchars($_SESSION['nama_karyawan']); ?>
                    </strong>
                </p>

            </div>

        </div>

        <!-- Card -->
        <div class="card">

            <div class="card-header">

                <h2>📚 Buku Tersedia Saat Ini</h2>

            </div>

            <!-- Tabel Buku -->
            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Kode Buku</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                    </tr>

                </thead>

                <tbody>

                    <?php
                    $no = 1;

                    while ($row = mysqli_fetch_assoc($data)) {
                    ?>

                        <tr>

                            <td>
                                <?= $no++; ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['kode_buku']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['judul']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['kategori']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['penulis']); ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($row['penerbit']); ?>
                            </td>

                        </tr>

                    <?php
                    }
                    ?>

                </tbody>

            </table>

        </div>

    </div>

    <!-- JavaScript Sidebar -->
    <script src="../assets/sidebar.js"></script>

    <!-- JavaScript Dashboard -->
    <script src="../assets/dashboard.js"></script>

</body>
<script>
history.pushState(null, "", location.href);

window.addEventListener("popstate", function () {
    history.pushState(null, "", location.href);
    location.reload();
});
</script>
</html>
```
