<?php
session_start();
if (!isset($_SESSION['hasil_spk'])) { header("Location: index.php"); exit(); }
$hasil_ranking = $_SESSION['hasil_spk'];

function getRekomendasiGizi($age, $body_weight, $body_length, $breastfeeding, $label) {
    $ideal_height = 50 + ($age * 0.7);
    $ideal_weight = 3 + ($age * 0.25);
    $height_gap = $body_length - $ideal_height;
    $weight_gap = $body_weight - $ideal_weight;

    $rekomendasi = ['status' => '', 'headline' => '', 'details' => '', 'foods' => [], 'advice' => ''];

    if ($label === 'Yes' || $height_gap < -5 || $weight_gap < -2) {
        $rekomendasi['status'] = 'Stunting / Gizi Buruk';
        $rekomendasi['details'] = 'Balita menunjukkan tanda stunting atau defisit gizi serius berdasarkan data input. Tingkatkan asupan nutrisi tinggi protein dan energi segera.';
        $rekomendasi['foods'] = ['Telur ayam kampung', 'Ikan lokal (tuna, tongkol)', 'Daging ayam tanpa kulit', 'Tahu dan tempe', 'Sayur hijau (bayam)'];
        $rekomendasi['advice'] = 'Bawa balita ke Puskesmas terdekat untuk pemeriksaan gizi dan program intervensi segera.';
    } elseif ($weight_gap < -2) {
        $rekomendasi['status'] = 'Indikasi Gizi Kurang';
        $rekomendasi['details'] = 'Berat badan lebih rendah dari target usia. Berikan makanan lebih sering dengan kalori seimbang.';
        $rekomendasi['foods'] = ['Susu formula / ASI lanjutan', 'Pisang', 'Daging ayam suwir', 'Kacang hijau'];
        $rekomendasi['advice'] = 'Jadwalkan pemeriksaan gizi di Puskesmas jika belum ada peningkatan dalam 2 minggu.';
    }
    return $rekomendasi;
}
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
                        $modals_html = ''; // Variabel untuk menampung kode popup modal gizi
                        
                        foreach ($hasil_ranking as $rank) { 
                            if ($rank['stunting_label'] == 'Yes') {
                                // Buat Tombol untuk yang stunting
                                $rekomendasi_btn = '<button type="button" class="btn btn-sm btn-warning fw-bold text-dark rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalRek' . $rank['id'] . '"><i class="fa-solid fa-utensils me-1"></i> Lihat Rekomendasi</button>';
                                
                                // Tarik perhitungan gizinya
                                $rek_data = getRekomendasiGizi($rank['age'], $rank['body_weight'], $rank['body_length'], $rank['breastfeeding'], $rank['stunting_label']);
                                
                                // Susun list makanan
                                $foods_list = '';
                                foreach ($rek_data['foods'] as $food) { $foods_list .= '<li>' . htmlspecialchars($food) . '</li>'; }

                                // Buat kerangka modal popup-nya (disimpan di memori dulu)
                                $modals_html .= '
                                <div class="modal fade" id="modalRek' . $rank['id'] . '" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content card-custom p-3">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-warning"><i class="fa-solid fa-notes-medical me-2"></i> Rekomendasi Gizi (ID Balita: #'.$rank['id'].')</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-warning border-warning border-2 rounded-3 mb-4">
                                                    <h5 class="fw-bold mb-1">'.htmlspecialchars($rek_data['status']).'</h5>
                                                    <p class="mb-0 text-dark">'.htmlspecialchars($rek_data['details']).'</p>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-7 mb-3">
                                                        <div class="bg-light rounded-3 p-3 border h-100">
                                                            <h6 class="fw-semibold mb-2">Daftar Bahan Makanan Lokal:</h6>
                                                            <ul class="mb-0">'.$foods_list.'</ul>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 mb-3">
                                                        <div class="bg-danger bg-opacity-10 rounded-3 p-3 border border-danger h-100">
                                                            <h6 class="fw-semibold text-danger mb-2">Tindakan Khusus:</h6>
                                                            <p class="mb-0 text-dark">'.htmlspecialchars($rek_data['advice']).'</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                            } else {
                                // Tampilan untuk balita normal
                                $rekomendasi_btn = '<span class="text-success fw-medium"><i class="fa-solid fa-circle-check me-1"></i> Pemantauan Rutin (Normal)</span>';
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
                            <td><?= $rekomendasi_btn; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div> <?= isset($modals_html) ? $modals_html : ''; ?>

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