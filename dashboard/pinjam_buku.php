<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$pesan = "";

/* =========================
   PROSES PEMINJAMAN
========================= */

if (isset($_POST['pinjam'])) {

    $id_user = (int)($_POST['id_user'] ?? 0);
    $nomorbuku = (int)($_POST['nomorbuku'] ?? 0);
    $jumlah_buku = trim($_POST['jumlah_buku'] ?? '');
    $tanggal_pinjam = $_POST['tanggal_pinjam'] ?? '';
    $tanggal_jatuh_tempo = $_POST['tanggal_jatuh_tempo'] ?? '';

    if (
        empty($id_user) ||
        empty($nomorbuku) ||
        empty($tanggal_pinjam) ||
        empty($tanggal_jatuh_tempo)
    ) {

        $pesan = "Semua data wajib diisi.";

    } elseif ((int)$jumlah_buku < 1) {

        $pesan = "Jumlah buku minimal 1.";

    } elseif ($tanggal_jatuh_tempo < $tanggal_pinjam) {

        $pesan = "Tanggal jatuh tempo tidak boleh sebelum tanggal pinjam.";

    } else {

        /* CEK USER DARI TABEL users */
        $cek_user = mysqli_prepare(
            $conn,
            "SELECT id_user 
             FROM users 
             WHERE id_user = ?"
        );

        mysqli_stmt_bind_param($cek_user, "i", $id_user);
        mysqli_stmt_execute($cek_user);
        $hasil_user = mysqli_stmt_get_result($cek_user);

        if (mysqli_num_rows($hasil_user) == 0) {

            $pesan = "User/Peminjam tidak ditemukan.";

        } else {

            /* CEK BUKU DARI TABEL buku */
            $cek_buku = mysqli_prepare(
                $conn,
                "SELECT nomorbuku 
                 FROM buku 
                 WHERE nomorbuku = ?"
            );

            mysqli_stmt_bind_param($cek_buku, "i", $nomorbuku);
            mysqli_stmt_execute($cek_buku);
            $hasil_buku = mysqli_stmt_get_result($cek_buku);

            if (mysqli_num_rows($hasil_buku) == 0) {

                $pesan = "Buku tidak ditemukan.";

            } else {

                /* SIMPAN KE TABEL pinjam_buku */
               /* Query INSERT tanpa menyertakan kolom id_peminjam */
$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO pinjam_buku 
    (
        id_user, 
        nomorbuku, 
        tanggal_pinjam, 
        tanggal_jatuh_tempo, 
        jumlah_pinjam
    ) 
    VALUES (?, ?, ?, ?, ?)"
);

/* Bind 5 parameter (iisss: int, int, string, string, string) */
mysqli_stmt_bind_param(
    $stmt,
    "iisss",
    $id_user,
    $nomorbuku,
    $tanggal_pinjam,
    $tanggal_jatuh_tempo,
    $jumlah_buku
);

/* Eksekusi query */
if (mysqli_stmt_execute($stmt)) {
    header("Location: daftar_pinjam.php?status=success");
    exit;
} else {
    echo "Gagal menyimpan data: " . mysqli_error($conn);
}


                mysqli_stmt_close($stmt);
            }

            mysqli_stmt_close($cek_buku);
        }

        mysqli_stmt_close($cek_user);
    }
}


/* =========================
   AMBIL DATA USERS
========================= */

$user = mysqli_query(
    $conn,
    "SELECT id_user, nama_karyawan 
     FROM users 
     ORDER BY nama_karyawan ASC"
);


/* =========================
   AMBIL DATA BUKU
========================= */

$buku = mysqli_query(
    $conn,
    "SELECT nomorbuku, kode_buku, judul 
     FROM buku 
     ORDER BY judul ASC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" 
      content="width=device-width, initial-scale=1.0">

<title>Pinjam Buku</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {

    min-height: 100vh;

    display: flex;
    justify-content: center;
    align-items: center;

    padding: 30px;

    background: 
    linear-gradient(
        rgba(0,0,0,.65),
        rgba(0,0,0,.65)
    ),
    url("https://images.unsplash.com/photo-1568667256549-094345857637?fm=jpg&q=60&w=3000&auto=format&fit=crop") 
    center / cover no-repeat fixed;
}


/* CONTAINER */

.container {

    width: 420px;
    max-width: 100%;

    padding: 30px;

    border-radius: 20px;

    background: rgba(255,255,255,.15);

    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);

    border: 1px solid rgba(255,255,255,.2);

    box-shadow: 
        0 10px 35px rgba(0,0,0,.4);

    animation: fade .6s ease;
}


/* JUDUL */

h1 {

    text-align: center;

    color: #ffffff;

    margin-bottom: 8px;

    font-size: 28px;
}

.description {

    text-align: center;

    color: rgba(255,255,255,.85);

    margin-bottom: 25px;

    font-size: 14px;
}


/* PESAN ERROR */

.error {

    background: rgba(255,70,70,.2);

    border: 1px solid rgba(255,100,100,.5);

    color: #fff;

    padding: 12px;

    border-radius: 10px;

    margin-bottom: 15px;

    font-size: 14px;

    text-align: center;
}


/* LABEL */

label {

    display: block;

    color: #fff;

    font-size: 14px;

    margin-top: 12px;

    margin-bottom: 6px;
}


/* INPUT & SELECT */

input,
select {

    width: 100%;

    padding: 13px;

    border: 1px solid rgba(255,255,255,.4);

    border-radius: 10px;

    outline: none;

    font-size: 15px;

    background: rgba(255,255,255,.95);

    color: #333;

    transition: .25s;
}

input:focus,
select:focus {

    border-color: #701B1D;

    box-shadow: 
        0 0 0 3px rgba(112,27,29,.2);
}


/* BUTTON */

button {

    width: 100%;

    margin-top: 25px;

    padding: 14px;

    border: none;

    border-radius: 12px;

    background: rgba(1,255,1,.40);

    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);

    border: 1px solid rgba(255,255,255,.25);

    color: #fff;

    font-size: 16px;

    font-weight: bold;

    cursor: pointer;

    transition: .3s;
}

button:hover {

    background: #018900;

    transform: translateY(-2px);

    box-shadow: 
        0 5px 15px rgba(0,0,0,.25);
}


/* TOMBOL BATAL */

.batal {

    display: block;

    width: 100%;

    margin-top: 12px;

    padding: 13px;

    text-align: center;

    text-decoration: none;

    color: #fff;

    border-radius: 12px;

    background: rgba(255,70,70,.18);

    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);

    border: 1px solid rgba(255,255,255,.25);

    transition: .3s;
}

.batal:hover {

    background: #d50000;

    transform: translateY(-2px);
}


/* ANIMATION */

@keyframes fade {

    from {

        opacity: 0;

        transform: translateY(20px);
    }

    to {

        opacity: 1;

        transform: translateY(0);
    }
}


/* RESPONSIVE */

@media (max-width: 500px) {

    body {

        padding: 15px;
    }

    .container {

        padding: 22px;
    }

    h1 {

        font-size: 24px;
    }
}

</style>

</head>


<body>


<div class="container">

    <h1>📚 Pinjam Buku</h1>

    <p class="description">
        Silakan isi data peminjaman buku
    </p>


    <?php if (!empty($pesan)): ?>

        <div class="error">
            <?= htmlspecialchars($pesan); ?>
        </div>

    <?php endif; ?>


    <form method="POST" autocomplete="off">


        <!-- PEMINJAM (AMBIL DARI TABEL users) -->

        <label for="id_user">
            Peminjam
        </label>

        <select 
            name="id_user" 
            id="id_user" 
            required>

            <option value="">
                -- Pilih Peminjam --
            </option>

            <?php while ($row = mysqli_fetch_assoc($user)): ?>

                <option 
                    value="<?= htmlspecialchars($row['id_user']); ?>"
                    <?= (
                        isset($_POST['id_user']) && 
                        $_POST['id_user'] == $row['id_user']
                    ) ? 'selected' : ''; ?>
                >

                    <?= htmlspecialchars($row['nama_karyawan']); ?>

                </option>

            <?php endwhile; ?>

        </select>


        <!-- BUKU (AMBIL DARI TABEL buku) -->

        <label for="nomorbuku">
            Buku
        </label>

        <select 
            name="nomorbuku" 
            id="nomorbuku" 
            required>

            <option value="">
                -- Pilih Buku --
            </option>

            <?php while ($row = mysqli_fetch_assoc($buku)): ?>

                <option 
                    value="<?= htmlspecialchars($row['nomorbuku']); ?>"
                    <?= (
                        isset($_POST['nomorbuku']) && 
                        $_POST['nomorbuku'] == $row['nomorbuku']
                    ) ? 'selected' : ''; ?>
                >

                    <?= htmlspecialchars($row['kode_buku']); ?> 
                    - 
                    <?= htmlspecialchars($row['judul']); ?>

                </option>

            <?php endwhile; ?>

        </select>


        <!-- JUMLAH -->

        <label for="jumlah_buku">
            Jumlah Buku
        </label>

        <input 
            type="number" 
            name="jumlah_buku" 
            id="jumlah_buku" 
            min="1"
            value="<?= htmlspecialchars($_POST['jumlah_buku'] ?? '1'); ?>"
            placeholder="Masukkan jumlah buku" 
            required>


        <!-- TANGGAL PINJAM -->

        <label for="tanggal_pinjam">
            Tanggal Pinjam
        </label>

        <input 
            type="date" 
            name="tanggal_pinjam" 
            id="tanggal_pinjam" 
            value="<?= htmlspecialchars(
                $_POST['tanggal_pinjam'] ?? date('Y-m-d')
            ); ?>"
            required>


        <!-- JATUH TEMPO -->

        <label for="tanggal_jatuh_tempo">
            Tanggal Jatuh Tempo
        </label>

        <input 
            type="date" 
            name="tanggal_jatuh_tempo" 
            id="tanggal_jatuh_tempo" 
            value="<?= htmlspecialchars(
                $_POST['tanggal_jatuh_tempo'] ?? ''
            ); ?>"
            required>


        <!-- BUTTON -->

        <button 
            type="submit" 
            name="pinjam">

            📚 Pinjam Buku

        </button>


        <a 
            href="../dashboard/sub-index.php" 
            class="batal">

            ← Batal

        </a>

    </form>

</div>


<script>

/* VALIDASI TANGGAL */

const tanggalPinjam = 
    document.getElementById("tanggal_pinjam");

const tanggalJatuhTempo = 
    document.getElementById("tanggal_jatuh_tempo");


function updateTanggalMinimum() {

    tanggalJatuhTempo.min = 
        tanggalPinjam.value;

}


tanggalPinjam.addEventListener(
    "change", 
    updateTanggalMinimum
);


updateTanggalMinimum();


/* VALIDASI JUMLAH */

const jumlahBuku = 
    document.getElementById("jumlah_buku");


jumlahBuku.addEventListener(
    "input", 
    function () {

        if (this.value < 1) {

            this.value = 1;

        }

    }
);

</script>


</body>
</html>