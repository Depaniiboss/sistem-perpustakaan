<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_peminjam = $_GET['id'];

    /* Prepared statement untuk menghapus data peminjaman */
    $stmt = mysqli_prepare($conn, "DELETE FROM pinjam_buku WHERE id_peminjam = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_peminjam);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: daftar_pinjam.php?status=deleted");
        exit;
    } else {
        echo "Gagal menghapus data: " . mysqli_error($conn);
    }
} else {
    header("Location: daftar_pinjam.php");
    exit;
}
?>