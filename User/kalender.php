<?php

session_start();
include "../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../login.php");
    exit;
}


// ==========================================
// BULAN & TAHUN
// ==========================================

$bulan = isset($_GET['bulan'])
    ? (int) $_GET['bulan']
    : date('n');

$tahun = isset($_GET['tahun'])
    ? (int) $_GET['tahun']
    : date('Y');


// Validasi bulan
if ($bulan < 1) {
    $bulan = 12;
    $tahun--;
}

if ($bulan > 12) {
    $bulan = 1;
    $tahun++;
}


// ==========================================
// JUMLAH HARI
// ==========================================

$jumlah_hari = cal_days_in_month(
    CAL_GREGORIAN,
    $bulan,
    $tahun
);


// Hari pertama
$hari_pertama = date(
    'N',
    strtotime("$tahun-$bulan-01")
);


// ==========================================
// BOOKING BULAN INI
// ==========================================

$awal_bulan = sprintf(
    '%04d-%02d-01',
    $tahun,
    $bulan
);

$akhir_bulan = sprintf(
    '%04d-%02d-%02d',
    $tahun,
    $bulan,
    $jumlah_hari
);


$query = mysqli_query($conn, "

    SELECT
        bookings.*,
        rooms.nama_room

    FROM bookings

    JOIN rooms
        ON bookings.room_id = rooms.id

    WHERE bookings.tanggal
    BETWEEN '$awal_bulan'
    AND '$akhir_bulan'

    AND bookings.status IN
        ('menunggu', 'disetujui')

    ORDER BY bookings.jam_mulai

");


// Masukkan berdasarkan tanggal
$booking = [];

while ($data = mysqli_fetch_assoc($query)) {

    $tanggal = (int) date(
        'j',
        strtotime($data['tanggal'])
    );

    $booking[$tanggal][] = $data;
}


$nama_bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];


// ==========================================
// NAVIGASI
// ==========================================

$prev_bulan = $bulan - 1;
$prev_tahun = $tahun;

if ($prev_bulan < 1) {
    $prev_bulan = 12;
    $prev_tahun--;
}


$next_bulan = $bulan + 1;
$next_tahun = $tahun;

if ($next_bulan > 12) {
    $next_bulan = 1;
    $next_tahun++;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0">

<title>Kalender Room - FKG UNPAD</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;

    font-family: Arial;

    background: #f4f6f9;
}

.container {

    width: 1100px;

    max-width: 95%;

    margin: 30px auto;

}


/* ==========================
   HEADER
========================== */

.header {

    display: flex;

    justify-content:
        space-between;

    align-items: center;

    margin-bottom: 20px;

}

.header h1 {

    color: #1e3a5f;

    margin-bottom: 5px;

}

.back {

    text-decoration: none;

    color: #1e3a5f;

}


/* ==========================
   CALENDAR HEADER
========================== */

.calendar {

    background: white;

    border-radius: 12px;

    overflow: hidden;

    box-shadow:
        0 3px 15px rgba(0,0,0,.08);

}

.calendar-header {

    padding: 20px;

    display: flex;

    justify-content:
        space-between;

    align-items: center;

    border-bottom:
        1px solid #eee;

}

.calendar-header h2 {

    margin: 0;

    color: #1e3a5f;

}

.nav-btn {

    text-decoration: none;

    padding: 8px 14px;

    background: #1e3a5f;

    color: white;

    border-radius: 6px;

}


/* ==========================
   WEEK
========================== */

.week {

    display: grid;

    grid-template-columns:
        repeat(7, 1fr);

    background: #1e3a5f;

    color: white;

}

.week div {

    padding: 12px;

    text-align: center;

    font-weight: bold;

}


/* ==========================
   DAYS
========================== */

.days {

    display: grid;

    grid-template-columns:
        repeat(7, 1fr);

}

.day {

    min-height: 130px;

    border-right:
        1px solid #eee;

    border-bottom:
        1px solid #eee;

    padding: 8px;

}

.empty {

    background: #f8f9fa;

}

.number {

    font-weight: bold;

    margin-bottom: 8px;

    color: #555;

}

.today {

    background: #eef5ff;

}


/* ==========================
   BOOKING
========================== */

.event {

    display: block;

    padding: 6px;

    margin-bottom: 5px;

    border-radius: 5px;

    background: #fadbd8;

    color: #922b21;

    font-size: 11px;

    text-decoration: none;

}

.event.pending {

    background: #fff3cd;

    color: #856404;

}

.available {

    display: block;

    font-size: 11px;

    color: #1e8449;

    margin-top: 8px;

}


/* ==========================
   LEGEND
========================== */

.legend {

    padding: 15px;

    display: flex;

    gap: 20px;

    font-size: 13px;

}

.legend span {

    display: inline-flex;

    align-items: center;

    gap: 5px;

}

.dot {

    width: 10px;

    height: 10px;

    border-radius: 50%;

    display: inline-block;

}

.red {
    background: #c0392b;
}

.yellow {
    background: #f1c40f;
}

.green {
    background: #27ae60;
}


@media(max-width: 700px) {

    .day {

        min-height: 90px;

    }

    .event {

        font-size: 9px;

    }

    .week div {

        font-size: 11px;

    }

}

</style>

</head>

<body>


<div class="container">


<div class="header">

<div>

<h1>
    📅 Kalender Room
</h1>

<p>
    Jadwal penggunaan ruangan FKG UNPAD
</p>

</div>


<a
    href="booking_saya.php"
    class="back">

    ← Booking Saya

</a>

</div>


<div class="calendar">


<!-- HEADER -->

<div class="calendar-header">

<a
    class="nav-btn"
    href="?bulan=<?= $prev_bulan; ?>&tahun=<?= $prev_tahun; ?>">

    ←

</a>


<h2>

<?= $nama_bulan[$bulan]; ?>

<?= $tahun; ?>

</h2>


<a
    class="nav-btn"
    href="?bulan=<?= $next_bulan; ?>&tahun=<?= $next_tahun; ?>">

    →

</a>

</div>


<!-- HARI -->

<div class="week">

<div>Sen</div>
<div>Sel</div>
<div>Rab</div>
<div>Kam</div>
<div>Jum</div>
<div>Sab</div>
<div>Min</div>

</div>


<!-- TANGGAL -->

<div class="days">


<?php for (
    $i = 1;
    $i < $hari_pertama;
    $i++
): ?>

<div class="day empty"></div>

<?php endfor; ?>


<?php for (
    $hari = 1;
    $hari <= $jumlah_hari;
    $hari++
): ?>


<?php

$tanggal_full = sprintf(
    '%04d-%02d-%02d',
    $tahun,
    $bulan,
    $hari
);

$is_today =
    $tanggal_full == date('Y-m-d');

?>


<div
    class="day <?= $is_today ? 'today' : ''; ?>">


<div class="number">

<?= $hari; ?>

</div>


<?php if (
    isset($booking[$hari])
): ?>


<?php foreach (
    $booking[$hari]
    as $event
): ?>


<a
    href="booking.php?room_id=<?= $event['room_id']; ?>&tanggal=<?= $tanggal_full; ?>"
    class="event <?= $event['status'] == 'menunggu' ? 'pending' : ''; ?>">


<strong>

<?= htmlspecialchars(
    $event['nama_room']
); ?>

</strong>


<br>


<?= htmlspecialchars(
    $event['nama_acara']
); ?>


<br>


<?= substr(
    $event['jam_mulai'],
    0,
    5
); ?>

-

<?= substr(
    $event['jam_selesai'],
    0,
    5
); ?>


</a>


<?php endforeach; ?>


<?php else: ?>


<?php if (
    $_SESSION['role'] == 'dosen'
): ?>

<a
    href="booking.php?tanggal=<?= $tanggal_full; ?>"
    class="available">

    🟢 Tersedia

</a>

<?php endif; ?>


<?php endif; ?>


</div>


<?php endfor; ?>


</div>


<div class="legend">

<span>
    <i class="dot red"></i>
    Disetujui
</span>

<span>
    <i class="dot yellow"></i>
    Menunggu
</span>

<span>
    <i class="dot green"></i>
    Tersedia
</span>

</div>


</div>

</div>

</body>

</html>