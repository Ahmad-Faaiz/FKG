<?php

session_start();
include "config/database.php";

$error = "";
$nama  = "";
$email = "";

if (isset($_POST['register'])) {

    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $email === '' || $password === '') {

        $error = "Semua kolom wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Format email tidak valid.";

    } elseif (strlen($password) < 8) {

        $error = "Password minimal 8 karakter.";
    
    } elseif (!str_ends_with($email, '@fkg.unpad.ac.id')) {
      
        $error = "Gunakan email resmi FKG UNPAD.";
    
    } else {

        // Cek email sudah dipakai
        $cek = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($cek, "s", $email);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {

            $error = "Email sudah terdaftar.";

        } else {

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'dosen'; // selalu dosen, tidak diambil dari form

            $simpan = mysqli_prepare(
                $conn,
                "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param($simpan, "ssss", $nama, $email, $hash, $role);

            if (mysqli_stmt_execute($simpan)) {

                header("Location: login.php?registered=1");
                exit;

            } else {

                // 1062 = email duplikat (kalau dua pendaftaran terjadi bersamaan)
                $error = (mysqli_errno($conn) == 1062)
                    ? "Email sudah terdaftar."
                    : "Pendaftaran gagal. Silakan coba lagi.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Daftar Akun - FKG UNPAD</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial;
    background: #f4f6f9;

    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    width: 420px;
    max-width: 94%;
    background: white;
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,.08);
}

h1 {
    margin: 0 0 5px;
    color: #1e3a5f;
}

.subtitle {
    color: #777;
    margin: 0 0 20px;
}

label {
    display: block;
    font-weight: bold;
    margin-top: 15px;
    margin-bottom: 6px;
}

input {
    width: 100%;
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.hint {
    font-size: 12px;
    color: #888;
    margin-top: 5px;
}

button {
    width: 100%;
    margin-top: 22px;
    padding: 13px;
    border: none;
    border-radius: 6px;
    background: #1e3a5f;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background: #2d527d;
}

.error {
    background: #fadbd8;
    color: #922b21;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 10px;
}

.login-link {
    text-align: center;
    margin-top: 18px;
    font-size: 14px;
}

.login-link a {
    color: #1e3a5f;
}

</style>

</head>

<body>

<div class="card">

    <h1>📝 Daftar Akun</h1>

    <p class="subtitle">Pendaftaran dosen FKG UNPAD</p>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">

        <label>Nama Pengguna</label>
        <input
            type="text"
            name="nama"
            value="<?= htmlspecialchars($nama); ?>"
            autocomplete="new-password"
            placeholder="Nama lengkap"
            required
        >

        <label>Email</label>
        <input
            type="email"
            name="email"
            value="<?= htmlspecialchars($email); ?>"
            placeholder="email@fkg.unpad.ac.id"
            required
        >

        <label>Password</label>
        <input
            type="password"
            name="password"
            minlength="8"
            autocomplete="new-password"
            placeholder="Minimal 8 karakter"
            required
        >

        <button type="submit" name="register">Daftar</button>

    </form>

    <div class="login-link">
        Sudah punya akun? <a href="login.php">Masuk</a>
    </div>

</div>

</body>

</html>