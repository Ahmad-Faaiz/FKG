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

<link rel="stylesheet" href="assets/css/admin.css">

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
