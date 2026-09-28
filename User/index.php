<?php

session_start();
include "../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../login.php");
    exit;
}

header("Location: kalender.php");
exit;

$user_id = $_SESSION['user_id'];


// ==========================================
// JUMLAH BOOKING
// ==========================================

$q_total = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM bookings
    WHERE user_id = $user_id
");

$total_booking = mysqli_fetch_assoc($q_total)['total'];


// ==========================================
// BOOKING MENUNGGU
// ==========================================

$q_menunggu = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM bookings
    WHERE user_id = $user_id
    AND status = 'menunggu'
");

$menunggu = mysqli_fetch_assoc($q_menunggu)['total'];


// ==========================================
// BOOKING DISETUJUI
// ==========================================

$q_disetujui = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM bookings
    WHERE user_id = $user_id
    AND status = 'disetujui'
");

$disetujui = mysqli_fetch_assoc($q_disetujui)['total'];


// ==========================================
// BOOKING TERBARU
// ==========================================

$booking_terbaru = mysqli_query($conn, "
    SELECT
        bookings.*,
        rooms.nama_room,
        rooms.gedung

    FROM bookings

    JOIN rooms
        ON bookings.room_id = rooms.id

    WHERE bookings.user_id = $user_id

    ORDER BY bookings.tanggal DESC,
             bookings.jam_mulai DESC

    LIMIT 5
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard Dosen - FKG UNPAD</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    color: #333;
}


/* ================================
   NAVBAR
================================ */

.navbar {
    background: #1e3a5f;
    color: white;
    padding: 18px 40px;

    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 20px;
    font-weight: bold;
}

.user {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logout {
    color: white;
    text-decoration: none;
    padding: 8px 14px;
    border: 1px solid rgba(255,255,255,.5);
    border-radius: 5px;
}


/* ================================
   CONTAINER
================================ */

.container {
    width: 1100px;
    max-width: 94%;
    margin: 35px auto;
}

.welcome h1 {
    margin-bottom: 5px;
    color: #1e3a5f;
}

.welcome p {
    color: #777;
}


/* ================================
   MENU
================================ */

.menu {
    display: grid;
    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;

    margin-top: 30px;
}

.menu-card {
    background: white;
    padding: 25px;
    border-radius: 12px;

    text-decoration: none;
    color: #333;

    box-shadow:
        0 3px 12px rgba(0,0,0,.07);

    transition: .2s;
}

.menu-card:hover {
    transform: translateY(-3px);
}

.menu-icon {
    font-size: 35px;
    margin-bottom: 10px;
}

.menu-card h3 {
    color: #1e3a5f;
    margin: 5px 0;
}

.menu-card p {
    color: #777;
    font-size: 14px;
}


/* ================================
   STATISTIK
================================ */

.stats {
    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;

    margin-top: 30px;
}

.stat {
    background: white;
    padding: 20px;
    border-radius: 10px;

    box-shadow:
        0 3px 12px rgba(0,0,0,.06);
}

.stat-number {
    font-size: 30px;
    font-weight: bold;
    color: #1e3a5f;
}

.stat-label {
    color: #777;
}


/* ================================
   TABLE
================================ */

.section {
    background: white;
    margin-top: 30px;
    padding: 25px;
    border-radius: 10px;

    box-shadow:
        0 3px 12px rgba(0,0,0,.06);
}

.section h2 {
    color: #1e3a5f;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th,
td {
    padding: 13px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

th {
    color: #1e3a5f;
}

.status {
    padding: 6px 10px;
    border-radius: 15px;
    font-size: 12px;
}

.menunggu {
    background: #fff3cd;
    color: #856404;
}

.disetujui {
    background: #d5f5e3;
    color: #1e8449;
}

.ditolak {
    background: #fadbd8;
    color: #922b21;
}

.dibatalkan {
    background: #eee;
    color: #555;
}


@media(max-width: 700px) {

    .menu,
    .stats {
        grid-template-columns: 1fr;
    }

    .navbar {
        padding: 15px;
    }

    .container {
        max-width: 92%;
    }

    table {
        font-size: 13px;
    }

}

</style>

</head>

<body>


<!-- NAVBAR -->

<div class="navbar">

    <div class="logo">
        FKG UNPAD
    </div>

    <div class="user">

        👤
        <?= htmlspecialchars($_SESSION['nama']); ?>

        <a
            href="../logout.php"
            class="logout">

            Logout

        </a>

    </div>

</div>


<div class="container">


<!-- WELCOME -->

<div class="welcome">

    <h1>
        Dashboard Dosen
    </h1>

    <p>
        Selamat datang di Sistem Manajemen Room FKG UNPAD.
    </p>

</div>


<!-- MENU -->

<div class="menu">


<a
    href="kalender.php"
    class="menu-card">

    <div class="menu-icon">
        📅
    </div>

    <h3>
        Kalender Room
    </h3>

    <p>
        Lihat jadwal dan ketersediaan ruangan.
    </p>

</a>


<a
    href="booking.php"
    class="menu-card">

    <div class="menu-icon">
        ➕
    </div>

    <h3>
        Booking Room
    </h3>

    <p>
        Ajukan pemakaian ruangan.
    </p>

</a>


<a
    href="booking_saya.php"
    class="menu-card">

    <div class="menu-icon">
        📋
    </div>

    <h3>
        Booking Saya
    </h3>

    <p>
        Lihat status pemesanan room.
    </p>

</a>

</div>


<!-- STATISTIK -->

<div class="stats">


<div class="stat">

    <div class="stat-number">
        <?= $total_booking; ?>
    </div>

    <div class="stat-label">
        Total Booking
    </div>

</div>


<div class="stat">

    <div class="stat-number">
        <?= $menunggu; ?>
    </div>

    <div class="stat-label">
        Menunggu Persetujuan
    </div>

</div>


<div class="stat">

    <div class="stat-number">
        <?= $disetujui; ?>
    </div>

    <div class="stat-label">
        Booking Disetujui
    </div>

</div>


</div>


<!-- BOOKING TERBARU -->

<div class="section">

<h2>
    📋 Booking Terbaru
</h2>


<table>

<tr>

    <th>Acara</th>
    <th>Room</th>
    <th>Tanggal</th>
    <th>Waktu</th>
    <th>Status</th>

</tr>


<?php if (mysqli_num_rows($booking_terbaru) == 0): ?>

<tr>

    <td colspan="5">
        Belum ada booking.
    </td>

</tr>

<?php endif; ?>


<?php while ($data = mysqli_fetch_assoc($booking_terbaru)): ?>

<tr>

    <td>
        <?= htmlspecialchars($data['nama_acara']); ?>
    </td>

    <td>
        <?= htmlspecialchars($data['nama_room']); ?>
    </td>

    <td>
        <?= date(
            'd-m-Y',
            strtotime($data['tanggal'])
        ); ?>
    </td>

    <td>

        <?= substr(
            $data['jam_mulai'],
            0,
            5
        ); ?>

        -

        <?= substr(
            $data['jam_selesai'],
            0,
            5
        ); ?>

    </td>

    <td>

        <span
            class="status <?= $data['status']; ?>">

            <?= ucfirst($data['status']); ?>

        </span>

    </td>

</tr>

<?php endwhile; ?>

</table>

</div>


</div>

</body>

</html>