<?php
session_start();
if (!isset($_SESSION['hasil_spk'])) { header("Location: index.php"); exit(); }
$hasil_ranking = $_SESSION['hasil_spk'];
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Prioritas SPK - Premium Master</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Desain Variabel Warna untuk Konsistensi Dual Mode */
        [data-bs-theme="light"] {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --table-th-bg: #f1f5f9;
            --table-th-color: #0f172a;
        }

        [data-bs-theme="dark"] {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f1f5f9;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --table-th-bg: #334155;
            --table-th-color: #38bdf8;
        }

        body { 
            background-color: var(--bg-body); 
            color: var(--text-main); 
            font-family: 'Segoe UI', system-ui, sans-serif; 
            transition: background-color 0.3s, color 0.3s;
        }
        
        /* Navbar Melayang (Sticky Top) - Persis seperti Halaman Utama */
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

        .footer-custom {
            background-color: var(--bg-card);
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        /* Desain Kustom Tombol Ekspor DataTables agar Elegan Minimalis */
        .dt-buttons .btn { 
            border-radius: 8px !important; 
            margin-right: 5px; 
            font-weight: 500; 
            font-size: 0.85rem;
            padding: 6px 14px;
        }

        /* Optimasi Otomatis Cetak Kertas HVS Putih Teks Hitam */
        @media print {
            body { background-color: #fff !important; color: #000 !important; }
            .navbar-sticky, .btn, .alert, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dt-buttons { display: none !important; }
            .card-custom { border: none !important; box-shadow: none !important; }
            .table-custom th, .table-custom td { border: 1px solid #000 !important; color: #000 !important; background: transparent !important; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-sticky py-2">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="index.php">
            <i class="fa-solid fa-heart-pulse text-info me-2"></i> SPK <span class="text-secondary fw-normal">Stunting</span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <button class="theme-switch-btn shadow-sm" id="themeToggler" onclick="toggleTheme()">
                <i class="fa-solid fa-moon me-1"></i> Gelap
            </button>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="card card-custom mb-4 p-4 shadow-sm" style="border-left: 6px solid #10b981;">
        <h2 class="fw-bold mb-1"><i class="fa-solid fa-ranking-star text-warning me-2"></i> Rekomendasi Urutan Prioritas Intervensi</h2>
        <p class="text-muted mb-0">Hasil pengurutan alternatif balita kritis yang paling mendesak memerlukan intervensi gizi segera (Fuzzy-TOPSIS Engine)</p>
    </div>

    <div class="mb-4 text-start">
        <a href="index.php" class="btn btn-light border fw-medium px-4 rounded-3 shadow-sm"><i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Dashboard</a>
    </div>

    <div class="card card-custom shadow-sm mb-5">
        <div class="card-body px-4 pb-4">
            <div class="alert alert-light border mb-4 rounded-3" style="color: var(--text-muted); font-size: 0.95rem;">
                <i class="fa-solid fa-circle-info text-primary me-2"></i> <strong>Sistem Keputusan Medis:</strong> Skor Preferensi (V) tertinggi menandakan tingkat kemiripan paling dekat dengan kondisi solusi krisis. Baris diurutkan otomatis dari kasus terparah.
            </div>

            <div class="table-responsive">
                <table id="tabelHasil" class="table table-custom table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>Peringkat</th>
                            <th>ID Balita</th>
                            <th>Umur</th>
                            <th>TB (cm)</th>
                            <th>Label Asli</th>
                            <th>Skor (V)</th>
                            <th>Rekomendasi Intervensi Medis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        foreach ($hasil_ranking as $rank) { 
                            // PERBAIKAN LOGIKA: Sinkronisasi Skor V dengan Label Status Validasi Asli
                            if ($rank['stunting_label'] == 'Yes') {
                                if ($rank['skor_v'] >= 0.50) {
                                    $rekomendasi = '<span class="text-danger fw-bold"><i class="fa-solid fa-circle-radiation me-1"></i> Intervensi Darurat (PMT Pokok & Nutrisi Klinis)</span>';
                                } else {
                                    $rekomendasi = '<span class="text-warning fw-medium"><i class="fa-solid fa-triangle-exclamation me-1"></i> Prioritas Sedang (Edukasi Gizi & Susu Tambahan)</span>';
                                }
                            } else {
                                // Jika dari data rekam medis aslinya Normal, rekomendasinya harus aman
                                $rekomendasi = '<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i> Pemantauan Rutin (Kondisi Tumbuh Normal)</span>';
                            }
                        ?>
                        <tr>
                            <td>
                                <?php if($no <= 10 && $rank['stunting_label'] == 'Yes'): ?>
                                    <span class="badge bg-danger text-white px-2 py-2 rounded-2 fw-bold">Prioritas Teratas</span>
                                    <?php $no++; ?>
                                <?php else: ?>
                                    <span class="ps-2 text-muted small">Peringkat <?= $no++; ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-secondary">#<?= $rank['id']; ?></td>
                            <td><?= $rank['age']; ?> Bln</td>
                            <td><?= $rank['body_length']; ?> cm</td>
                            <td>
                                <span class="badge <?= $rank['stunting_label'] == 'Yes' ? 'bg-danger bg-opacity-10 text-danger border border-danger' : 'bg-success bg-opacity-10 text-success border border-success'; ?> px-2 py-1.5">
                                    <?= $rank['stunting_label'] == 'Yes' ? '⚠ Stunted' : 'Normal'; ?>
                                </span>
                            </td>
                            <td class="fw-bold text-primary"><?= number_format($rank['skor_v'], 4); ?></td>
                            <td><?= $rekomendasi; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<footer class="footer-custom py-4 text-center">
    <p class="mb-1 fw-medium">&copy; 2026 Tim Proyek Tugas Akhir SPK Stunting.</p>
    <small class="opacity-75">Sistem Informasi &bull; Universitas Negeri Semarang (UNNES)</small>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabelHasil').DataTable({
        "pageLength": 10,
        "order": [],
        "dom": "<'row mb-3'<'col-md-6'B><'col-md-6'f>>" + "<'row'<'col-sm-12'tr>>" + "<'row mt-3'<'col-md-5'i><'col-md-7'p>>",
        "buttons": [
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm text-white', text: '<i class="fa-solid fa-file-excel me-1"></i> Excel' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm text-white', text: '<i class="fa-solid fa-file-pdf me-1"></i> PDF' },
            { extend: 'print', className: 'btn btn-dark btn-sm text-white', text: '<i class="fa-solid fa-print me-1"></i> Cetak Dokumen' }
        ],
        "language": { 
            "search": "Filter Hasil Peringkat:", 
            "lengthMenu": "Tampilkan _MENU_ data",
            "info": "Menampilkan peringkat _START_ sampai _END_ dari _TOTAL_ alternatif balita",
            "paginate": { "next": "→", "previous": "←" }
        }
    });
});

// Fungsi Switch Tema Gelap / Terang yang Sinkron dengan Halaman Utama
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