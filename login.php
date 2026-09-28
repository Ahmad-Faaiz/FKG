<?php
session_start();

include "config/database.php";

$error = "";

if (isset($_POST['login'])) {

    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    $query = mysqli_query($conn, "
        SELECT *
        FROM users
        WHERE email = '$email'
        LIMIT 1
    ");

    if (mysqli_num_rows($query) == 1) {

        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {

            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
                exit;
            }

            if ($user['role'] == 'dosen') {
                header("Location: User/index.php");
                exit;
            }

            session_unset();
            session_destroy();
            $error = "Role pengguna tidak valid.";

        } else {
            $error = "Email atau password salah.";
        }

    } else {
        $error = "Email atau password salah.";
    }
}
?>
<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Login - FKG UNPAD</title>

<style>

* {
    box-sizing: border-box;
}

body {

    margin: 0;

    font-family: Arial;

    background: #f4f6f9;

    display: flex;

    justify-content: center;

    align-items: center;

    min-height: 100vh;

}

.login-box {

    width: 400px;

    max-width: 90%;

    background: white;

    padding: 35px;

    border-radius: 12px;

    box-shadow:
        0 5px 20px rgba(0,0,0,.1);

}

.logo {

    text-align: center;

    font-size: 30px;

    margin-bottom: 10px;

}

h1 {

    text-align: center;

    color: #1e3a5f;

    margin-bottom: 5px;

}

.subtitle {

    text-align: center;

    color: #777;

    margin-bottom: 30px;

}

label {

    display: block;

    font-weight: bold;

    margin-bottom: 6px;

}

input {

    width: 100%;

    padding: 12px;

    border: 1px solid #ccc;

    border-radius: 6px;

    margin-bottom: 18px;

}

button {

    width: 100%;

    padding: 13px;

    background: #1e3a5f;

    color: white;

    border: none;

    border-radius: 6px;

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

    margin-bottom: 20px;

    text-align: center;

}

</style>

</head>

<body>


<div class="login-box">


<div class="logo">
    🦷
</div>


<h1>
    FKG UNPAD
</h1>


<div class="subtitle">

Sistem Manajemen Room

</div>


<?php if ($error != ""): ?>

<div class="error">

<?= htmlspecialchars($error); ?>

</div>

<?php endif; ?>


<form method="POST">


<label>
    Email
</label>

<input
    type="email"
    name="email"
    autocomplete="off"
    placeholder="Masukkan email"
    required
>


<label>
    Password
</label>

<input
    type="password"
    name="password"
    autocomplete="new-password"
    placeholder="Masukkan password"
    required
>


<button
    type="submit"
    name="login">

    Login

</button>


</form>


</div>

</body>

</html>