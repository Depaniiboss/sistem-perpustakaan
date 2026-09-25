<?php
session_start();
include '../config/koneksi.php';

/* ==================================================
   CEK SESSION
   ================================================== */

// Jika sudah login
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {

    // Jika admin, langsung ke dashboard admin
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: admin-dashboard.php");
        exit;
    }

    // Jika bukan admin, hapus session
    session_unset();
    session_destroy();
}


/* ==================================================
   PROSES LOGIN ADMIN
   ================================================== */

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $cek = mysqli_query($conn, "
        SELECT *
        FROM users
        WHERE username='$username'
        AND password='$password'
        LIMIT 1
    ");

    if (mysqli_num_rows($cek) > 0) {

        $user = mysqli_fetch_assoc($cek);

        /* ==========================================
           CEK ROLE
           ========================================== */

        if ($user['role'] !== 'admin') {

            $error = "❌ Akses ditolak! Halaman ini khusus untuk Admin.";

        } else {

            /* ==========================================
               LOGIN ADMIN BERHASIL
               ========================================== */

            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_karyawan'] = $user['nama_karyawan'];
            $_SESSION['role'] = $user['role'];

            header("Location: ../admin/admin-dashboard.php");
            exit;
        }

    } else {

        $error = "❌ Username atau Password Salah!";

    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login Admin - Inventory Buku</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{

    height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;

    background:
    linear-gradient(
        rgba(0,0,0,.65),
        rgba(0,0,0,.65)
    ),

    url("https://images.unsplash.com/photo-1568667256549-094345857637?fm=jpg&q=60&w=3000&auto=format&fit=crop")

    center/cover no-repeat fixed;
}


/* ==========================================
   LOGIN BOX
   ========================================== */

.login-box{

    width:400px;

    background:rgba(255,255,255,.15);

    backdrop-filter:blur(12px);

    -webkit-backdrop-filter:blur(12px);

    padding:30px;

    border-radius:20px;

    border:1px solid rgba(255,255,255,.2);

    box-shadow:
        0 8px 30px rgba(0,0,0,.3);

    animation:fade .7s ease;

}


/* ==========================================
   JUDUL
   ========================================== */

h2{

    text-align:center;

    color:white;

    margin-bottom:10px;

}

.subtitle{

    text-align:center;

    color:rgba(255,255,255,.8);

    font-size:14px;

    margin-bottom:20px;

}


/* ==========================================
   INPUT
   ========================================== */

input{

    width:100%;

    padding:12px;

    margin:10px 0;

    border:none;

    border-radius:10px;

    outline:none;

    background:rgba(255,255,255,.9);

}


/* ==========================================
   BUTTON LOGIN
   ========================================== */

button{

    display:block;

    width:100%;

    margin-top:15px;

    padding:14px;

    text-align:center;

    color:#fff;

    border-radius:12px;

    background:rgba(1,255,1,.40);

    backdrop-filter:blur(15px);

    -webkit-backdrop-filter:blur(15px);

    border:1px solid rgba(255,255,255,.25);

    transition:.35s;

    cursor:pointer;

    font-size:15px;

}

button:hover{

    background:#018900;

    transform:translateY(-2px);

}


/* ==========================================
   BUTTON KELUAR
   ========================================== */

.btn-keluar{

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

.btn-keluar:hover{

    background:#d50000;

}


/* ==========================================
   ERROR
   ========================================== */

.error{

    background:rgba(255,0,0,.85);

    color:white;

    padding:10px;

    border-radius:10px;

    margin-bottom:15px;

    text-align:center;

    font-size:14px;

}


/* ==========================================
   ANIMATION
   ========================================== */

@keyframes fade{

    from{

        opacity:0;

        transform:translateY(-20px);

    }

    to{

        opacity:1;

        transform:translateY(0);

    }

}


/* ==========================================
   RESPONSIVE
   ========================================== */

@media(max-width:500px){

    .login-box{

        width:90%;

        padding:25px;

    }

}

</style>

</head>


<body>


<div class="login-box">

    <h2>📚 Login Admin</h2>

    <div class="subtitle">
        Inventory Buku
    </div>


    <?php if(isset($error)){ ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php } ?>


    <form method="POST">

        <input
            type="text"
            name="username"
            placeholder="Username Admin"
            autocomplete="username"
            required
        >


        <input
            type="password"
            name="password"
            placeholder="Password"
            autocomplete="current-password"
            required
        >


        <button
            type="submit"
            name="login"
        >
            🔐 Login sebagai Admin
        </button>


        <a
            href="../index.php"
            class="btn-keluar"
        >
            ← Keluar
        </a>

    </form>

</div>


</body>

</html>
