<?php
// 1. Pastikan file koneksi sudah benar arahnya
include 'koneksi.php'; 
session_start();

// Hapus hasil SPK lama agar tidak tampil kembali setelah reset data
unset($_SESSION['hasil_spk']);

// 2. Jalankan query dan langsung cek apakah ada error dari MySQL
$query_reset = mysqli_query($koneksi, "TRUNCATE TABLE balita_simulasi");

if ($query_reset) {
    // Jika berhasil, kasih notifikasi dan paksa balik ke index.php
    echo "<script>
            alert('Tabel simulasi berhasil dikosongkan!'); 
            window.location.href = 'index.php';
          </script>";
    exit();
} else {
    // Jika gagal, tampilkan pesan error dari XAMPP biar ketahuan salahnya di mana
    die("Gagal mengosongkan tabel! Error MySQL: " . mysqli_error($koneksi));
}
?>