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

<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    font-family: Arial;
    background: #f4f6f9;
}

.container {
    width: 800px;
    max-width: 95%;
    margin: 40px auto;
}

.navigation {
    display: flex;
    gap: 18px;
    margin-bottom: 20px;
}

.navigation a {
    color: #1e3a5f;
    text-decoration: none;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,.08);
    margin-bottom: 20px;
}

h1, h2 { color: #1e3a5f; margin-top: 0; }

label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
}

input {
    width: 100%;
    padding: 11px;
    border: 1px solid #ccc;
    border-radius: 6px;
}

.row { display: flex; gap: 15px; }
.row > div { flex: 1; }

button, .btn {
    display: inline-block;
    padding: 11px 18px;
    border: none;
    border-radius: 6px;
    background: #1e3a5f;
    color: white;
    font-size: 14px;
    text-decoration: none;
    cursor: pointer;
}

button { width: 100%; margin-top: 20px; }

.pesan-error {
    background: #fadbd8;
    color: #922b21;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 15px;
}

/* Kartu room */
.room {
    border: 1px solid #e5e8ec;
    border-left: 5px solid #27ae60;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 12px;
}

.room.terpakai {
    border-left-color: #c0392b;
    background: #fdf6f5;
}

.room-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
}

.room h3 { margin: 0 0 4px; color: #1e3a5f; }

.meta { font-size: 13px; color: #666; }

.badge {
    font-size: 12px;
    font-weight: bold;
    padding: 4px 10px;
    border-radius: 20px;
    white-space: nowrap;
}

.badge.ok  { background: #d5f5e3; color: #1e8449; }
.badge.no  { background: #fadbd8; color: #922b21; }

.acara {
    margin-top: 10px;
    font-size: 13px;
    padding: 8px;
    border-radius: 5px;
    background: #fadbd8;
    color: #922b21;
}

.acara.menunggu {
    background: #fff3cd;
    color: #856404;
}

.acara.lain {
    background: #f1f3f5;
    color: #555;
}

.sub {
    font-size: 12px;
    color: #888;
    margin-top: 10px;
}

@media (max-width: 600px) {
    .row { flex-direction: column; }
}
</style>
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
<script>
document.querySelectorAll('.jam-input').forEach(function (input) {

    input.addEventListener('input', function () {

        var angka = this.value.replace(/\D/g, '').slice(0, 4);

        if (angka.length > 2) {
            angka = angka.slice(0, 2) + ':' + angka.slice(2);
        }

        this.value = angka;
    });

});
</script>
</body>
</html>