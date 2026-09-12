<?php
session_start();
include '../config/koneksi.php';

// Redirect otomatis jika pengguna sudah login
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/admin-dashboard.php");
    } elseif ($_SESSION['role'] === 'karyawan') {
        header("Location: ../dashboard/sub-index.php");
    } else {
        header("Location: ../dashboard/sub-index.php");
    }
    exit;
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Ambil data user menggunakan Prepared Statement (Aman dari SQL Injection)
    $stmt = mysqli_prepare($conn, "SELECT id_user, username, password, nama_karyawan, role FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // Verifikasi password (Mendukung password_hash & fallback MD5)
        if (password_verify($password, $user['password']) || md5($password) === $user['password']) {
            
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_karyawan'] = $user['nama_karyawan'];
            $_SESSION['role'] = $user['role'];

            // Redirect sesuai role
            if ($user['role'] === 'admin') {
                header("Location: ../admin/admin-dashboard.php");
            } else {
                header("Location: ../dashboard/sub-index.php");
            }
            exit;
        } else {
            $error = "Username atau Password Salah!";
        }
    } else {
        $error = "Username atau Password Salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Inventory Buku</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: 
                linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)),
                url("https://images.unsplash.com/photo-1568667256549-094345857637?fm=jpg&q=60&w=3000&auto=format&fit=crop")
                center/cover no-repeat fixed;
            padding: 20px;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 35px 30px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.6s ease-out;
        }

        h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 25px;
            font-size: 1.6rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid transparent;
            border-radius: 10px;
            outline: none;
            font-size: 0.95rem;
            color: #333;
            transition: all 0.3s ease;
        }

        input:focus {
            background: #ffffff;
            border-color: #4CAF50;
            box-shadow: 0 0 8px rgba(76, 175, 80, 0.4);
        }

        .btn-submit {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 14px;
            text-align: center;
            font-size: 1rem;
            font-weight: 600;
            color: #ffffff;
            border-radius: 10px;
            background: rgba(46, 125, 50, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            transition: background 0.3s ease, transform 0.1s ease;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #2e7d32;
        }

        .btn-submit:active, .btn-keluar:active {
            transform: scale(0.98);
        }

        .btn-keluar {
            display: block;
            width: 100%;
            margin-top: 12px;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            color: #ffffff;
            border-radius: 10px;
            background: rgba(211, 47, 47, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            transition: background 0.3s ease, transform 0.1s ease;
        }

        .btn-keluar:hover {
            background: #c62828;
        }

        .error {
            background: rgba(255, 235, 238, 0.95);
            color: #c62828;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 500;
            border-left: 4px solid #d32f2f;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>📚 Login Inventory Buku</h2>

    <?php if (isset($error)) : ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="input-group">
            <input 
                type="text" 
                name="username" 
                placeholder="Username" 
                autocomplete="off"
                required>
        </div>

        <div class="input-group">
            <input 
                type="password" 
                name="password" 
                placeholder="Password" 
                required>
        </div>

        <button type="submit" name="login" class="btn-submit">
            Login
        </button>

        <a href="../index.php" class="btn-keluar">Keluar</a>
    </form>
</div>

</body>
</html>