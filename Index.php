<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "fuzzy_db";
$conn = new mysqli($host, $user, $pass, $db);

function fuzzy_tulis($input) {
    return [
        'lulus' => max(0, min(1, ($input - 25) / 50)),
        'tidak_lulus' => max(0, min(1, (75 - $input) / 50))
    ];
}
function fuzzy_keterampilan($input) {
    return fuzzy_tulis($input);
}
function fuzzy_wawancara($input) {
    return [
        'lulus' => max(0, min(1, ($input - 55) / 20)),
        'tidak_lulus' => max(0, min(1, (75 - $input) / 20))
    ];
}
function fuzzy_kesehatan($input) {
    return [
        'lulus' => max(0, min(1, ($input - 50) / 20)),
        'tidak_lulus' => max(0, min(1, (70 - $input) / 20))
    ];
}

function get_rank_average($array, $key) {
    $values = array_column($array, $key);
    arsort($values);
    $ranks = [];
    $rank = 1;
    $prev_value = null;
    $same_rank = [];
    foreach ($values as $index => $value) {
        if ($value === $prev_value) {
            $same_rank[] = $index;
        } else {
            if (count($same_rank) > 0) {
                $avg = array_sum(range($rank - count($same_rank), $rank - 1)) / count($same_rank);
                foreach ($same_rank as $i) {
                    $ranks[$i] = $avg;
                }
                $same_rank = [];
            }
            $same_rank[] = $index;
            $prev_value = $value;
        }
        $rank++;
    }
    if (count($same_rank) > 0) {
        $avg = array_sum(range($rank - count($same_rank), $rank - 1)) / count($same_rank);
        foreach ($same_rank as $i) {
            $ranks[$i] = $avg;
        }
    }
    ksort($ranks);
    return array_values($ranks);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $tulis = $_POST['tulis'];
    $keterampilan = $_POST['keterampilan'];
    $wawancara = $_POST['wawancara'];
    $kesehatan = $_POST['kesehatan'];

    $alur1 = ($tulis + $keterampilan) / 2;
    $nilai_pakar = round(($wawancara + $kesehatan + $alur1) / 3);

    $fuzzy_tulis = fuzzy_tulis($tulis);
    $fuzzy_keterampilan = fuzzy_keterampilan($keterampilan);
    $fuzzy_wawancara = fuzzy_wawancara($wawancara);
    $fuzzy_kesehatan = fuzzy_kesehatan($kesehatan);

    $rules = [
        [['lulus','lulus','lulus','lulus'], 'lulus'],
        [['lulus','lulus','lulus','tidak_lulus'], 'tidak_lulus'],
        [['lulus','lulus','tidak_lulus','lulus'], 'tidak_lulus'],
        [['lulus','lulus','tidak_lulus','tidak_lulus'], 'tidak_lulus'],
        [['lulus','tidak_lulus','lulus','lulus'], 'lulus'],
        [['lulus','tidak_lulus','lulus','tidak_lulus'], 'tidak_lulus'],
        [['lulus','tidak_lulus','tidak_lulus','lulus'], 'tidak_lulus'],
        [['lulus','tidak_lulus','tidak_lulus','tidak_lulus'], 'tidak_lulus'],
        [['tidak_lulus','lulus','lulus','lulus'], 'lulus'],
        [['tidak_lulus','lulus','lulus','tidak_lulus'], 'tidak_lulus'],
        [['tidak_lulus','lulus','tidak_lulus','lulus'], 'tidak_lulus'],
        [['tidak_lulus','lulus','tidak_lulus','tidak_lulus'], 'tidak_lulus'],
        [['tidak_lulus','tidak_lulus','lulus','lulus'], 'lulus'],
        [['tidak_lulus','tidak_lulus','lulus','tidak_lulus'], 'tidak_lulus'],
        [['tidak_lulus','tidak_lulus','tidak_lulus','lulus'], 'tidak_lulus'],
        [['tidak_lulus','tidak_lulus','tidak_lulus','tidak_lulus'], 'tidak_lulus']
    ];

    $sum_alpha = 0;
    $sum_alpha_z = 0;

    foreach ($rules as [$conditions, $output]) {
        $alpha = min(
            $fuzzy_tulis[$conditions[0]],
            $fuzzy_keterampilan[$conditions[1]],
            $fuzzy_wawancara[$conditions[2]],
            $fuzzy_kesehatan[$conditions[3]]
        );
        $z = $output === 'lulus' ? ($alpha * 50 + 25) : (75 - $alpha * 50);
        $sum_alpha += $alpha;
        $sum_alpha_z += $alpha * $z;
    }

    $nilai_sistem = round(($sum_alpha == 0) ? 0 : $sum_alpha_z / $sum_alpha);

    $stmt = $conn->prepare("INSERT INTO fuzzifikasi (nama, test_tulis, test_keterampilan, wawancara, test_kesehatan, nilai_pakar, hasil_sistem, tanggal_input) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sdddddd", $nama, $tulis, $keterampilan, $wawancara, $kesehatan, $nilai_pakar, $nilai_sistem);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$result = $conn->query("SELECT * FROM fuzzifikasi ORDER BY tanggal_input ASC");
$data = $result->fetch_all(MYSQLI_ASSOC);

$rank_pakar = get_rank_average($data, 'nilai_pakar');
$rank_sistem = get_rank_average($data, 'hasil_sistem');

foreach ($data as $i => &$row) {
    $row['rank_pakar'] = $rank_pakar[$i];
    $row['rank_sistem'] = $rank_sistem[$i];
    $row['di'] = $row['rank_pakar'] - $row['rank_sistem'];
    $row['di2'] = pow($row['di'], 2);
}

$jumlah_data = count($data);
$sum_d2 = array_sum(array_column($data, 'di2'));
$r1 = 6 * $sum_d2;
$r2 = $jumlah_data * ($jumlah_data ** 2 - 1);
$spearman = ($r2 == 0) ? 0 : 1 - ($r1 / $r2);
?>

<html>
<head>
    <title>Sistem Fuzzy Seleksi Karyawan</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; background: #fff; margin-bottom: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #4CAF50; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        form { background: #fff; padding: 20px; border: 1px solid #ccc; margin-bottom: 30px; }
        input[type="text"], input[type="number"] { padding: 6px; width: 200px; margin-bottom: 10px; }
        input[type="submit"] { background: #4CAF50; color: white; border: none; padding: 10px 15px; cursor: pointer; }
    </style>
</head>
<body>

<h2>Form Input Seleksi</h2>
<form method="post">
    Nama: <input type="text" name="nama" required><br>
    Tes Tulis: <input type="number" name="tulis" required><br>
    Tes Keterampilan: <input type="number" name="keterampilan" required><br>
    Wawancara: <input type="number" name="wawancara" required><br>
    Tes Kesehatan: <input type="number" name="kesehatan" required><br>
    <input type="submit" name="submit" value="Proses">
</form>

<h2>Tabel Hasil Ranking dan Korelasi Spearman</h2>
<table>
<tr>
    <th>Nama</th><th>Pakar</th><th>Sistem</th><th>Rank Pakar</th><th>Rank Sistem</th><th>d</th><th>d²</th>
</tr>
<?php foreach ($data as $d): ?>
<tr>
    <td><?= $d['nama'] ?></td>
    <td><?= round($d['nilai_pakar'], 2) ?></td>
    <td><?= round($d['hasil_sistem'], 2) ?></td>
    <td><?= $d['rank_pakar'] ?></td>
    <td><?= $d['rank_sistem'] ?></td>
    <td><?= $d['di'] ?></td>
    <td><?= $d['di2'] ?></td>
</tr>
<?php endforeach; ?>
</table>
<p><strong>∑d² = <?= $sum_d2 ?></strong></p>
<p><strong>Spearman ρ = <?= round($spearman, 4) ?></strong></p>

<h2>Riwayat Input Data</h2>
<table>
<tr>
    <th>Nama</th><th>Tulis</th><th>Keterampilan</th><th>Wawancara</th><th>Kesehatan</th><th>Pakar</th><th>Sistem</th><th>Tanggal</th>
</tr>
<?php foreach ($data as $d): ?>
<tr>
    <td><?= $d['nama'] ?></td>
    <td><?= $d['test_tulis'] ?></td>
    <td><?= $d['test_keterampilan'] ?></td>
    <td><?= $d['wawancara'] ?></td>
    <td><?= $d['test_kesehatan'] ?></td>
    <td><?= $d['nilai_pakar'] ?></td>
    <td><?= $d['hasil_sistem'] ?></td>
    <td><?= $d['tanggal_input'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>
