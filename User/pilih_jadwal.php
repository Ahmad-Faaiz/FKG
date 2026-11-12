<?php

session_start();
include "../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../login.php");
    exit;
}

// ==========================================
// TANGGAL & JAM
// ==========================================

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) || $tanggal < date('Y-m-d')) {
    $tanggal = date('Y-m-d');
}

$jam_mulai   = $_GET['jam_mulai'] ?? '';
$jam_selesai = $_GET['jam_selesai'] ?? '';
$error       = '';
$tampil_room = false;

if ($jam_mulai !== '' || $jam_selesai !== '') {

 $pola_jam = '/^([01]\d|2[0-3]):[0-5]\d$/';

    if (!preg_match($pola_jam, $jam_mulai) ||
        !preg_match($pola_jam, $jam_selesai)) {

        $error = "Format jam tidak valid.";
 
    } elseif ($tanggal == date('Y-m-d') && $jam_mulai <= date('H:i', strtotime('+1 hour'))) {

        $error = "Jam mulai sudah lewat. Pilih 1 jam setelah " . date('H:i') . ".";

    } elseif ($jam_mulai >= $jam_selesai) {

        $error = "Jam selesai harus lebih besar dari jam mulai.";

    } else {
        $tampil_room = true;
    }
}

// ==========================================
// DATA ROOM & BOOKING PADA TANGGAL INI
// ==========================================

$jadwal_room = [];
$rooms       = [];

if ($tampil_room) {

    $q = mysqli_query($conn, "
        SELECT *
        FROM bookings
        WHERE tanggal = '$tanggal'
        AND status IN ('menunggu', 'disetujui')
        ORDER BY jam_mulai
    ");

    while ($b = mysqli_fetch_assoc($q)) {
        $jadwal_room[$b['room_id']][] = $b;
    }

    $rooms = mysqli_query($conn, "
        SELECT *
        FROM rooms
        WHERE status = 'aktif'
        ORDER BY nama_room
    ");
}

$nama_hari = [1=>'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
$label_tanggal = $nama_hari[date('N', strtotime($tanggal))] . ', ' . date('d-m-Y', strtotime($tanggal));

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pilih Jadwal - FKG UNPAD</title>

<link rel="stylesheet" href="assets/css/pilih_jadwal.css">
</head>

<body>

<div class="container">

<nav class="navigation">
    <a href="index.php">Kembali ke Kalender</a>
</nav>


<!-- LANGKAH 1: PILIH JAM -->
<div class="card">

<h1>📅 <?= htmlspecialchars($label_tanggal); ?></h1>

<?php if ($error): ?>
    <div class="pesan-error"><?= htmlspecialchars($error); ?></div>
<?php endif; ?>

<form method="GET">

<input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal); ?>">

<div class="row">

<div>
    <label>Jam Mulai</label>
    <input
        type="text"
        name="jam_mulai"
        class="jam-input"
        value="<?= htmlspecialchars($jam_mulai); ?>"
        placeholder="08:00"
        maxlength="5"
        inputmode="numeric"
        pattern="([01][0-9]|2[0-3]):[0-5][0-9]"
        title="Format 24 jam, contoh: 08:00 atau 13:30"
        autocomplete="off"
        required
    >
</div>

<div>
    <label>Jam Selesai</label>
    <input
        type="text"
        name="jam_selesai"
        class="jam-input"
        value="<?= htmlspecialchars($jam_selesai); ?>"
        placeholder="10:00"
        maxlength="5"
        inputmode="numeric"
        pattern="([01][0-9]|2[0-3]):[0-5][0-9]"
        title="Format 24 jam, contoh: 08:00 atau 13:30"
        autocomplete="off"
        required
    >
</div>

</div>

<button type="submit">🔍 Lihat Ruangan Tersedia</button>

</form>

</div>


<!-- LANGKAH 2: DAFTAR RUANGAN -->
<?php if ($tampil_room): ?>

<div class="card">

<h2>Ruangan pada <?= htmlspecialchars($jam_mulai); ?> - <?= htmlspecialchars($jam_selesai); ?></h2>

<?php while ($room = mysqli_fetch_assoc($rooms)): ?>

<?php

$semua = $jadwal_room[$room['id']] ?? [];
$bentrok = [];
$lainnya = [];

foreach ($semua as $b) {

    $mulai   = substr($b['jam_mulai'], 0, 5);
    $selesai = substr($b['jam_selesai'], 0, 5);

    if ($mulai < $jam_selesai && $selesai > $jam_mulai) {
        $bentrok[] = $b;
    } else {
        $lainnya[] = $b;
    }
}

$terpakai = count($bentrok) > 0;

$link = "booking.php?room_id=" . $room['id']
      . "&tanggal=" . urlencode($tanggal)
      . "&jam_mulai=" . urlencode($jam_mulai)
      . "&jam_selesai=" . urlencode($jam_selesai);

?>

<div class="room <?= $terpakai ? 'terpakai' : ''; ?>">

<div class="room-top">

<div>
    <h3><?= htmlspecialchars($room['nama_room']); ?></h3>
    <div class="meta">
        <?= htmlspecialchars($room['gedung']); ?> · Kapasitas <?= (int) $room['kapasitas']; ?> orang
    </div>
</div>

<?php if ($terpakai): ?>
    <span class="badge no">Terpakai</span>
<?php else: ?>
    <span class="badge ok">Tersedia</span>
<?php endif; ?>

</div>


<?php foreach ($bentrok as $b): ?>
    <div class="acara <?= $b['status'] == 'menunggu' ? 'menunggu' : ''; ?>">
        <strong><?= htmlspecialchars($b['nama_acara']); ?></strong><br>
        <?= substr($b['jam_mulai'], 0, 5); ?> - <?= substr($b['jam_selesai'], 0, 5); ?>
        (<?= $b['status'] == 'menunggu' ? 'Menunggu persetujuan' : 'Disetujui'; ?>)
    </div>
<?php endforeach; ?>


<?php if (!$terpakai): ?>
    <div style="margin-top:12px;">
        <a class="btn" href="<?= $link; ?>">Pilih Ruangan Ini →</a>
    </div>
<?php endif; ?>


<?php if (count($lainnya) > 0): ?>
    <div class="sub">Jadwal lain di ruangan ini hari itu:</div>
    <?php foreach ($lainnya as $b): ?>
        <div class="acara lain">
            <?= substr($b['jam_mulai'], 0, 5); ?> - <?= substr($b['jam_selesai'], 0, 5); ?>
            · <?= htmlspecialchars($b['nama_acara']); ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

</div>

<?php endwhile; ?>

</div>

<?php endif; ?>

</div>

<script src="assets/js/pilih_jadwal.js"></script>
</body>
</html>