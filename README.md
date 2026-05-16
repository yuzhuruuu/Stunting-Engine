# 📊 Sistem Pendukung Keputusan (SPK) Penanganan Stunting 
### Optimasi Skala Prioritas Urgensi Intervensi Menggunakan Algoritma Fuzzy-TOPSIS

[![Laravel](https://img.shields.io/badge/Framework-Laravel%2010-red?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/Language-PHP%208-777BB4?style=flat-square&logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/UI--UX-Bootstrap%205-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/Academic-Project--SBD-orange?style=flat-square)](#)

---

## 📌 Deskripsi Proyek
Aplikasi **Sistem Pendukung Keputusan (SPK) Penanganan Stunting** ini dirancang secara khusus untuk membantu tenaga medis (Puskesmas/Posyandu) dalam menentukan **skala prioritas rujukan dan intervensi gizi** terhadap balita yang terindikasi stunting. 

Dengan memanfaatkan **10.000+ data rekam medis**, sistem ini mengombinasikan logika **Fuzzy** untuk klasifikasi status gizi awal dan algoritma **TOPSIS (Technique for Order of Preference by Similarity to Ideal Solution)** untuk meranking balita dari tingkat urgensi paling kritis (paling mendekati solusi ideal positif masalah krisis gizi).

Proyek ini dikembangkan sebagai luaran akademis praktis untuk memenuhi tugas **Sistem Basis Data (SBD)** mahasiswa **Sistem Informasi, Universitas Negeri Semarang (UNNES)**.

---

## 🌟 Fitur Utama
* 🌓 **Premium Dual-Theme Dashboard (Light/Dark Mode):** Desain antarmuka modern yang konsisten di seluruh halaman dengan fitur transisi tema yang lembut tanpa merusak kontras visual data.
* 🧪 **Simulasi Bobot Kriteria Dinamis:** User/Petugas medis dapat mensimulasikan nilai bobot preferensi (Skala 1-5) untuk setiap kriteria (**C1: Umur Anak**, **C2: Berat Lahir**, **C3: Tinggi Badan Sekarang**, **C4: Riwayat ASI**) secara *real-time* sebelum menjalankan mesin pencari keputusan.
* 📈 **Analisis Grafik Interaktif:** Visualisasi proporsi dan sebaran komparatif status gizi dataset menggunakan grafik donat minimalis bergaya modern.
* 🧮 **Kalkulator Skrining Komprehensif:** Fitur uji mandiri pertumbuhan balita berbasis logika *fuzzy* untuk mendeteksi indikasi stunting awal secara cepat.
* 🎯 **Algoritma Fuzzy-TOPSIS Sinkron:** Perbaikan logika perankingan yang rasional, di mana kriteria umur dikondisikan sebagai *Cost* (1000 Hari Pertama Kelahiran / balita lebih muda mendapat prioritas pemulihan darurat), serta sinkronisasi label rekomendasi medis otomatis yang anti-error.
* 🖨 **Ekspor Dokumen Satu Klik:** Fitur penunjang laporan instan berupa tombol cetak langsung, serta ekspor file berbasis **Excel** dan **PDF** untuk keperluan administrasi medis.

---

## 🛠 Spesifikasi Teknologi
* **Core Framework:** Laravel 10 (MVC Architecture)
* **Server-side Language:** PHP 8.x
* **Database Engine:** MySQL / MariaDB
* **Front-End Styling:** Bootstrap 5 & FontAwesome 6 (Premium Custom Glassmorphism Effect Icons)
* **Data Presentation:** DataTables Responsive Plugin & Chart.js

---

## 📊 Pemetaan Kriteria SPK
Sistem ini mengambil keputusan prioritas berdasarkan 4 kriteria utama rekam medis:

| Kode | Kriteria Gizi / Medis | Jenis Kriteria | Deskripsi Urgensi |
| :---: | :--- | :---: | :--- |
| **C1** | Umur Balita (Bulan) | *Cost* | Prioritas tinggi pada balita usia lebih muda karena mengejar masa keemasan penanganan stunting (1000 HPK). |
| **C2** | Berat Badan Lahir (kg) | *Cost* | Balita dengan riwayat Berat Badan Lahir Rendah (BBLR) memicu risiko stunting lebih besar. |
| **C3** | Tinggi Badan Sekarang (cm) | *Cost* | Nilai tinggi badan yang menyimpang jauh di bawah rata-rata klinis memerlukan intervensi gizi paling mendesak. |
| **C4** | Riwayat ASI Eksklusif | *Benefit* | Balita yang tidak mendapatkan ASI Eksklusif memiliki tingkat urgensi intervensi nutrisi tambahan yang lebih tinggi. |

---

## 🚀 Panduan Instalasi Lokal (Localhost XAMPP)

1. **Clone Repository ini**
   ```bash
   git clone [https://github.com/NAMA_GITHUB_KAMU/spk-stunting.git](https://github.com/NAMA_GITHUB_KAMU/spk-stunting.git)
   cd spk-stunting

2. **Buka di web browser**
   http://localhost/spk-stunting/index.php
