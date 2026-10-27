<?php

// ==========================================
// PROSES PERUBAHAN STATUS
// ==========================================

if (isset($_GET['aksi']) && isset($_GET['id'])) {

    $id = (int) $_GET['id'];
    $aksi = $_GET['aksi'];

    if ($aksi == 'setujui') {

        // Ambil data booking
        $cek_booking = mysqli_query($conn, "
            SELECT *
            FROM bookings
            WHERE id = $id
        ");

        $booking = mysqli_fetch_assoc($cek_booking);

        if (!$booking) {
            die("Booking tidak ditemukan.");
        }


        // ==========================================
        // CEK BENTROK SEBELUM DISETUJUI
        // ==========================================

        $room_id = $booking['room_id'];
        $tanggal = $booking['tanggal'];
        $jam_mulai = $booking['jam_mulai'];
        $jam_selesai = $booking['jam_selesai'];

        $bentrok = mysqli_query($conn, "
            SELECT *
            FROM bookings
            WHERE room_id = $room_id
            AND tanggal = '$tanggal'
            AND id != $id
            AND status = 'disetujui'
            AND jam_mulai < '$jam_selesai'
            AND jam_selesai > '$jam_mulai'
        ");

        if (mysqli_num_rows($bentrok) > 0) {

            echo "
            <script>
                alert('Booking tidak dapat disetujui karena jadwal bentrok!');
                window.location = 'index.php?page=booking';
            </script>
            ";

            exit;
        }

        $status = 'disetujui';

    } elseif ($aksi == 'tolak') {

        $status = 'ditolak';

    } elseif ($aksi == 'batalkan') {

        $status = 'dibatalkan';

    } else {

        $status = '';
    }

    if ($status != '') {

        mysqli_query($conn, "
            UPDATE bookings
            SET status = '$status'
            WHERE id = $id
        ");
    }

    header("Location: index.php?page=booking");
    exit;
}


// ==========================================
// AMBIL DATA BOOKING
// ==========================================

$query = mysqli_query($conn, "
    SELECT
        bookings.*,
        users.nama AS nama_user,
        rooms.nama_room,
        rooms.gedung
    FROM bookings

    JOIN users ON bookings.user_id = users.id
    JOIN rooms ON bookings.room_id = rooms.id

    ORDER BY bookings.tanggal ASC, bookings.jam_mulai ASC
");

?>

<h1>Kelola Booking</h1>

<p>Kelola pengajuan pemesanan room.</p>

<br>

<table class="tabel">

    <tr>
        <th>No</th>
        <th>Pemesan</th>
        <th>Acara</th>
        <th>Room</th>
        <th>Tanggal</th>
        <th>Waktu</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php $no = 1; while ($data = mysqli_fetch_assoc($query)): ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($data['nama_user']); ?></td>
        <td><?= htmlspecialchars($data['nama_acara']); ?></td>
        <td><?= htmlspecialchars($data['nama_room']); ?></td>
        <td><?= date('d-m-Y', strtotime($data['tanggal'])); ?></td>
        <td>
            <?= substr($data['jam_mulai'], 0, 5); ?>
            -
            <?= substr($data['jam_selesai'], 0, 5); ?>
        </td>

        <td>
            <span class="status <?= htmlspecialchars($data['status']); ?>">
                <?= ucfirst(htmlspecialchars($data['status'])); ?>
            </span>
        </td>

        <td class="aksi">

            <?php if ($data['status'] == 'menunggu'): ?>

                <a href="index.php?page=booking&aksi=setujui&id=<?= $data['id']; ?>"
                   class="setuju"
                   onclick="return confirm('Setujui booking ini?')">
                    ✓ Setujui
                </a>

                <a href="index.php?page=booking&aksi=tolak&id=<?= $data['id']; ?>"
                   class="tolak"
                   onclick="return confirm('Tolak booking ini?')">
                    ✕ Tolak
                </a>

            <?php elseif ($data['status'] == 'disetujui'): ?>

                <a href="index.php?page=booking&aksi=batalkan&id=<?= $data['id']; ?>"
                   class="batal"
                   onclick="return confirm('Batalkan booking ini?')">
                    Batalkan
                </a>

            <?php else: ?>

                -

            <?php endif; ?>

        </td>
    </tr>

    <?php endwhile; ?>

</table>