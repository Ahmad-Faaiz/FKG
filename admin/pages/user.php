<?php

$query = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");

?>

<h1>Kelola User</h1>

<p>Kelola akun Admin dan Dosen FKG UNPAD.</p>

<a href="index.php?page=user_tambah" class="btn">+ Tambah user</a>


<table class="tabel">

    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
        <th>Aksi</th>
    </tr>

    <?php $no = 1; while ($data = mysqli_fetch_assoc($query)): ?>

    <tr>
        <td><?= $no++; ?></td>
        <td><?= htmlspecialchars($data['nama']); ?></td>
        <td><?= htmlspecialchars($data['email']); ?></td>

        <td>
            <span class="role role-<?= htmlspecialchars($data['role']); ?>">
                <?= ucfirst(htmlspecialchars($data['role'])); ?>
            </span>
        </td>

        <td>
            <?php if ($data['id'] != $_SESSION['user_id']): ?>

                <a class="hapus"
                   href="user_hapus.php?id=<?= $data['id']; ?>"
                    onclick="return confirm('Menghapus user ini juga akan menghapus semua booking miliknya. Lanjutkan?')">
                    Hapus
                </a>

            <?php else: ?>

                Akun Anda

            <?php endif; ?>
        </td>
    </tr>

    <?php endwhile; ?>

</table>