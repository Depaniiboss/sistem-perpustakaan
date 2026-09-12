<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * 1. Fungsi untuk Memproteksi Halaman berdasarkan Role
 */
function check_access($allowed_roles = []) {
    // Cek apakah pengguna sudah login
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        header("Location: ../auth/login.php");
        exit;
    }

    // Cek apakah role pengguna diizinkan
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        echo "<script>
                alert('Anda tidak memiliki akses ke halaman ini!');
                window.location.href = '../auth/login.php';
              </script>";
        exit;
    }
}

/**
 * 2. Fungsi untuk Mengambil / Menampilkan Data User yang Sedang Login
 * (Pengganti isi file cek.php)
 */
function get_user_login() {
    if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
        return [
            'status'        => true,
            'id_user'       => $_SESSION['id_user'] ?? null,
            'username'      => $_SESSION['username'] ?? '',
            'nama_karyawan' => $_SESSION['nama_karyawan'] ?? '',
            'role'          => $_SESSION['role'] ?? ''
        ];
    }
    return ['status' => false];
}

/**
 * 3. Fungsi Opsional: Cetak Info User Langsung ke Layar (Untuk Debugging)
 */
function debug_user_session() {
    $user = get_user_login();
    if ($user['status']) {
        echo "<h3>Status: Sudah Login</h3>";
        echo "ID User: " . $user['id_user'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Nama: " . $user['nama_karyawan'] . "<br>";
        echo "Role: <strong>" . $user['role'] . "</strong><br>";
    } else {
        echo "<h3>Status: Belum Login</h3>";
        echo "<a href='../auth/login.php'>Klik di sini untuk Login</a>";
    }
}
?>