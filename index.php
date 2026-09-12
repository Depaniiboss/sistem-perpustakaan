<?php
session_start();
include 'config/koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventory Buku Perpustakaan</title>

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
    overflow:hidden;

    background:
    linear-gradient(rgba(0,0,0,.65),rgba(0,0,0,.65)),
    url("https://images.unsplash.com/photo-1568667256549-094345857637?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fGJ1a3UlMjBwZXJwdXN0YWthYW58ZW58MHx8MHx8fDA%3D")
    center/cover no-repeat fixed;
}

/* Background Blur */
body::before{
    content:"";
    position:absolute;
    width:350px;
    height:350px;
    background:#3b82f6;
    border-radius:50%;
    filter:blur(120px);
    top:-100px;
    left:-100px;
    opacity:.35;
}

body::after{
    content:"";
    position:absolute;
    width:300px;
    height:300px;
    background:#8b5cf6;
    border-radius:50%;
    filter:blur(120px);
    bottom:-100px;
    right:-100px;
    opacity:.35;
}

.container{
    position:relative;
    z-index:2;
    width:90%;
    max-width:850px;
    text-align:center;
    color:#fff;
}

.container h1{
    font-size:55px;
    margin-bottom:20px;
}

.container p{
    font-size:20px;
    line-height:1.7;
    margin-bottom:35px;
}

.click{
    margin:25px 0 10px;
    font-size:15px;
    opacity:.9;
}

/* ==========================
      LIQUID GLASS BUTTON
========================== */

.btn{
    position:relative;
    display:inline-block;
    padding:16px 45px;
    margin:10px;
    text-decoration:none;
    color:#fff;
    font-size:18px;
    font-weight:600;
    border-radius:50px;

    background:rgba(255,255,255,.12);

    backdrop-filter:blur(1px) saturate(180%);
    -webkit-backdrop-filter:blur(20px) saturate(180%);

    border:1px solid rgba(255,255,255,.3);

    box-shadow:
        0 8px 30px rgba(0,0,0,.3),
        inset 0 1px 2px rgba(255,255,255,.45);

    overflow:hidden;
    transition:.35s;
}

/* Highlight */
.btn::before{
    content:"";
    position:absolute;
    top:8px;
    left:15%;
    width:70%;
    height:35%;
    border-radius:50%;
    background:rgba(255,255,255,.35);
    filter:blur(8px);
}

/* Shine */
.btn::after{
    content:"";
    position:absolute;
    top:-60%;
    left:-120%;
    width:60%;
    height:220%;
    background:linear-gradient(
        120deg,
        transparent,
        rgba(255,255,255,.9),
        transparent
    );
    transform:rotate(25deg);
    transition:.8s;
}

.btn:hover{
    transform:translateY(-5px) scale(1.05);

    background:rgba(255,255,255,.18);

    box-shadow:
        0 15px 35px rgba(0,0,0,.4),
        inset 0 2px 4px rgba(255,255,255,.6);
}

.btn:hover::after{
    left:170%;
}

.btn:active{
    transform:scale(.96);
}

/* Floating */
@keyframes floating{
    0%,100%{
        transform:translateY(0);
    }
    50%{
        transform:translateY(-6px);
    }
}

.btn{
    animation:floating 15s ease-in-out infinite;
}

.footer{
    position:fixed;
    bottom:20px;
    left:0;
    width:100%;
    text-align:center;
    color:#fff;
    font-size:14px;
    z-index:2;
}

</style>
</head>

<body>

<div class="container">

    <h1>📚 Inventory Buku</h1>

    <p>
        Selamat datang di Website Inventory Buku Perpustakaan.<br>
        Kelola data buku dengan mudah, cepat, dan aman.
    </p>

    <?php if(isset($_SESSION['login'])){ ?>

        <a href="dashboard/sub-index.php" class="btn">
            📖 Dashboard
        </a>

    <?php }else{ ?>

        <a href="auth/login.php" class="btn">
            🔑 Login
        </a>

        <div class="click">
            Belum punya akun?
        </div>

        <a href="auth/register.php" class="btn">
            ✨ Register
        </a>

    <?php } ?>

</div>

<div class="footer">
    © <?= date('Y'); ?> Inventory Buku | Dibuat oleh <b>Parel</b>
</div>

</body>
</html>