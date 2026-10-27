<?php

$error = "";

if (isset($_POST['simpan'])) {

    $nama_room = mysqli_real_escape_string($conn, $_POST['nama_room']);
    $kode_room = mysqli_real_escape_string($conn, $_POST['kode_room']);
    $gedung    = mysqli_real_escape_string($conn, $_POST['gedung']);
    $lantai    = mysqli_real_escape_string($conn, $_POST['lantai']);
    $kapasitas = (int) $_POST['kapasitas'];
    $fasilitas = mysqli_real_escape_string($conn, $_POST['fasilitas']);
    $status    = $_POST['status'] === 'nonaktif' ? 'nonaktif' : 'aktif';

    $query = "INSERT INTO rooms
        (nama_room, kode_room, gedung, lantai, kapasitas, fasilitas, status)
        VALUES
        ('$nama_room', '$kode_room', '$gedung', '$lantai', '$kapasitas', '$fasilitas', '$status')";

    if (mysqli_query($conn, $query)) {
        header("Location: index.php?page=room");
        exit;
    }

    $error = "Gagal menyimpan data: " . mysqli_error($conn);
}

?>
    <style>
.form-card {
    width: 400px;
    margin: 30px auto;
    padding: 25px;
    background: white;
    border-radius: 10px;
    box-sizing: border-box;
}
</style>

<div class="form-card">

    <h1>Tambah Room</h1>

    <?php if ($error): ?>
        <div class="pesan-error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Nama Room</label>
        <input type="text" name="nama_room" placeholder="Contoh: Ruang Seminar" required>

        <label>Kode Room</label>
        <input type="text" name="kode_room" placeholder="Contoh: RSM-01" required>

        <label>Gedung</label>
        <input type="text" name="gedung" placeholder="Contoh: Gedung A" required>

        <label>Lantai</label>
        <input type="text" name="lantai" placeholder="Contoh: Lantai 2" required>

        <label>Kapasitas</label>
        <input type="number" name="kapasitas" min="1" placeholder="Contoh: 100" required>

        <label>Fasilitas</label>
        <textarea name="fasilitas" placeholder="Contoh: AC, Proyektor, Sound System"></textarea>

        <label>Status</label>
        <select name="status">
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Nonaktif</option>
        </select>

        <button type="submit" name="simpan">Simpan Room</button>

        <a href="index.php?page=room" class="batal-link">Batal</a>

    </form>

</div>