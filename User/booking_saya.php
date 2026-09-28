<?php

session_start();

include "../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = mysqli_query($conn, "
    SELECT
        bookings.*,
        rooms.nama_room,
        rooms.gedung
    FROM bookings
    JOIN rooms
        ON bookings.room_id = rooms.id
    WHERE bookings.user_id = $user_id
    ORDER BY bookings.tanggal DESC, bookings.jam_mulai DESC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Booking Saya</title>

    <style>

        body {
            font-family: Arial;
            background: #f4f6f9;
            margin: 0;
        }

        .container {
            padding: 30px;
        }

        h1 {
            color: #1e3a5f;
        }

        .back {
            display: inline-block;
            margin-bottom: 20px;
            color: #1e3a5f;
            text-decoration: none;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #1e3a5f;
            color: white;
        }

        .status {
            padding: 6px 10px;
            border-radius: 15px;
            font-size: 13px;
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

    </style>

</head>

<body>

<div class="container">

    <a href="index.php" class="back">
        ← Dashboard
    </a>

    <h1>Booking Saya</h1>

    <p>
        Daftar pemesanan room yang pernah kamu lakukan.
    </p>

    <?php if (isset($_GET['success'])): ?>

    <div style="
        background:#d5f5e3;
        color:#1e8449;
        padding:15px;
        border-radius:8px;
        margin:20px 0;
    ">

        ✅ Booking berhasil diajukan.

        <br>

        Silakan tunggu persetujuan admin.

    </div>

<?php endif; ?>

    <br>

    <table>

        <tr>

            <th>No</th>
            <th>Acara</th>
            <th>Room</th>
            <th>Tanggal</th>
            <th>Waktu</th>
            <th>Status</th>

        </tr>


        <?php

        $no = 1;

        while ($data = mysqli_fetch_assoc($query)):

        ?>

        <tr>

            <td>
                <?= $no++; ?>
            </td>

            <td>
                <?= htmlspecialchars($data['nama_acara']); ?>
            </td>

            <td>
                <?= htmlspecialchars($data['nama_room']); ?>
            </td>

            <td>
                <?= date('d-m-Y', strtotime($data['tanggal'])); ?>
            </td>

            <td>
                <?= substr($data['jam_mulai'], 0, 5); ?>
                -
                <?= substr($data['jam_selesai'], 0, 5); ?>
            </td>

            <td>

                <span class="status <?= $data['status']; ?>">

                    <?= ucfirst($data['status']); ?>

                </span>

            </td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>

</html>