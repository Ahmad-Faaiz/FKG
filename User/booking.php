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

    if ($jam_mulai >= $jam_selesai) {

        $pesan = "Jam selesai harus lebih besar dari jam mulai.";
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
    width: 700px;
    max-width: 95%;
    margin: 40px auto;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 12px;

    box-shadow:
        0 4px 15px rgba(0,0,0,.08);
}

h1 {
    color: #1e3a5f;
}

.subtitle {
    color: #666;
    margin-bottom: 25px;
}

label {
    display: block;
    font-weight: bold;
    margin-top: 15px;
    margin-bottom: 6px;
}

input,
select,
textarea {
    width: 100%;
    padding: 11px;

    border:
        1px solid #ccc;

    border-radius: 6px;
}

textarea {
    height: 100px;
    resize: vertical;
}

.row {
    display: flex;
    gap: 15px;
}

.row > div {
    flex: 1;
}

button {
    width: 100%;

    margin-top: 25px;

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

.back {
    display: inline-block;

    margin-bottom: 20px;

    color: #1e3a5f;

    text-decoration: none;
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

.pesan {
    padding: 12px;

    margin-bottom: 20px;

    border-radius: 6px;
}

.success {
    background: #d5f5e3;

    color: #1e8449;
}

.error {
    background: #fadbd8;

    color: #922b21;
}

.info {
    background: #d6eaf8;

    color: #21618c;

    padding: 12px;

    border-radius: 6px;

    margin-bottom: 20px;
}

</style>

</head>

<body>


<div class="container">

<nav class="navigation" aria-label="Navigasi dosen">
    <a href="index.php">← Dashboard</a>
    <a href="kalender.php">Kembali ke Kalender</a>
    <a href="booking_saya.php">Booking Saya</a>
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

<label>
Jam Mulai
</label>

<input
    type="time"
    name="jam_mulai"
    required
>

</div>


<div>

<label>
Jam Selesai
</label>

<input
    type="time"
    name="jam_selesai"
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

</div>

</body>

</html>

</div>

</div>

</body>

</html>