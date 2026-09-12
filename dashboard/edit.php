<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/koneksi.php';

// =========================
// CEK KONEKSI DATABASE
// =========================
if (!$conn) {
    die("Koneksi database gagal : " . mysqli_connect_error());
}

// =========================
// CEK ID
// =========================
if (!isset($_GET['id'])) {
    die("ID Buku tidak ditemukan!");
}

$id = (int)$_GET['id'];

// =========================
// AMBIL DATA BUKU
// =========================
$query = mysqli_query($conn, "SELECT * FROM buku WHERE nomorbuku='$id'");

if (!$query) {
    die("Query Error : " . mysqli_error($conn));
}

if (mysqli_num_rows($query) == 0) {
    die("Data buku tidak ditemukan.");
}

$data = mysqli_fetch_assoc($query);

// =========================
// UPDATE DATA
// =========================
if (isset($_POST['update'])) {

    $kode_buku     	= mysqli_real_escape_string($conn, $_POST['kode_buku']);
    $judul     		= mysqli_real_escape_string($conn, $_POST['judul']);
    $kategori		= mysqli_real_escape_string($conn, $_POST['kategori']);
    $penulis   		= mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit  		= mysqli_real_escape_string($conn, $_POST['penerbit']);

    $update = mysqli_query($conn, "
        UPDATE buku SET
            kode_buku='$kode_buku',
            judul='$judul',
            kategori='$kategori',
            penulis='$penulis',
            penerbit='$penerbit'
        WHERE nomorbuku='$id'
    ");

    if ($update) {
        echo "
        <script>
            alert('Data berhasil diperbarui');
            window.location='sub-index.php';
        </script>";
        exit;
    } else {
        echo "
        <script>
            alert('Gagal mengubah data');
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Data Buku</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#483D8B,#1E1E2F);

}

.container{

    width:550px;
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(15px);
    border-radius:20px;
    padding:35px;
    color:#fff;
    box-shadow:0 20px 40px rgba(0,0,0,.4);

}

h2{

    text-align:center;
    margin-bottom:30px;

}

.form-group{

    margin-bottom:18px;

}

label{

    display:block;
    margin-bottom:8px;
    font-weight:bold;

}

input{

    width:100%;
    padding:13px;
    border:none;
    border-radius:10px;
    background:rgba(255,255,255,.15);
    color:#fff;
    outline:none;
    font-size:15px;

}

input:focus{

    background:rgba(255,255,255,.25);

}

.btn{

    display:flex;
    gap:15px;
    margin-top:30px;

}

button,
a{

    flex:1;
    padding:14px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    text-align:center;
    font-weight:bold;
    font-size:15px;
    transition:.3s;

}

button{

    background:#00C853;
    color:#fff;
    cursor:pointer;

}

button:hover{

    background:#00B248;
    transform:translateY(-2px);

}

a{

    background:#F44336;
    color:white;

}

a:hover{

    background:#D32F2F;

}

</style>

</head>
<body>

<div class="container">

<h2>📚 Edit Data Buku</h2>

<form method="POST">

<div class="form-group">
<label>Kode Buku</label>
<input
type="text"
name="kode_buku"
value="<?= htmlspecialchars($data['kode_buku']) ?>"
required>
</div>

<div class="form-group">
<label>Judul Buku</label>
<input
type="text"
name="judul"
value="<?= htmlspecialchars($data['judul']) ?>"
required>
</div>

<div class="form-group">
<label>Kategori Buku</label>
<input
type="text"
name="kategori"
value="<?= htmlspecialchars($data['kategori']) ?>"
required>
</div>

<div class="form-group">
<label>Nama Penulis</label>
<input
type="text"
name="penulis"
value="<?= htmlspecialchars($data['penulis']) ?>"
required>
</div>

<div class="form-group">
<label>Penerbit</label>
<input
type="text"
name="penerbit"
value="<?= htmlspecialchars($data['penerbit']) ?>"
required>
</div>

<div class="btn">

<button type="submit" name="update">
💾 Simpan Perubahan
</button>

<a href="sub-index.php">
⬅ Kembali
</a>

</div>

</form>

</div>

</body>
</html>