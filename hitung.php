<?php
include 'koneksi.php';
session_start();
ini_set('memory_limit', '512M');
set_time_limit(300);

$w1 = isset($_POST['w_umur']) ? intval($_POST['w_umur']) : 2;
$w2 = isset($_POST['w_bblahir']) ? intval($_POST['w_bblahir']) : 3;
$w3 = isset($_POST['w_tinggi']) ? intval($_POST['w_tinggi']) : 5;
$w4 = isset($_POST['w_asi']) ? intval($_POST['w_asi']) : 2;

$bobot = [$w1, $w2, $w3, $w4]; 

$query = "SELECT * FROM balita_simulasi";
$result = mysqli_query($koneksi, $query);
$dataset = [];
while ($row = mysqli_fetch_assoc($result)) { $dataset[] = $row; }

if (empty($dataset)) {
    $query = "SELECT * FROM balita";
    $result = mysqli_query($koneksi, $query);
    $dataset = [];
    while ($row = mysqli_fetch_assoc($result)) { $dataset[] = $row; }
}

if (empty($dataset)) {
    unset($_SESSION['hasil_spk']);
    echo "<script>alert('Tidak ada data untuk proses SPK. Tambahkan data simulasi atau pastikan dataset utama tersedia.'); window.location='index.php';</script>";
    exit();
}

function hitungFuzzyTinggi($age, $body_length) {
    $tinggi_ideal_per_bulan = 50 + ($age * 0.7); 
    $selisih = $body_length - $tinggi_ideal_per_bulan;
    if ($selisih < -5) return 0.1;
    elseif ($selisih <= 2) return 0.6;
    return 1.0;
}

$pembagi = [0, 0, 0, 0]; 
$matriks_x = [];

foreach ($dataset as $data) {
    $c4_val = ($data['breastfeeding'] == 'No') ? 5 : 1; 
    $c3_fuzzy = hitungFuzzyTinggi($data['age'], $data['body_length']);

    $c1 = $data['age'];
    $c2 = $data['birth_weight'];
    $c3 = $c3_fuzzy;
    $c4 = $c4_val;

    $matriks_x[] = [
            'id' => $data['id'], 'gender' => $data['gender'], 'age' => $data['age'],
            'body_length' => $data['body_length'], 'body_weight' => $data['body_weight'], 'breastfeeding' => $data['breastfeeding'], 'stunting_label' => $data['stunting_label'],
            'kriteria' => [$c1, $c2, $c3, $c4]
        ];

    $pembagi[0] += pow($c1, 2); $pembagi[1] += pow($c2, 2); $pembagi[2] += pow($c3, 2); $pembagi[3] += pow($c4, 2);
}

$pembagi = [sqrt($pembagi[0]), sqrt($pembagi[1]), sqrt($pembagi[2]), sqrt($pembagi[3])];

$matriks_y = [];
foreach ($matriks_x as $x) {
    $y1 = ($x['kriteria'][0] / $pembagi[0]) * $bobot[0];
    $y2 = ($x['kriteria'][1] / $pembagi[1]) * $bobot[1];
    $y3 = ($x['kriteria'][2] / $pembagi[2]) * $bobot[2];
    $y4 = ($x['kriteria'][3] / $pembagi[3]) * $bobot[3];

    $matriks_y[] = [
            'id' => $x['id'], 'gender' => $x['gender'], 'age' => $x['age'],
            'body_length' => $x['body_length'], 'body_weight' => $x['body_weight'], 'breastfeeding' => $x['breastfeeding'], 'stunting_label' => $x['stunting_label'],
            'y' => [$y1, $y2, $y3, $y4]
        ];
}

$a_positif = [
    min(array_column(array_column($matriks_y, 'y'), 0)), 
    min(array_column(array_column($matriks_y, 'y'), 1)), 
    min(array_column(array_column($matriks_y, 'y'), 2)), 
    max(array_column(array_column($matriks_y, 'y'), 3))  
];

$a_negatif = [
    max(array_column(array_column($matriks_y, 'y'), 0)), 
    max(array_column(array_column($matriks_y, 'y'), 1)), 
    max(array_column(array_column($matriks_y, 'y'), 2)), 
    min(array_column(array_column($matriks_y, 'y'), 3))  
];

$hasil_ranking = [];
foreach ($matriks_y as $y) {
    $d_positif = sqrt(pow($y['y'][0]-$a_positif[0],2) + pow($y['y'][1]-$a_positif[1],2) + pow($y['y'][2]-$a_positif[2],2) + pow($y['y'][3]-$a_positif[3],2));
    $d_negatif = sqrt(pow($y['y'][0]-$a_negatif[0],2) + pow($y['y'][1]-$a_negatif[1],2) + pow($y['y'][2]-$a_negatif[2],2) + pow($y['y'][3]-$a_negatif[3],2));
    $v = ($d_positif + $d_negatif == 0) ? 0 : $d_negatif / ($d_positif + $d_negatif);

    $hasil_ranking[] = [
            'id' => $y['id'], 'gender' => $y['gender'], 'age' => $y['age'],
            'body_length' => $y['body_length'], 'body_weight' => $y['body_weight'], 'breastfeeding' => $y['breastfeeding'], 'stunting_label' => $y['stunting_label'], 'skor_v' => $v
        ];
}

usort($hasil_ranking, function ($a, $b) { return $b['skor_v'] <=> $a['skor_v']; });

session_start();
$_SESSION['hasil_spk'] = $hasil_ranking;
header("Location: hasil.php");
exit();
?>