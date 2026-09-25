<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/koneksi.php";

session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Cek parameter
if (!isset($_GET['id'])) {
    die("Parameter ID tidak ditemukan.");
}

$id = (int) $_GET['id'];

// Cek data
$cek = mysqli_query($conn, "SELECT * FROM buku WHERE nomorbuku='$id'");

if (!$cek) {
    die("Query Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($cek) == 0) {
    die("Data buku tidak ditemukan.");
}

// Hapus data
$hapus = mysqli_query($conn, "DELETE FROM buku WHERE nomorbuku='$id'");

if ($hapus) {
    // Ambil halaman asal (referer), atau fallback ke halaman utama jika kosong
    $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'sub-index.php';
    
    echo "
    <script>
        alert('Data buku berhasil dihapus!');
        window.location.href = '$back';
    </script>";
    exit;
} else {
    echo "
    <script>
        alert('Data buku gagal dihapus!');
        window.history.back();
    </script>";
    exit;
}
