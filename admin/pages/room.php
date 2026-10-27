<?php

$query = mysqli_query($conn, "SELECT * FROM rooms ORDER BY id DESC");

?>

<h1>Kelola Room</h1>

<p>Daftar ruangan FKG UNPAD</p>

<a href="index.php?page=room_tambah" class="btn">+ Tambah Room</a>

<table class="tabel">

    <tr>
        <th>No</th>
        <th>Nama Room</th>
        <th>Kode</th>
        <th>Gedung</th>
        <th>Lantai</th>
        <th>Kapasitas</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    <?php $no = 1; while ($data = mysqli_fetch_assoc($query)): ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($data['nama_room']); ?></td>
        <td><?= htmlspecialchars($data['kode_room']); ?></td>
        <td><?= htmlspecialchars($data['gedung']); ?></td>
        <td><?= htmlspecialchars($data['lantai']); ?></td>
        <td><?= $data['kapasitas']; ?> orang</td>

        <td>
            <?php if ($data['status'] == 'aktif'): ?>
                <span class="status aktif">Aktif</span>
            <?php else: ?>
                <span class="status nonaktif">Nonaktif</span>
            <?php endif; ?>
        </td>

  <td class="aksi">
    <a href="index.php?page=room_detail&id=<?= $data['id']; ?>" class="detail">Detail</a>

    <a href="index.php?page=room_edit&id=<?= $data['id']; ?>" class="edit">Edit</a>

    <a href="room_hapus.php?id=<?= $data['id']; ?>"
       class="hapus"
         onclick="return confirm('Menghapus room ini juga akan menghapus semua booking di room tersebut. Lanjutkan?')">Hapus</a>
</td>
    </tr>

    <?php endwhile; ?>

</table>