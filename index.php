<?php 
include 'koneksi.php'; 

// PROSES INPUT DATA REKAM MEDIS BARU DARI MODAL
$success_message = '';
$error_message = '';
$rekomendasi_balita = null;

function getRekomendasiGizi($age, $body_weight, $body_length, $breastfeeding, $label) {
    $ideal_height = 50 + ($age * 0.7);
    $ideal_weight = 3 + ($age * 0.25);
    $height_gap = $body_length - $ideal_height;
    $weight_gap = $body_weight - $ideal_weight;

    $rekomendasi = [
        'status' => '',
        'headline' => '',
        'details' => '',
        'foods' => [],
        'advice' => ''
    ];

    if ($label === 'Yes' || $height_gap < -5 || $weight_gap < -2) {
        $rekomendasi['status'] = 'Stunting / Gizi Buruk';
        $rekomendasi['headline'] = 'Segera Prioritaskan Perbaikan Gizi dan Konsultasi ke Puskesmas';
        $rekomendasi['details'] = 'Balita menunjukkan tanda stunting atau defisit gizi serius berdasarkan data input. Tingkatkan asupan nutrisi tinggi protein dan energi segera.';
        $rekomendasi['foods'] = ['Telur ayam kampung', 'Ikan lokal (tuna, tongkol, bandeng)', 'Daging ayam tanpa kulit', 'Tahu dan tempe', 'Susu formula / ASI lanjutan', 'Sayur hijau (bayam, kangkung)', 'Buah lokal (pisang, pepaya)'];
        $rekomendasi['advice'] = 'Bawa balita ke Puskesmas terdekat untuk pemeriksaan gizi dan program intervensi, lalu catat perkembangan setiap 2 minggu.';
    } elseif ($height_gap < -5) {
        $rekomendasi['status'] = 'Indikasi Stunting Ringan';
        $rekomendasi['headline'] = 'Perbaiki MPASI dengan Fokus Protein Hewani';
        $rekomendasi['details'] = 'Tinggi badan berada di bawah harapan usia, walau berat badan tidak terlalu rendah. Fokus pada makanan padat nutrisi dan pemantauan rutin.';
        $rekomendasi['foods'] = ['Bubur ikan', 'Telur rebus', 'Ayam suwir', 'Tahu tempe', 'Sayur bayam', 'Buah pisang'];
        $rekomendasi['advice'] = 'Konsultasikan status pertumbuhan ke petugas gizi Puskesmas jika tidak ada perbaikan dalam 1 bulan.';
    } elseif ($weight_gap < -2) {
        $rekomendasi['status'] = 'Indikasi Gizi Kurang';
        $rekomendasi['headline'] = 'Tambahkan Asupan Energi dan Protein Setiap Hari';
        $rekomendasi['details'] = 'Berat badan lebih rendah dari target usia, meski tinggi badan relatif normal. Berikan makanan lebih sering dengan kalori seimbang.';
        $rekomendasi['foods'] = ['Susu formula / ASI lanjutan', 'Pisang', 'Daging ayam suwir', 'Tahu tempe goreng', 'Kacang hijau', 'Sayuran berdaun'];
        $rekomendasi['advice'] = 'Jadwalkan pemeriksaan gizi di Puskesmas jika belum ada peningkatan dalam 2 minggu.';
    } else {
        $rekomendasi['status'] = 'Status Gizi Baik';
        $rekomendasi['headline'] = 'Pertahankan Pola MPASI Seimbang dan Pantau Secara Rutin';
        $rekomendasi['details'] = 'Data input menunjukkan perkembangan sesuai usia. Terus dukung dengan variasi makanan gizi seimbang dan ASI/MPASI berkualitas.';
        $rekomendasi['foods'] = ['ASI atau susu lanjutan', 'Sayuran hijau', 'Buah lokal', 'Protein hewani ringan', 'Sumber karbohidrat sehat seperti nasi tim'];
        $rekomendasi['advice'] = 'Lakukan pemantauan rutin di posyandu atau Puskesmas agar tumbuh kembang tetap optimal.';
    }

    return $rekomendasi;
}

if (isset($_POST['tambah_balita'])) {
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $birth_weight = $_POST['birth_weight'];
    $birth_length = $_POST['birth_length'];
    $body_weight = $_POST['body_weight'];
    $body_length = $_POST['body_length'];
    $breastfeeding = $_POST['breastfeeding'];
    $stunting_label = $_POST['stunting_label'];

    $query_add = "INSERT INTO balita_simulasi (gender, age, birth_weight, birth_length, body_weight, body_length, breastfeeding, stunting_label) 
                  VALUES ('$gender', '$age', '$birth_weight', '$birth_length', '$body_weight', '$body_length', '$breastfeeding', '$stunting_label')";
    
    if (mysqli_query($koneksi, $query_add)) {
        $success_message = 'Data balita berhasil ditambahkan.';
        $rekomendasi_balita = getRekomendasiGizi($age, $body_weight, $body_length, $breastfeeding, $stunting_label);
    } else {
        $error_message = 'Gagal menambahkan data: ' . mysqli_error($koneksi);
    }
}

// Ambil data untuk widget statistik & Grafik
$query_total = "SELECT COUNT(*) as total FROM balita";
$res_total = mysqli_query($koneksi, $query_total);
$data_total = mysqli_fetch_assoc($res_total);

$query_stunted = "SELECT COUNT(*) as total FROM balita WHERE stunting_label = 'Yes'";
$res_stunted = mysqli_query($koneksi, $query_stunted);
$data_stunted = mysqli_fetch_assoc($res_stunted);

$total_balita = $data_total['total'];
$total_stunted = $data_stunted['total'];
$total_normal = $total_balita - $total_stunted;
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Keputusan Stunting - Master Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        [data-bs-theme="light"] {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --gradient-banner: linear-gradient(135deg, #0284c7 0%, #0ea5e9 100%);
            --table-th-bg: #f1f5f9;
            --table-th-color: #0f172a;
        }

        [data-bs-theme="dark"] {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --gradient-banner: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            --table-th-bg: #334155;
            --table-th-color: #38bdf8;
        }

        /* Trik Agar Scroll Bergeser Halus */
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 90px; /* Jarak penahan agar kontainer tidak tertutup header melayang */
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: 'Segoe UI', Roboto, sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        /* BARU: Header Melayang Tetap di Atas (Sticky Top) */
        .navbar-sticky {
            position: sticky;
            top: 0;
            z-index: 1020;
            background-color: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(8px);
            background-color: rgba(var(--bg-card), 0.9);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .card-custom { 
            background-color: var(--bg-card); 
            border: 1px solid var(--border-color); 
            border-radius: 16px; 
        }

        .banner-gradient {
            background: var(--gradient-banner);
            border: none;
            border-radius: 16px;
            margin-top: 2rem;
        }
        .table-custom th {
            background-color: var(--table-th-bg) !important;
            color: var(--table-th-color) !important;
            border-color: var(--border-color) !important;
            font-weight: 600;
        }
        .table-custom td {
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }
        .theme-switch-btn {
            cursor: pointer;
            padding: 6px 14px;
            border-radius: 50px;
            border: 1px solid var(--border-color);
            background-color: var(--bg-body);
            color: var(--text-main);
            font-weight: 500;
            font-size: 0.85rem;
        }
        .nav-link-custom {
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        .nav-link-custom:hover, .nav-link-custom:focus {
            color: #0284c7;
        }
        .footer-custom {
            background-color: var(--bg-card);
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-sticky py-2">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="#">
            <i class="fa-solid fa-layer-group me-2"></i> SPK <span class="text-secondary fw-normal">Stunting</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto gap-2">
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#section-data"><i class="fa-solid fa-server me-1"></i> Data Medis</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#section-bobot"><i class="fa-solid fa-sliders me-1"></i> Simulasi Bobot</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#section-grafik"><i class="fa-solid fa-chart-pie me-1"></i> Analisis Grafik</a></li>
                <li class="nav-item"><a class="nav-link nav-link-custom" href="#section-kalkulator"><i class="fa-solid fa-heart-pulse me-1"></i> Kalkulator Skrining</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <button class="theme-switch-btn shadow-sm" id="themeToggler" onclick="toggleTheme()">
                    <i class="fa-solid fa-moon me-1"></i> Gelap
                </button>
            </div>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="card banner-gradient mb-5 p-5 text-center text-white shadow-sm">
        <h1 class="fw-bold mb-2" style="letter-spacing: -1px;">Sistem Pendukung Keputusan Penanganan Stunting</h1>
        <p class="mb-0 fs-5 opacity-90 fw-light">Optimasi Skala Prioritas Menggunakan Algoritma <span class="fw-bold">Fuzzy-TOPSIS</span></p>
    </div>

    <?php if (!empty($success_message) || !empty($error_message)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert <?= !empty($success_message) ? 'alert-success' : 'alert-danger'; ?> rounded-3 shadow-sm" role="alert">
                <?= !empty($success_message) ? htmlspecialchars($success_message) : htmlspecialchars($error_message); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($rekomendasi_balita)): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-custom shadow-sm border-start border-4 border-warning p-4">
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="badge bg-warning text-dark rounded-pill py-2 px-3 fw-semibold">Rekomendasi Gizi Otomatis</div>
                    <div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($rekomendasi_balita['status']); ?></h5>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($rekomendasi_balita['details']); ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <div class="bg-white rounded-3 p-3 shadow-sm">
                            <h6 class="fw-semibold mb-2">Daftar Bahan Makanan Lokal Tinggi Protein</h6>
                            <ul class="mb-2">
                                <?php foreach ($rekomendasi_balita['foods'] as $food): ?>
                                    <li><?= htmlspecialchars($food); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <div class="bg-light rounded-3 p-3 h-100">
                            <h6 class="fw-semibold mb-2">Tindakan Rekomendasi</h6>
                            <p class="mb-3"><?= htmlspecialchars($rekomendasi_balita['advice']); ?></p>
                            <div class="badge bg-danger text-white px-3 py-2">Konsultasi ke Puskesmas terdekat</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row mb-5">
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 shadow-sm d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <h6 class="text-uppercase small mb-1" style="color: var(--text-muted); font-weight: 600;">Total Balita</h6>
                    <h2 class="fw-bold mb-0"><?= number_format($data_total['total']); ?></h2>
                </div>
                <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-3">
                    <i class="fa-solid fa-folder-open fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card card-custom p-3 shadow-sm d-flex flex-row align-items-center justify-content-between h-100">
                <div>
                    <h6 class="text-uppercase small mb-1" style="color: var(--text-muted); font-weight: 600;">Kasus Stunting</h6>
                    <h2 class="fw-bold mb-0 text-danger"><?= number_format($data_stunted['total']); ?></h2>
                </div>
                <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-3">
                    <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 d-grid">
            <button class="btn btn-outline-primary fw-bold rounded-3 d-flex align-items-center justify-content-center border-2" data-bs-toggle="modal" data-bs-target="#modalTambahData">
                <i class="fa-solid fa-plus me-2"></i> Input Rekam Medis
            </button>
        </div>
        <div class="col-md-3 mb-3 d-grid">
            <a href="reset_simulasi.php" class="btn btn-outline-danger fw-bold rounded-3 border-2 d-flex align-items-center justify-content-center" onclick="return confirm('Apakah kamu yakin ingin menghapus semua data di tabel simulasi?')">
                <i class="fa-solid fa-trash-can me-2"></i> Bersihkan Data Simulasi
            </a>
        </div>
        <div class="col-md-3 mb-3 d-grid">
            <button type="submit" form="form-hitung-engine" class="btn btn-primary fw-bold shadow-sm rounded-3 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); border:none;">
                <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Jalankan Mesin SPK
            </button>
        </div>
    </div>

    <form id="form-hitung-engine" action="hitung.php" method="POST">

        <div id="section-data" class="card card-custom shadow-sm mb-5">
            <div class="card-header bg-transparent py-4 border-0">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-server text-muted me-2"></i> Repositori Data Rekam Medis</h5>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="table-responsive">
                    <table id="tabelBalita" class="table table-custom table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gender</th>
                                <th>Umur (Bln)</th>
                                <th>BB Lahir (kg)</th>
                                <th>PB Lahir (cm)</th>
                                <th>BB Skrg (kg)</th>
                                <th>TB Skrg (cm)</th>
                                <th>ASI</th>
                                <th>Label Asli</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $query_all = "SELECT * FROM balita ORDER BY id DESC LIMIT 2000"; 
                            $res_all = mysqli_query($koneksi, $query_all);
                            $no = 1;
                            while($row = mysqli_fetch_assoc($res_all)) { 
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $row['gender']; ?></td>
                                <td><?= $row['age']; ?></td>
                                <td><?= $row['birth_weight']; ?></td>
                                <td><?= $row['birth_length']; ?></td>
                                <td><?= $row['body_weight']; ?></td>
                                <td><?= $row['body_length']; ?></td>
                                <td><?= $row['breastfeeding']; ?></td>
                                <td>
                                    <span class="badge <?= $row['stunting_label'] == 'Yes' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success'; ?> px-3 py-2 rounded-2 fw-semibold">
                                        <?= $row['stunting_label'] == 'Yes' ? 'Stunted' : 'Normal'; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="section-bobot" class="card card-custom shadow-sm mb-5" style="border-top: 4px solid #f59e0b;">
            <div class="card-header bg-transparent py-4 border-0">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-sliders text-warning me-2"></i> Modul Pengaturan & Simulasi Bobot Kriteria SPK</h5>
                <p class="text-muted small mb-0 mt-1">Sesuaikan nilai prioritas kebijakan analisis medis (Skala Nilai 1 - 5)</p>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">C1: Kriteria Umur Anak</label>
                        <select class="form-select" name="w_umur">
                            <option value="1">1 (Sangat Rendah)</option>
                            <option value="2" selected>2 (Rendah / Default)</option>
                            <option value="3">3 (Cukup)</option>
                            <option value="4">4 (Tinggi)</option>
                            <option value="5">5 (Sangat Kritis)</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">C2: Kriteria Berat Lahir</label>
                        <select class="form-select" name="w_bblahir">
                            <option value="1">1 (Sangat Rendah)</option>
                            <option value="2">2 (Rendah)</option>
                            <option value="3" selected>3 (Cukup / Default)</option>
                            <option value="4">4 (Tinggi)</option>
                            <option value="5">5 (Sangat Kritis)</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">C3: Kriteria Tinggi Badan (Fuzzy)</label>
                        <select class="form-select" name="w_tinggi">
                            <option value="1">1 (Sangat Rendah)</option>
                            <option value="2">2 (Rendah)</option>
                            <option value="3">3 (Cukup)</option>
                            <option value="4">4 (Tinggi)</option>
                            <option value="5" selected>5 (Sangat Kritis / Default)</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">C4: Kriteria Riwayat ASI</label>
                        <select class="form-select" name="w_asi">
                            <option value="1">1 (Sangat Rendah)</option>
                            <option value="2" selected>2 (Rendah / Default)</option>
                            <option value="3">3 (Cukup)</option>
                            <option value="4">4 (Tinggi)</option>
                            <option value="5">5 (Sangat Kritis)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </form> <div id="section-grafik" class="row mb-5 justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom shadow-sm p-4 text-center">
                <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-pie text-primary me-2"></i> Proporsi Distribusi Gizi Balita</h5>
                <div style="position: relative; height:250px; width:100%">
                    <canvas id="chartGizi"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div id="section-kalkulator" class="card card-custom shadow-sm mb-5" style="border-top: 4px solid #0ea5e9;">
        <div class="card-header bg-transparent py-4 border-0">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-heart-pulse text-danger me-2"></i> Kalkulator Skrining Komprehensif Status Gizi</h5>
            <p class="text-muted small mb-0 mt-1">Uji dini status indikasi pertumbuhan menggunakan kombinasi variabel antropometri anak</p>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Umur Balita (Bulan)</label>
                    <input type="number" id="cek_umur" class="form-control" placeholder="Contoh: 18">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tinggi Badan Sekarang (cm)</label>
                    <input type="number" step="0.1" id="cek_tinggi" class="form-control" placeholder="Contoh: 75.4">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Berat Badan Sekarang (kg)</label>
                    <input type="number" step="0.1" id="cek_berat" class="form-control" placeholder="Contoh: 8.2">
                </div>
                <div class="col-md-3 mb-3 d-grid align-items-end">
                    <button onclick="prosesSkriningFuzzy()" class="btn btn-outline-primary fw-bold py-2 rounded-3">
                        <i class="fa-solid fa-calculator me-1"></i> Analisis Status Gizi
                    </button>
                </div>
            </div>

            <div id="hasil_skrining_box" class="mt-4 d-none">
                <div class="p-4 rounded-3 text-center border bg-light" id="skrining_alert_wrapper">
                    <h5 class="fw-bold mb-1" id="skrining_title">-</h5>
                    <p class="mb-0 id_desc" id="skrining_desc">-</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahData" tabindex="-1" aria-labelledby="modalTambahDataLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-custom p-2">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary" id="modalTambahDataLabel"><i class="fa-solid fa-file-medical me-2"></i> Tambah Rekam Medis Balita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select class="form-select" name="gender" required>
                                <option value="Male">Laki-laki (Male)</option>
                                <option value="Female">Perempuan (Female)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Umur (Bulan)</label>
                            <input type="number" class="form-control" name="age" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Berat Lahir (kg)</label>
                            <input type="number" step="0.1" class="form-control" name="birth_weight" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Panjang Lahir (cm)</label>
                            <input type="number" class="form-control" name="birth_length" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Berat Sekarang (kg)</label>
                            <input type="number" step="0.1" class="form-control" name="body_weight" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tinggi Sekarang (cm)</label>
                            <input type="number" step="0.1" class="form-control" name="body_length" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ASI Eksklusif</label>
                            <select class="form-select" name="breastfeeding" required>
                                <option value="Yes">Ya (Yes)</option>
                                <option value="No">Tidak (No)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Diagnosa Riwayat Asli</label>
                            <select class="form-select" name="stunting_label" required>
                                <option value="No">Normal (No Stunting)</option>
                                <option value="Yes">Stunted (Ada Stunting)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_balita" class="btn btn-primary px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<footer class="footer-custom py-4 mt-5">
    <div class="container text-center">
        <p class="mb-1 fw-medium" style="letter-spacing: 0.3px;">&copy; 2026 SPK-11.</p>
        <small class="opacity-75">Sistem Informasi &bull; Universitas Negeri Semarang (UNNES)</small>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    $('#tabelBalita').DataTable({
        "pageLength": 10,
        "dom": "<'row mb-3'<'col-md-6'l><'col-md-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
        "language": {
            "search": "Cari Balita:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ balita",
            "paginate": { "next": "→", "previous": "←" }
        }
    });

    const ctx = document.getElementById('chartGizi').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Normal', 'Stunted (Stunting)'],
            datasets: [{
                data: [<?= $total_normal; ?>, <?= $total_stunted; ?>],
                backgroundColor: ['rgba(16, 185, 129, 0.75)', 'rgba(239, 68, 68, 0.75)'],
                borderColor: ['#10b981', '#ef4444'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#cbd5e1' : '#1e293b',
                        font: { family: 'Segoe UI', size: 13 }
                    }
                }
            },
            cutout: '70%'
        }
    });
});

function prosesSkriningFuzzy() {
    const age = parseFloat($('#cek_umur').val());
    const body_length = parseFloat($('#cek_tinggi').val());
    const body_weight = parseFloat($('#cek_berat').val());
    
    if (isNaN(age) || isNaN(body_length) || isNaN(body_weight)) {
        alert('Mohon lengkapi data Umur, Tinggi Badan, dan Berat Badan!');
        return;
    }

    const tinggi_ideal = 50 + (age * 0.7);
    const berat_ideal = 3 + (age * 0.25);
    const selisih_tinggi = body_length - tinggi_ideal;
    const selisih_berat = body_weight - berat_ideal;
    
    const resultBox = document.getElementById('hasil_skrining_box');
    const wrapper = document.getElementById('skrining_alert_wrapper');
    const title = document.getElementById('skrining_title');
    const desc = document.getElementById('skrining_desc');

    resultBox.classList.remove('d-none');

    if (selisih_tinggi < -5 && selisih_berat < -2) {
        wrapper.className = "p-4 rounded-3 text-center border border-danger bg-danger bg-opacity-10 text-danger shadow-sm";
        title.innerHTML = "PRIORITAS KRITIS: Terindikasi Stunting & Gizi Buruk";
        desc.innerHTML = `Balita berumur ${age} bulan mengalami keterlambatan pertumbuhan tinggi (${body_length} cm) sekaligus defisit berat badan berat (${body_weight} kg). Anak ini wajib masuk daftar prioritas teratas intervensi gizi sistem SPK.`;
    } else if (selisih_tinggi < -5 && selisih_berat >= -2) {
        wrapper.className = "p-4 rounded-3 text-center border border-warning bg-warning bg-opacity-10 text-warning-emphasis shadow-sm";
        title.innerHTML = "⚠ Indikasi Stunting (Perawakan Pendek)";
        desc.innerHTML = `Tinggi anak berada di bawah ambang normal usia ${age} bulan, meskipun berat badannya relatif cukup. Direkomendasikan pemeriksaan lanjutan terkait stunting kronis.`;
    } else if (selisih_tinggi >= -5 && selisih_berat < -2) {
        wrapper.className = "p-4 rounded-3 text-center border border-warning bg-warning bg-opacity-10 text-warning-emphasis shadow-sm";
        title.innerHTML = "⚠ Indikasi Gizi Kurang (Kurus)";
        desc.innerHTML = `Tinggi badan anak normal, namun berat badan (${body_weight} kg) tidak mencukupi untuk standar balita usia ${age} bulan. Butuh tambahan asupan kalori dan gizi makro.`;
    } else {
        wrapper.className = "p-4 rounded-3 text-center border border-success bg-success bg-opacity-10 text-success shadow-sm";
        title.innerHTML = " Pertumbuhan Sehat (Normal & Ideal)";
        desc.innerHTML = `Luar biasa! Kombinasi tinggi badan (${body_length} cm) dan berat badan (${body_weight} kg) balita tumbuh sangat proporsional sesuai grafik usia ${age} bulan.`;
    }
}

function toggleTheme() {
    const htmlEl = document.documentElement;
    const currentTheme = htmlEl.getAttribute('data-bs-theme');
    const btn = document.getElementById('themeToggler');
    
    if (currentTheme === 'light') {
        htmlEl.setAttribute('data-bs-theme', 'dark');
        btn.innerHTML = '<i class="fa-solid fa-sun me-1"></i> Terang';
    } else {
        htmlEl.setAttribute('data-bs-theme', 'light');
        btn.innerHTML = '<i class="fa-solid fa-moon me-1"></i> Gelap';
    }
}
</script>
</body>
</html>