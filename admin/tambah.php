<script>
history.pushState(null, "", location.href);

window.addEventListener("popstate", function () {
    history.pushState(null, "", location.href);
    location.reload();
});
</script>


<?php
session_start();
include '../config/koneksi.php';

if(!isset($_SESSION['login']) || $_SESSION['login'] !== true || ($_SESSION['role'] ?? '') !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

if(isset($_POST['simpan'])){
    $kode_buku = $_POST['kode_buku'];
    $judul = $_POST['judul'];
    $kategori = $_POST['kategori'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];

    mysqli_query($conn,"
        INSERT INTO buku
        (kode_buku,judul,kategori,penulis,penerbit)
        VALUES
        ('$kode_buku','$judul','$kategori','$penulis','$penerbit')
    ");

    // Redirect kembali ke halaman ini agar kode_buku otomatis langsung diperbarui
    header("Location: tambah.php");
    exit;
}

// 1. Ambil semua kode buku yang ada dan urutkan dari yang terkecil
$query = "SELECT kode_buku FROM buku WHERE kode_buku LIKE 'BK%' ORDER BY kode_buku ASC";
$result = mysqli_query($conn, $query);

$existing_numbers = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Ambil angkanya saja, misal 'BK005' -> 5
    $existing_numbers[] = (int) substr($row['kode_buku'], 2);
}

// 2. Cari angka terkecil yang belum digunakan (mulai dari 1)
$next_number = 1;
while (in_array($next_number, $existing_numbers)) {
    $next_number++;
}

// 3. Format kembali jadi kode buku (Disimpan ke variabel $kode_buku agar sesuai dengan HTML)
$kode_buku = 'BK' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Inventory Buku</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    min-height:100vh;
    background:
    linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)),
    url("https://images.unsplash.com/photo-1568667256549-094345857637?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fGJ1a3UlMjBwZXJwdXN0YWthYW58ZW58MHx8MHx8fDA%3D")
    center/cover no-repeat fixed;
    padding:30px;
}

.container{
    max-width:1200px;
    margin:auto;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 30px;
    border-radius:20px;
    color:white;
    animation:fadeDown .8s ease;
    margin-bottom: 40px;
    background:rgba(255,255,255,.15);
    backdrop-filter:blur(12px);
    padding:30px;
    box-shadow:0 8px 30px rgba(0,0,0,.3);
}

.header h1{
    font-size:35px;
}

.header p{
    font-size:18px;
    margin-top:5px;
}

.header a{
    text-decoration:none;
    background:#ff5252;
    color:white;
    padding:10px 20px;
    border-radius:10px;
    transition:.3s;
}

.card{
    background:rgba(255,255,255,.15);
    backdrop-filter:blur(10px);
    -webkit-backdrop-filter:blur(10px);
    border:1px solid rgba(255,255,255,.2);
    border-radius:20px;
    padding:25px;
    margin-bottom:25px;
    box-shadow:0 8px 32px rgba(0,0,0,.2);
    animation:fadeUp .8s ease;
}

label{
    color:white;
    display:block;
    margin-bottom:5px;
}

input{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    margin-bottom:15px;
    outline:none;
}

/* Style tambahan khusus input readonly agar terlihat jelas bahwa field ini dikunci */
input[readonly]{
    background: rgba(235, 235, 235, 0.7);
    color: #333;
    cursor: not-allowed;
}

button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#00c853;
    color:white;
    font-size:16px;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    transform:translateY(-3px);
    background:#00e676;
}

.search{
    margin-bottom:20px;
}

.search input{
    background:white;
}

table{
    width:100%;
    border-collapse:collapse;
    overflow:hidden;
    border-radius:15px;
}

th{
    background:#4a148c;
    color:white;
    padding:15px;
}

td{
    background:white;
    padding:12px;
}

tr:hover td{
    background:#f3e5f5;
    transition:.3s;
}

@keyframes fadeUp{
    from{
        opacity:0;
        transform:translateY(40px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

@keyframes fadeDown{
    from{
        opacity:0;
        transform:translateY(-40px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>
</head>
<body>

<div class="header">
    <h1>Tambah Buku</h1>
    <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama_karyawan']); ?></strong></p>
    <a href="../admin/admin-dashboard.php">Kembali</a>
</div>
    
<div class="container">
    <div class="card">
        <h2>Form Input Buku</h2><br>

        <form method="POST">
            <label>Kode Buku Otomatis</label>
            <!-- Menggunakan $kode_buku dan atribut readonly agar tidak bisa diedit manual -->
            <input type="text" name="kode_buku" value="<?= $kode_buku; ?>" readonly required>

            <label>Judul Buku</label>
            <input type="text" name="judul" placeholder="Judul Buku" required>

            <label>Kategori</label>
            <input type="text" name="kategori" placeholder="Kategori Buku" required>

            <label>Penulis</label>
            <input type="text" name="penulis" placeholder="Nama Penulis" required>

            <label>Penerbit</label>
            <input type="text" name="penerbit" placeholder="Nama Penerbit">

            <button type="submit" name="simpan">
                Simpan Buku
            </button>
        </form>
    </div>
</div>

</body>
</html>
