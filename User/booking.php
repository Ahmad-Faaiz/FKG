<?php

session_start();
include "../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$pesan = "";
$tipe_pesan = "";


// ==========================================
// DATA DARI KALENDER
// ==========================================

$room_selected = isset($_GET['room_id'])
    ? (int) $_GET['room_id']
    : 0;

$tanggal_selected = isset($_GET['tanggal'])
    ? $_GET['tanggal']
    : date('Y-m-d');

$jam_mulai_selected = isset($_GET['jam_mulai'])
    ? $_GET['jam_mulai'] : '';

$jam_selesai_selected = isset($_GET['jam_selesai']) 
    ? $_GET['jam_selesai'] : '';

// ==========================================
// PROSES BOOKING
// ==========================================

if (isset($_POST['booking'])) {

    $room_id = (int) $_POST['room_id'];
    $nama_acara = mysqli_real_escape_string(
        $conn,
        $_POST['nama_acara']
    );

    $tanggal = $_POST['tanggal'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $jumlah_peserta = (int) $_POST['jumlah_peserta'];

    $keterangan = mysqli_real_escape_string(
        $conn,
        $_POST['keterangan']
    );
    
    // ==========================================
    // CEK JAM
    // ==========================================
      
    $pola_jam = '/^([01]\d|2[0-3]):[0-5]\d$/';

    if (!preg_match($pola_jam, $jam_mulai) ||
        !preg_match($pola_jam, $jam_selesai)) {

        $error = "Format jam tidak valid.";
    } elseif ($jam_mulai >= $jam_selesai) {

        $pesan = "Jam selesai harus lebih besar dari jam mulai.";
        $tipe_pesan = "error";
    
    } elseif ($tanggal < date('Y-m-d')) {

        $pesan = "Tanggal yang dipilih sudah lewat.";
        $tipe_pesan = "error";

    } elseif ($tanggal == date('Y-m-d') && $jam_mulai <= date('H:i', strtotime('+1 hour'))) {

        $pesan = "Jam mulai sudah lewat. Pilih jam setelah " . date('H:i', strtotime('+1 hour')) . ".";
        $tipe_pesan = "error";

    } else {


        // ==========================================
        // CEK ROOM
        // ==========================================

        $room_query = mysqli_query($conn, "
            SELECT *
            FROM rooms
            WHERE id = $room_id
            AND status = 'aktif'
        ");

        $room = mysqli_fetch_assoc($room_query);


        if (!$room) {

            $pesan = "Room tidak ditemukan atau sedang tidak aktif.";
            $tipe_pesan = "error";

        } elseif ($jumlah_peserta > $room['kapasitas']) {

            $pesan = "Jumlah peserta melebihi kapasitas room.";
            $tipe_pesan = "error";

        } else {


            // ==========================================
            // CEK BENTROK
            // ==========================================

            $cek = mysqli_query($conn, "
                SELECT *
                FROM bookings

                WHERE room_id = $room_id

                AND tanggal = '$tanggal'

                AND status IN
                    ('menunggu', 'disetujui')

                AND jam_mulai < '$jam_selesai'

                AND jam_selesai > '$jam_mulai'
            ");


            if (mysqli_num_rows($cek) > 0) {

                $pesan = "❌ Jadwal tersebut sudah digunakan.";
                $tipe_pesan = "error";

            } else {


                // ==========================================
                // SIMPAN BOOKING
                // ==========================================

                $query = mysqli_query($conn, "
                    INSERT INTO bookings
                    (
                        user_id,
                        room_id,
                        nama_acara,
                        tanggal,
                        jam_mulai,
                        jam_selesai,
                        jumlah_peserta,
                        keterangan,
                        status
                    )

                    VALUES
                    (
                        $user_id,
                        $room_id,
                        '$nama_acara',
                        '$tanggal',
                        '$jam_mulai',
                        '$jam_selesai',
                        $jumlah_peserta,
                        '$keterangan',
                        'menunggu'
                    )
                ");


                if ($query) {

                    header(
                        "Location: booking_saya.php?success=1"
                    );

                    exit;

                } else {

                    $pesan = "Booking gagal disimpan.";
                    $tipe_pesan = "error";

                }

            }

        }

    }

}


// ==========================================
// AMBIL ROOM
// ==========================================

$rooms = mysqli_query($conn, "
    SELECT *
    FROM rooms
    WHERE status = 'aktif'
    ORDER BY nama_room
");

?>

<!DOCTYPE html>

<html lang="id">

<head>

<meta charset="UTF-8">

<title>Booking Room - FKG UNPAD</title>

<link rel="stylesheet" href="assets/css/booking.css">

</head>

<body>


<div class="container">

<nav class="navigation" aria-label="Navigasi dosen">
    <a href="pilih_jadwal.php">← kembali</a>
</nav>


<div class="card">

<h1>📅 Booking Room</h1>

<p class="subtitle">

Ajukan pemakaian ruangan untuk kegiatan FKG UNPAD.

</p>


<?php if ($pesan != ""): ?>

<div class="pesan <?= $tipe_pesan; ?>">

<?= htmlspecialchars($pesan); ?>

</div>

<?php endif; ?>


<div class="info">

Booking akan berstatus
<strong>Menunggu</strong>
sampai disetujui oleh admin.

</div>


<form method="POST">

<label>
Nama Acara
</label>

<input
    type="text"
    name="nama_acara"
    placeholder="Contoh: Seminar Nasional FKG"
    required
>


<label>
Tanggal
</label>

<input
    type="date"
    name="tanggal"
    value="<?= htmlspecialchars($tanggal_selected); ?>"
    min="<?= date('Y-m-d'); ?>"
    required
>


<label>
Room
</label>

<select name="room_id" required>

<option value="">
-- Pilih Room --
</option>


<?php while ($room = mysqli_fetch_assoc($rooms)): ?>

<option
    value="<?= $room['id']; ?>"
    <?= $room['id'] == $room_selected
        ? 'selected'
        : ''; ?>
>

<?= htmlspecialchars(
    $room['nama_room']
); ?>

-

<?= htmlspecialchars(
    $room['gedung']
); ?>

-

Kapasitas
<?= $room['kapasitas']; ?>

</option>

<?php endwhile; ?>

</select>


<div class="row">


<div>
    <label>Jam Mulai</label>
    <input
        type="text"
        name="jam_mulai"
        class="jam-input"
        value="<?= htmlspecialchars($jam_mulai_selected); ?>"
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
        value="<?= htmlspecialchars($jam_selesai_selected); ?>"
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


<label>
Jumlah Peserta
</label>

<input
    type="number"
    name="jumlah_peserta"
    min="1"
    placeholder="Contoh: 50"
    required
>


<label>
Keterangan
</label>

<textarea
    name="keterangan"
    placeholder="Contoh: Membutuhkan projector dan microphone"
></textarea>


<button
    type="submit"
    name="booking">

📅 Ajukan Booking

</button>


</form>

</div>

<script src="assets/js/booking.js"></script>
</body>

</html>