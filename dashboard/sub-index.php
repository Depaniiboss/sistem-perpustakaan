<?php
session_start();
include '../config/koneksi.php';

/* Mengecek apakah user sudah login */
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* Mengambil semua data buku dari database */
$data = mysqli_query($conn, "
    SELECT *
    FROM buku
    ORDER BY nomorbuku ASC
");

/* Mengecek apakah query berhasil */
if (!$data) {
    die("Query Error: " . mysqli_error($conn));
}
$daftar_buku = mysqli_fetch_all($data, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="icon" type="image/jpeg" href="../assets/icon.jpeg">

    <title>Dashboard Inventory Buku</title>


    <!-- =================================================
         CSS LAMA
    ================================================== -->

    <link rel="stylesheet" href="../assets/sidebar.css">

    <link rel="stylesheet" href="../assets/dashboard.css">


    <!-- =================================================
         CSS TAMBAHAN
         Untuk membuat Header Liquid Glass
    ================================================== -->

    <style>

        /* ================================
           HEADER LIQUID GLASS
        ================================= */

        .header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            gap: 25px;

            padding: 25px 30px;

            margin-bottom: 25px;

            border-radius: 20px;

            /* Efek transparan */

            background: rgba(255, 255, 255, 0.12);

            /* Garis tipis */

            border: 1px solid rgba(255, 255, 255, 0.25);

            /* Efek blur */

            backdrop-filter: blur(18px);

            -webkit-backdrop-filter: blur(18px);

            /* Bayangan */

            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.08),
                inset 0 1px 1px rgba(255, 255, 255, 0.25);

        }


        /* ================================
           BAGIAN SEARCH + SELECT
        ================================= */

        .header-search {

            display: flex;

            align-items: center;

            gap: 10px;

            flex-wrap: wrap;

        }


        /* ================================
           SEARCH BOX
        ================================= */

        .search-box {

            display: flex;

            align-items: center;

            margin: 0;

        }


        .search-box input {

            width: 300px;

            padding: 12px 15px;

            border-radius: 12px;

            border: 1px solid rgba(255, 255, 255, 0.35);

            background: rgba(255, 255, 255, 0.25);

            backdrop-filter: blur(10px);

            -webkit-backdrop-filter: blur(10px);

            font-size: 15px;

            color: #fff;

            outline: none;

            transition: 0.25s;

        }


        /* Saat search diklik */

        .search-box input:focus {

            background: rgba(255, 255, 255, 0.4);

            border-color: rgba(52, 152, 219, 0.6);

            box-shadow:
                0 0 0 3px rgba(52, 152, 219, 0.12);

        }


        /* Warna tulisan placeholder */

        .search-box input::placeholder {

            color: rgba(255, 251, 251, 0.9);

        }


        /* ================================
           DROPDOWN BUKU
        ================================= */

        #nomorbuku {

            width: 300px;

            padding: 12px 15px;

            margin: 0;

            border-radius: 12px;

            border: 1px solid rgba(255, 255, 255, 0.35);

            background: rgba(255, 255, 255, 0.25);

            backdrop-filter: blur(10px);

            -webkit-backdrop-filter: blur(10px);

            font-size: 15px;

            color: #f0e6e6;

            outline: none;

            cursor: pointer;

            transition: 0.25s;

        }


        /* Saat dropdown dipilih */

        #nomorbuku:focus {

            background: rgba(255, 255, 255, 0.4);

            border-color: rgba(52, 152, 219, 0.6);

        }


        /* ================================
           RESPONSIVE
        ================================= */

        @media (max-width: 900px) {

            .header {

                flex-direction: column;

                align-items: flex-start;

            }


            .header-search {

                width: 100%;

            }


            .search-box {

                width: 100%;

            }


            .search-box input {

                width: 100%;

            }


            #nomorbuku {

                width: 100%;

            }

        }

    </style>

</head>


<body>


    <!-- =================================================
         TOMBOL MENU
    ================================================== -->

    <button class="menu-btn" id="menuBtn">
        ☰
    </button>


    <!-- =================================================
         SIDEBAR
    ================================================== -->

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

            <a href="../dashboard/pengembalian_buku.php">
                ↩️ Pengembalian Buku
            </a>


            <a href="../auth/logout.php">
                🚪 Logout
            </a>

        </div>

    </div>


    <!-- Overlay untuk mobile -->

    <div class="overlay" id="overlay"></div>


    <!-- =================================================
         CONTAINER
    ================================================== -->

    <div class="container">


        <!-- =================================================
             HEADER
        ================================================== -->

        <div class="header">


            <!-- Informasi user -->

            <div>

                <h1>
                    Liblary
                </h1>


                <p>

                    Selamat datang,

                    <strong>

                        <?= htmlspecialchars($_SESSION['nama_karyawan']); ?>

                    </strong>

                </p>

            </div>


            <!-- Search dan dropdown -->

<div class="header-search">

    <!-- Search -->
    <div class="search-box">
        <input
            type="text"
            id="searchBuku"
            placeholder="🔍 Cari judul atau kode buku..."
        >
    </div>

    <!-- Pilihan buku BK -->
    <select name="nomorbuku" id="nomorbuku">
        <?php if (count($daftar_buku) === 0): ?>
            <option value="">Belum ada buku</option>
        <?php endif; ?>
        <?php foreach ($daftar_buku as $buku): ?>
            <option value="<?= (int)$buku['nomorbuku']; ?>">
                <?= htmlspecialchars($buku['kode_buku']); ?> -
                <?= htmlspecialchars($buku['judul']); ?>
            </option>
        <?php endforeach; ?>
    </select>

</div>

</div>

        <!-- =================================================
             CARD BUKU
        ================================================== -->

        <div class="card">


            <div class="card-header">

                <h2>
                    📚 Buku Tersedia Saat Ini
                </h2>

            </div>


            <!-- =================================================
                 TABEL BUKU
            ================================================== -->

            <table>

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Kode Buku
                        </th>

                        <th>
                            Judul
                        </th>

                        <th>
                            Kategori
                        </th>

                        <th>
                            Penulis
                        </th>

                        <th>
                            Penerbit
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php

                    $no = 1;
                    ?>

                    <?php if (count($daftar_buku) === 0): ?>
                        <tr>
                            <td colspan="6">Belum ada data buku.</td>
                        </tr>
                    <?php else: ?>
                    <?php foreach ($daftar_buku as $row): ?>

                        <tr>


                            <!-- Nomor urut -->

                            <td>

                                <?= $no++; ?>

                            </td>


                            <!-- Kode buku -->

                            <td>

                                <?= htmlspecialchars($row['kode_buku']); ?>

                            </td>


                            <!-- Judul -->

                            <td>

                                <?= htmlspecialchars($row['judul']); ?>

                            </td>


                            <!-- Kategori -->

                            <td>

                                <?= htmlspecialchars($row['kategori']); ?>

                            </td>


                            <!-- Penulis -->

                            <td>

                                <?= htmlspecialchars($row['penulis']); ?>

                            </td>


                            <!-- Penerbit -->

                            <td>

                                <?= htmlspecialchars($row['penerbit']); ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>
                    <?php endif; ?>


                </tbody>

            </table>

        </div>

    </div>


    <!-- =================================================
         JAVASCRIPT SIDEBAR
    ================================================== -->

    <script src="../assets/sidebar.js"></script>

    <script src="../assets/dashboard.js"></script>


    <!-- =================================================
         JAVASCRIPT SEARCH
    ================================================== -->

    <script>

        /* Mengambil elemen search */

        const searchBuku =
            document.getElementById("searchBuku");


        /* Mengambil dropdown */

        const selectBuku =
            document.getElementById("nomorbuku");


        /* Menjalankan search ketika user mengetik */

        searchBuku.addEventListener("input", function () {


            /* Mengambil teks yang diketik */

            const keyword =
                this.value.toLowerCase().trim();


            /* Mengambil semua option */

            const options =
                selectBuku.querySelectorAll("option");


            /* Penanda apakah buku ditemukan */

            let ditemukan = false;


            options.forEach(function (option) {


                /* Mengambil teks option */

                const text =
                    option.textContent.toLowerCase();


                /* Mengecek apakah teks cocok */

                if (text.includes(keyword)) {


                    /* Tampilkan option */

                    option.hidden = false;


                    /* Pilih hasil pertama */

                    if (!ditemukan) {

                        selectBuku.value =
                            option.value;

                        ditemukan = true;

                    }


                } else {


                    /* Sembunyikan option */

                    option.hidden = true;

                }

            });

            if (!ditemukan) {
                selectBuku.selectedIndex = -1;
            }

        });

    </script>


    <!-- =================================================
         HISTORY
    ================================================== -->

    <script>

        /* Menyimpan halaman di history browser */

        history.pushState(
            null,
            "",
            location.href
        );


        /* Mencegah kembali ke halaman sebelumnya */

        window.addEventListener(
            "popstate",
            function () {

                history.pushState(
                    null,
                    "",
                    location.href
                );

                location.reload();

            }
        );

    </script>


</body>

</html>
