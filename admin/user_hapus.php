<?php

session_start();
include "../config/database.php";

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$id = (int) $_GET['id'];

if ($id == $_SESSION['user_id']) {
    die("Anda tidak dapat menghapus akun sendiri.");
}

mysqli_query($conn, "
    DELETE FROM users
    WHERE id = $id
");

header("Location: index.php?page=user");
exit;