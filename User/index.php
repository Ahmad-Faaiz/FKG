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

<link rel="stylesheet" href="assets/css/index.css">

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

    Booking Saya

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

$is_today = $tanggal_full == date('Y-m-d');

$bisa_booking = $tanggal_full >= date('Y-m-d');

?>


<div class="day <?= $is_today ? 'today' : ''; ?> <?= $bisa_booking ? 'free' : 'lewat'; ?>">

    <div class="number">

        <?php if ($bisa_booking): ?>

            <a href="pilih_jadwal.php?tanggal=<?= $tanggal_full; ?>" class="number-link">
                <?= $hari; ?>
            </a>

        <?php else: ?>

            <span><?= $hari; ?></span>

        <?php endif; ?>

    </div>

</div>


<?php endfor; ?>


</div>

</div>

</div>

</body>

</html>