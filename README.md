# 📊 Sistem Pendukung Keputusan (SPK) Penanganan Stunting
## Optimasi Skala Prioritas Urgensi Intervensi Menggunakan Algoritma Fuzzy-TOPSIS

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white">
  <img src="https://img.shields.io/badge/Frontend-HTML5%20%26%20CSS3-E34F26?style=for-the-badge&logo=html5&logoColor=white">
  <img src="https://img.shields.io/badge/JavaScript-Interactive-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black">
  <img src="https://img.shields.io/badge/Academic-Project-orange?style=for-the-badge">
</p>

---

# 📌 Tentang Proyek

Aplikasi **Sistem Pendukung Keputusan (SPK) Penanganan Stunting** merupakan sistem berbasis web yang dirancang untuk membantu tenaga kesehatan seperti **Puskesmas**, **Posyandu**, maupun petugas medis dalam menentukan **skala prioritas intervensi penanganan stunting pada balita**.

Sistem ini menerapkan metode:

- **Fuzzy Logic** → untuk klasifikasi kondisi awal balita
- **TOPSIS (Technique for Order Preference by Similarity to Ideal Solution)** → untuk menentukan ranking prioritas penanganan

Dengan pendekatan tersebut, sistem dapat membantu proses pengambilan keputusan menjadi:

✅ Lebih cepat  
✅ Lebih objektif  
✅ Lebih akurat  
✅ Mengurangi subjektivitas manusia  

---

# 🎯 Tujuan Pengembangan

Tujuan utama dari pengembangan sistem ini adalah:

- Membantu tenaga kesehatan menentukan prioritas intervensi stunting
- Mempermudah proses pengolahan data balita
- Meningkatkan efisiensi pengambilan keputusan
- Mengimplementasikan metode SPK berbasis Fuzzy-TOPSIS
- Sebagai media pembelajaran dan implementasi akademik Sistem Pendukung Keputusan

---
# Link Akses Publik : stunting-engine.kesug.com

# 🌟 Fitur Utama Sistem

## 🧮 Perhitungan Fuzzy-TOPSIS
Sistem dapat melakukan proses:
- Fuzzifikasi data
- Normalisasi matriks keputusan
- Pembobotan kriteria
- Perhitungan solusi ideal positif dan negatif
- Perankingan prioritas balita

---

## 📊 Dashboard Monitoring
Menampilkan:
- Total data balita
- Statistik status stunting
- Hasil ranking prioritas
- Informasi visual data

---

## 📈 Grafik Statistik
Visualisasi data menggunakan grafik interaktif untuk:
- Persentase stunting
- Sebaran data balita
- Hasil analisis SPK

---

## 🧪 Simulasi Bobot Kriteria
Petugas dapat mengubah bobot penilaian setiap kriteria untuk melihat perubahan hasil ranking secara dinamis.

---

## 🖨 Export & Print
Mendukung:
- Cetak laporan
- Export Excel
- Export PDF

---

## 🌙 Light & Dark Mode
Tampilan modern dengan:
- Tema terang
- Tema gelap
- Transisi visual yang nyaman

---

# 🏗 Metode yang Digunakan

# 1️⃣ Fuzzy Logic

Metode fuzzy digunakan untuk:
- Mengubah data numerik menjadi nilai linguistik
- Menentukan tingkat kondisi balita berdasarkan parameter tertentu

---

# 2️⃣ TOPSIS

TOPSIS digunakan untuk:
- Menghitung solusi ideal positif
- Menghitung solusi ideal negatif
- Menentukan ranking prioritas intervensi

Konsep utama:
> Alternatif terbaik adalah yang paling dekat dengan solusi ideal positif dan paling jauh dari solusi ideal negatif.

---

# 📊 Kriteria Penilaian

| Kode | Kriteria | Jenis | Keterangan |
|---|---|---|---|
| C1 | Umur Balita | Cost | Balita usia lebih muda diprioritaskan |
| C2 | Berat Badan Lahir | Cost | BBLR meningkatkan risiko stunting |
| C3 | Tinggi Badan Saat Ini | Cost | Tinggi badan rendah perlu intervensi cepat |
| C4 | Riwayat ASI Eksklusif | Benefit | ASI eksklusif membantu pertumbuhan optimal |

---

# 🛠 Teknologi yang Digunakan

| Teknologi | Fungsi |
|---|---|
| PHP Native | Backend |
| HTML5 | Struktur halaman |
| CSS3 | Styling |
| JavaScript | Interaktivitas |
| MySQL | Database |
| phpMyAdmin | Pengelolaan database |
| Chart.js | Grafik statistik |

---

# 📂 Struktur Folder Project

```bash
spk-stunting/
├── koneksi.php
├── index.php
├── login.php
├── logout.php
└── README.md
```

---

# ⚙️ Persyaratan Sistem

Sebelum menjalankan project, pastikan sudah menginstall:

- XAMPP / Laragon
- PHP 8.x
- MySQL / MariaDB
- Web Browser

---

# 🚀 Cara Instalasi Project

# 1️⃣ Clone Repository

```bash
git clone https://github.com/yuzhuruuu/spk-stunting.git
```

---

# 2️⃣ Pindahkan Folder Project
# 3️⃣ Jalankan XAMPP
# 4️⃣ Membuat Database
# 5️⃣ Import Database
# 6️⃣ Konfigurasi Database
# 7️⃣ Menjalankan Project

---

# 📸 Tampilan Sistem

## Dashboard
- Statistik data
- Informasi stunting
- Grafik analisis

## Data Balita
- CRUD data balita
- Input rekam medis

## Perhitungan SPK
- Fuzzy
- TOPSIS
- Ranking prioritas

## Laporan
- Cetak laporan
- Export data

---

# 🔄 Alur Sistem

```text
Input Data Balita
        ↓
Fuzzifikasi Data
        ↓
Normalisasi Matriks
        ↓
Pembobotan Kriteria
        ↓
Perhitungan TOPSIS
        ↓
Perankingan Prioritas
        ↓
Hasil Rekomendasi
```

---

# 📈 Contoh Perhitungan

## Normalisasi Matriks

```text
Rij = Xij / √ΣX²ij
```

---

## Solusi Ideal Positif

```text
A+ = {max(Yij)}
```

---

## Solusi Ideal Negatif

```text
A- = {min(Yij)}
```

---

## Nilai Preferensi

```text
Vi = D- / (D+ + D-)
```

---

# 📌 Kelebihan Sistem

✅ Mudah digunakan  
✅ Interface sederhana  
✅ Perhitungan otomatis  
✅ Responsive  
✅ Mempermudah pengambilan keputusan  
✅ Mengurangi human error  

---

# 👨‍💻 Developer

### Mahasiswa Sistem Informasi
### Universitas Negeri Semarang (UNNES)

---

# 📄 Lisensi

Project ini dibuat untuk:
- Kebutuhan akademik
- Pembelajaran
- Penelitian
- Implementasi Sistem Pendukung Keputusan

---

# 🤝 Kontribusi

Jika ingin berkontribusi:

1. Fork repository
2. Buat branch baru
3. Commit perubahan
4. Push branch
5. Create Pull Request

---

# ⭐ Dukungan

Jika project ini membantu, jangan lupa:

⭐ Star repository  
🍴 Fork project  
🛠 Support development  

---

# 📬 Kontak

Jika ada pertanyaan atau masukan:

📧 Email: annisayusri59@gmail.com  
🌐 Github: https://github.com/yuzhuruuu

---

<p align="center">
  <b>SPK Penanganan Stunting - Fuzzy TOPSIS</b><br>
  Built with ❤️ using PHP Native & MySQL
</p>
