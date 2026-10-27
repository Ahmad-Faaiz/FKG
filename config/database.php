<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "room_fkg";

date_default_timezone_set('Asia/Jakarta');

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
