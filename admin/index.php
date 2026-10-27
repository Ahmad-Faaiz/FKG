<?php

ob_start(); // supaya header("Location: ...") di halaman isi tetap berfungsi

session_start();
include "../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Daftar halaman yang diizinkan (whitelist)
$halaman = [
    'dashboard'   => 'pages/dashboard.php',
    'room'        => 'pages/room.php',
    'room_tambah' => 'pages/room_tambah.php',
    'room_edit'   => 'pages/room_edit.php',
    'room_detail' => 'pages/room_detail.php',
    'user'        => 'pages/user.php',
    'user_tambah' => 'pages/user_tambah.php',
    'booking'     => 'pages/booking.php',
];

// Halaman turunan -> tab navbar yang harus menyala
$menu_aktif = [
    'room_tambah' => 'room',
    'room_edit'   => 'room',
    'room_detail' => 'room',
    'user_tambah' => 'user',
];

$page = $_GET['page'] ?? 'dashboard';

if (!isset($halaman[$page])) {
    $page = 'dashboard';
}

$aktif = $menu_aktif[$page] ?? $page;

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Admin - FKG UNPAD</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial;
    background: #f4f6f9;
}

/* ===== NAVBAR ===== */

.navbar {
    background: #1e3a5f;
    color: white;
    padding: 0 40px;

    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.logo {
    font-weight: bold;
    font-size: 20px;
    padding: 18px 0;
}

.nav-menu {
    display: flex;
    gap: 5px;
}

.nav-menu a {
    color: #cfd8e3;
    text-decoration: none;
    padding: 20px 18px;
    border-bottom: 3px solid transparent;
}

.nav-menu a:hover {
    color: white;
    background: rgba(255,255,255,.08);
}

.nav-menu a.active {
    color: white;
    border-bottom-color: #f1c40f;
    font-weight: bold;
}

.user {
    display: flex;
    gap: 15px;
    align-items: center;
}

.logout {
    color: white;
    text-decoration: none;
}

/* ===== ISI ===== */

.container {
    width: 1100px;
    max-width: 94%;
    margin: 35px auto;
}

h1 {
    color: #1e3a5f;
}

.subtitle {
    color: #777;
}

.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-top: 30px;
}

.stat {
    background: white;
    padding: 22px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,.07);
}

.number {
    font-size: 30px;
    font-weight: bold;
    color: #1e3a5f;
}

.label {
    color: #777;
}

.section {
    background: white;
    margin-top: 30px;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,.07);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 13px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

th {
    color: #1e3a5f;
}

.action {
    text-decoration: none;
    margin-right: 10px;
}

.approve { color: #1e8449; }
.reject  { color: #c0392b; }

.btn {
    display: inline-block;
    padding: 10px 15px;
    background: #1e3a5f;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    margin: 15px 5px 20px 0;
}

.btn:hover {
    background: #2d527d;
}

.tabel {
    width: 100%;
    background: white;
    border-collapse: collapse;
    box-shadow: 0 3px 10px rgba(0,0,0,.08);
}

.tabel th,
.tabel td {
    padding: 13px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

.tabel th {
    background: #1e3a5f;
    color: white;
}

.aksi a {
    text-decoration: none;
    margin-right: 8px;
}

.edit    { color: #2980b9; }
.detail  { color: #27ae60; }
.hapus   { color: #c0392b; text-decoration: none; }
.setuju  { color: #27ae60; }
.tolak   { color: #c0392b; }
.batal   { color: #7f8c8d; }

.status,
.role {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 13px;
}

.aktif,
.disetujui,
.role-dosen {
    background: #d5f5e3;
    color: #1e8449;
}

.nonaktif,
.ditolak {
    background: #fadbd8;
    color: #922b21;
}

.menunggu {
    background: #fff3cd;
    color: #856404;
}

.dibatalkan {
    background: #eee;
}

.role-admin {
    background: #d6eaf8;
    color: #21618c;
}

.form-card {
    width: 600px;
    max-width: 100%;
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 3px 12px rgba(0,0,0,.07);
}

.form-card.sempit {
    width: 500px;
}

.form-card h1 {
    margin-top: 0;
}

.form-card label {
    display: block;
    margin-top: 15px;
    margin-bottom: 5px;
}

.form-card input,
.form-card textarea,
.form-card select {
    width: 100%;
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
}

.form-card textarea {
    height: 100px;
    resize: vertical;
}

.form-card button {
    margin-top: 20px;
    padding: 12px 20px;
    background: #1e3a5f;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.form-card button:hover {
    background: #2d527d;
}

.batal-link {
    margin-left: 10px;
    color: #1e3a5f;
    text-decoration: none;
}

.pesan-error {
    color: #c0392b;
    margin: 15px 0;
}

.detail-item {
    margin: 15px 0;
}

.detail-label {
    font-weight: bold;
    color: #555;
}
@media(max-width: 700px) {

    .stats {
        grid-template-columns: 1fr;
    }

    .navbar {
        padding: 0 15px;
    }

    .nav-menu a {
        padding: 14px 10px;
        font-size: 14px;
    }

}

</style>

</head>

<body>


<div class="navbar">

    <div class="logo">FKG UNPAD</div>

    <div class="nav-menu">

        <a href="index.php?page=dashboard"
           class="<?= $page == 'dashboard' ? 'active' : ''; ?>">
            🏠 Dashboard
        </a>

        <a href="index.php?page=room"
           class="<?= $aktif == 'room' ? 'active' : ''; ?>">
            🏢 Kelola Room
        </a>

        <a href="index.php?page=user"
           class="<?= $aktif == 'user' ? 'active' : ''; ?>">
            👥 Kelola User
        </a>

        <a href="index.php?page=booking"
           class="<?= $aktif == 'booking' ? 'active' : ''; ?>">
            📋 Kelola Booking
        </a>

    </div>

    <div class="user">
        👤 Admin
        <a href="../logout.php" class="logout">Logout</a>
    </div>

</div>


<div class="container">

    <?php include $halaman[$page]; ?>

</div>

</body>

</html>