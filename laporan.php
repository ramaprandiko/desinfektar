
<?php
require_once 'config/auth.php';
require_once 'config/koneksi.php';

$query = mysqli_query($conn, "
    SELECT * FROM penyemprotan
    ORDER BY created_at DESC
");

if (!$query) {
    die("Gagal mengambil data laporan: " . mysqli_error($conn));
}

$total = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Riwayat Penyemprotan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            padding: 30px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
        }

        h2, p {
            text-align: center;
        }

        .info {
            margin: 25px 0;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #cbd5e1;
            padding: 12px;
            text-align: center;
        }

        th {
            background: #0f172a;
            color: white;
        }

        .actions {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }

        button, .back {
            padding: 10px 18px;
            border: none;
            border-radius: 7px;
            background: #059669;
            color: white;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .back {
            background: #475569;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                padding: 0;
            }

            .actions {
                display: none;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="actions">
        <button onclick="window.print()">Cetak Laporan</button>
        <a href="index.php" class="back">Kembali</a>
    </div>

    <h2>LAPORAN RIWAYAT PENYEMPROTAN</h2>
    <p>Sistem Desinfektan Otomatis Berbasis Arduino</p>
    <p>PT TANDEM MAKMUR ABADI</p>

    <div class="info">
        Total Riwayat Penyemprotan: <?= $total ?> data
    </div>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Mode</th>
                <th>Jarak (cm)</th>
                <th>Durasi (ms)</th>
                <th>Status</th>
                <th>Waktu</th>
            </tr>
        </thead>

        <tbody>
        <?php
        $no = 1;

        if ($total > 0):
            while ($data = mysqli_fetch_assoc($query)):
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($data['mode']) ?></td>
                <td><?= $data['jarak_cm'] !== null
                    ? htmlspecialchars($data['jarak_cm'])
                    : '-' ?></td>
                <td><?= htmlspecialchars($data['durasi_ms']) ?></td>
                <td><?= htmlspecialchars($data['status']) ?></td>
                <td><?= htmlspecialchars($data['created_at']) ?></td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr>
                <td colspan="6">Belum ada data penyemprotan.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

</div>
</body>
</html>
