<?php
include '../config/koneksi.php';

$pesan = "";

if (isset($_POST['register'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $nama_karyawan = trim($_POST['nama_karyawan'] ?? '');

    $cek = mysqli_prepare($conn, "SELECT id_user FROM users WHERE username = ?");
    mysqli_stmt_bind_param($cek, "s", $username);
    mysqli_stmt_execute($cek);
    $hasil_cek = mysqli_stmt_get_result($cek);

    if ($username === '' || $password === '' || $nama_karyawan === '') {
        $pesan = "<p style='color:yellow;'>Semua kolom wajib diisi!</p>";
    } elseif (mysqli_num_rows($hasil_cek) > 0) {
        $pesan = "<p style='color:yellow;'>Username sudah digunakan!</p>";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $simpan = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_karyawan) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($simpan, "sss", $username, $password_hash, $nama_karyawan);
        $berhasil = mysqli_stmt_execute($simpan);

        if ($berhasil) {
            $pesan = "<p style='color:lightgreen;'>Register berhasil!</p>";
        } else {
            $pesan = "<p style='color:red;'>Register gagal!</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Register User</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
	background:
    linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)),
    url("https://images.unsplash.com/photo-1568667256549-094345857637?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fGJ1a3UlMjBwZXJwdXN0YWthYW58ZW58MHx8MHx8fDA%3D")
    center/cover no-repeat fixed;
}

.container{
     width:400px;
    background:rgba(255,255,255,.15);
    backdrop-filter:blur(12px);
    padding:30px;
    border-radius:20px;
    box-shadow:0 8px 30px rgba(0,0,0,.3);
    animation:fade .7s ease;
}

h1{
    text-align:center;
	color:#66160A;
    margin-bottom:10px;
}

p{
    text-align:center;
    color:#FFFFFF;
    margin-bottom:20px;
}

input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:1px solid #ccc;
    border-radius:10px;
    outline:none;
    font-size:15px;
}

input:focus{
    border-color:#701B1D;
}

button{
   display:block;
    width:100%;
    margin-top:15px;
    padding:14px;

    text-align:center;
    text-decoration:none;
    color:#fff;

    border-radius:12px;

    background:rgba(1,255,1,.40);
    backdrop-filter:blur(15px);
    -webkit-backdrop-filter:blur(15px);

    border:1px solid rgba(255,255,255,.25);

    transition:.35s;
}

button:hover{
    background:#018900;
}


	.batal{
	display:block;
    width:100%;
    margin-top:15px;
    padding:12px;

    text-align:center;
    text-decoration:none;
    color:#fff;

    border-radius:12px;

    background:rgba(255,70,70,.18);
    backdrop-filter:blur(15px);
    -webkit-backdrop-filter:blur(15px);

    border:1px solid rgba(255,255,255,.25);

    transition:.35s;
	}

.batal:hover{
    background:#d50000;
}

.pesan{
    margin-bottom:15px;
    text-align:center;
    font-weight:bold;
}

</style>

</head>
<body>

<div class="container">

    <h1>Register User</h1>
    <p>Silakan isi data di bawah ini</p>

    <div class="pesan">
        <?= $pesan; ?>
    </div>

    <form method="POST">

        <input
            type="text"
            name="username"
            placeholder="Username"
            required>

        <input
            type="password"
            name="password"
            placeholder="Password"
            required>

        <input
            type="text"
            name="nama_karyawan"
            placeholder="Nama Karyawan"
            required>

        <button type="submit" name="register">
            Register
        </button>

        <a href="../index.php" class="batal">
            Batal
        </a>

    </form>

</div>

</body>
</html>
