<?php
$host     = "localhost";
$username = "root";
$password = "";
$database = "db_spk_stunting";

$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek apakah koneksi berhasil atau gagal
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>