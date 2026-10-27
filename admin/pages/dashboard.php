<?php

// ==========================================
// STATISTIK
// ==========================================

$total_room = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM rooms WHERE status = 'aktif'
"))['total'];

$total_user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM users
"))['total'];

$total_booking = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM bookings
"))['total'];

$total_menunggu = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM bookings WHERE status = 'menunggu'
"))['total'];


// ==========================================
// BOOKING MENUNGGU
// ==========================================

$booking_menunggu = mysqli_query($conn, "

    SELECT
        bookings.*,
        users.nama AS nama_user,
        rooms.nama_room

    FROM bookings

    JOIN users ON bookings.user_id = users.id
    JOIN rooms ON bookings.room_id = rooms.id

    WHERE bookings.status = 'menunggu'

    ORDER BY bookings.tanggal ASC, bookings.jam_mulai ASC

    LIMIT 5
");

?>

<h1>Dashboard Admin</h1>

<p class="subtitle">Kelola sistem peminjaman room FKG UNPAD.</p>


<div class="stats">

    <div class="stat">
        <div class="number"><?= $total_room; ?></div>
        <div class="label">Room Aktif</div>
    </div>

    <div class="stat">
        <div class="number"><?= $total_user; ?></div>
        <div class="label">Total User</div>
    </div>

    <div class="stat">
        <div class="number"><?= $total_booking; ?></div>
        <div class="label">Total Booking</div>
    </div>

    <div class="stat">
        <div class="number"><?= $total_menunggu; ?></div>
        <div class="label">Menunggu Approval</div>
    </div>

</div>


<div class="section">

<h2>⏳ Menunggu Persetujuan</h2>

<table>

<tr>
    <th>Pemesan</th>
    <th>Acara</th>
    <th>Room</th>
    <th>Tanggal</th>
    <th>Waktu</th>
    <th>Aksi</th>
</tr>

<?php if (mysqli_num_rows($booking_menunggu) == 0): ?>
<tr>
    <td colspan="6">Tidak ada booking yang menunggu.</td>
</tr>
<?php endif; ?>

<?php while ($data = mysqli_fetch_assoc($booking_menunggu)): ?>

<tr>
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
        <a class="action approve"
           href="index.php?page=booking&aksi=setujui&id=<?= $data['id']; ?>"
           onclick="return confirm('Setujui booking ini?')">
            ✓ Setujui
        </a>

        <a class="action reject"
           href="index.php?page=booking&aksi=tolak&id=<?= $data['id']; ?>"
           onclick="return confirm('Tolak booking ini?')">
            ✕ Tolak
        </a>
    </td>
</tr>

<?php endwhile; ?>

</table>

</div>