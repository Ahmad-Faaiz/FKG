<?php

include "../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = (int) $_GET['id'];

$result = mysqli_query(
    $conn,
    "SELECT * FROM rooms WHERE id = $id"
);

$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Room tidak ditemukan.");
}


if (isset($_POST['update'])) {

    $nama_room = mysqli_real_escape_string(
        $conn,
        $_POST['nama_room']
    );

    $kode_room = mysqli_real_escape_string(
        $conn,
        $_POST['kode_room']
    );

    $gedung = mysqli_real_escape_string(
        $conn,
        $_POST['gedung']
    );

    $lantai = mysqli_real_escape_string(
        $conn,
        $_POST['lantai']
    );

    $kapasitas = (int) $_POST['kapasitas'];

    $fasilitas = mysqli_real_escape_string(
        $conn,
        $_POST['fasilitas']
    );

    $status = $_POST['status'];


    $query = "UPDATE rooms SET

        nama_room = '$nama_room',
        kode_room = '$kode_room',
        gedung = '$gedung',
        lantai = '$lantai',
        kapasitas = '$kapasitas',
        fasilitas = '$fasilitas',
        status = '$status'

        WHERE id = $id";


    if (mysqli_query($conn, $query)) {

        header("Location: index.php?page=room");
        exit;

    } else {

        echo "Gagal update: "
             . mysqli_error($conn);

    }

}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Edit Room</title>

    <link rel="stylesheet" href="assets/css/room_edit.css">

</head>

<body>

<div class="container">

    <h1>Edit Room</h1>

    <form method="POST">

        <label>Nama Room</label>

        <input
            type="text"
            name="nama_room"
            value="<?= htmlspecialchars($data['nama_room']); ?>"
            required
        >


        <label>Kode Room</label>

        <input
            type="text"
            name="kode_room"
            value="<?= htmlspecialchars($data['kode_room']); ?>"
            required
        >


        <label>Gedung</label>

        <input
            type="text"
            name="gedung"
            value="<?= htmlspecialchars($data['gedung']); ?>"
            required
        >


        <label>Lantai</label>

        <input
            type="text"
            name="lantai"
            value="<?= htmlspecialchars($data['lantai']); ?>"
            required
        >


        <label>Kapasitas</label>

        <input
            type="number"
            name="kapasitas"
            value="<?= $data['kapasitas']; ?>"
            required
        >


        <label>Fasilitas</label>

        <textarea name="fasilitas"><?= htmlspecialchars($data['fasilitas']); ?></textarea>


        <label>Status</label>

        <select name="status">

            <option value="aktif"
                <?= $data['status'] == 'aktif' ? 'selected' : ''; ?>>
                Aktif
            </option>

            <option value="nonaktif"
                <?= $data['status'] == 'nonaktif' ? 'selected' : ''; ?>>
                Nonaktif
            </option>

        </select>


        <button
            type="submit"
            name="update">
            Update Room
        </button>

     <a href="index.php?page=room" class="batal-link" style="margin-left:0;">
        batal
    </a>
    </form>

</div>

</body>

</html>