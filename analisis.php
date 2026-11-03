<?php
require_once 'config/auth.php';
include 'config/koneksi.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$totalQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penyemprotan");
$total = (int)mysqli_fetch_assoc($totalQ)['total'];

$otomatisQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penyemprotan WHERE mode='otomatis'");
$otomatis = (int)mysqli_fetch_assoc($otomatisQ)['total'];

$manualQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penyemprotan WHERE mode='manual'");
$manual = (int)mysqli_fetch_assoc($manualQ)['total'];

$berhasilQ = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penyemprotan WHERE status='berhasil'");
$berhasil = (int)mysqli_fetch_assoc($berhasilQ)['total'];

$settingQ = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
$setting = mysqli_fetch_assoc($settingQ);

$level = $setting['level_cairan'] ?? 0;
$batas = $setting['batas_deteksi_cm'] ?? 15;

$sensorQ = mysqli_query($conn, "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1");
$sensorTerbaru = mysqli_fetch_assoc($sensorQ);

$riwayatQ = mysqli_query($conn, "SELECT * FROM penyemprotan ORDER BY id DESC LIMIT 8");

$persenOtomatis = $total > 0 ? round(($otomatis / $total) * 100) : 0;
$persenManual = $total > 0 ? round(($manual / $total) * 100) : 0;
$persenBerhasil = $total > 0 ? round(($berhasil / $total) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis & Perbandingan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topbar">
    <div class="brand">
        <div class="brand-icon">✦</div>
        <div>
            <h2>PT TANDEM MAKMUR ABADI</h2>
            <p>UNIT FEEDMILL & BIOSECURITY</p>
        </div>
    </div>
    <div class="topbar-right">
        <span class="system-status"><span class="status-dot"></span> SYSTEM ACTIVE</span>
        <span class="user-info">Admin</span>
        <div class="avatar">A</div>
    </div>
</header>

<div class="main-layout">
    <aside class="sidebar">
        <div class="sidebar-label">MENU UTAMA</div>
        <a href="index.php" class="menu"><span>▦</span> Dashboard Overview</a>
        <a href="kontrol.php" class="menu"><span>⌁</span> Kontrol & Simulasi Sensor</a>
        <a href="diagram.php" class="menu"><span>◇</span> Diagram UML & Rangkaian</a>
        <a href="hardware.php" class="menu"><span>▣</span> Hardware & Script Arduino</a>
        <a href="analisis.php" class="menu active"><span>▤</span> Analisis & Perbandingan</a>

        <div class="sidebar-footer">
            <p>SISTEM DESINFEKTAN OTOMATIS</p>
            <small>Berbasis Arduino Uno R3</small>
            <small class="version">Version 1.0</small>
        </div>
    </aside>

    <main class="content">

        <div class="page-heading">
            <div>
                <p class="eyebrow">ANALYSIS / SYSTEM PERFORMANCE</p>
                <h1>Analisis & Perbandingan</h1>
                <p class="subtitle">Ringkasan hasil simulasi dan perbandingan proses penyemprotan.</p>
            </div>
            <div class="date-box">📅 <?= date('d M Y') ?></div>
        </div>

        <div class="notice">
            Data pada halaman ini berasal dari aktivitas simulasi yang tersimpan di database.
            Data belum mewakili pengukuran perangkat Arduino fisik.
        </div>

        <div class="section-title">
            <div>
                <h2>Statistik Penyemprotan</h2>
                <p>Ringkasan aktivitas penyemprotan yang tercatat.</p>
            </div>
            <span class="live-label">● DATABASE REPORT</span>
        </div>

        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-top">
                    <span>Total Penyemprotan</span>
                    <span class="stat-icon green">⌁</span>
                </div>
                <h2><?= number_format($total) ?></h2>
                <small>Seluruh aktivitas tercatat</small>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span>Otomatis</span>
                    <span class="stat-icon blue">⚙</span>
                </div>
                <h2><?= number_format($otomatis) ?></h2>
                <small><?= $persenOtomatis ?>% dari total penyemprotan</small>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span>Manual</span>
                    <span class="stat-icon purple">✋</span>
                </div>
                <h2><?= number_format($manual) ?></h2>
                <small><?= $persenManual ?>% dari total penyemprotan</small>
            </div>

            <div class="stat-card">
                <div class="stat-top">
                    <span>Level Cairan</span>
                    <span class="stat-icon green">◉</span>
                </div>
                <h2><?= number_format($level, 0) ?>%</h2>
                <small>Kondisi cairan saat ini</small>
            </div>
        </section>

        <div class="section-title">
            <div>
                <h2>Hasil Pembacaan Sensor</h2>
                <p>Informasi pembacaan sensor terakhir yang tersimpan.</p>
            </div>
        </div>

        <section class="panel">
            <?php if ($sensorTerbaru): ?>
                <div class="analysis-sensor">
                    <div>
                        <small>JARAK TERAKHIR</small>
                        <h2><?= e($sensorTerbaru['jarak_cm']) ?> cm</h2>
                    </div>
                    <div>
                        <small>STATUS DETEKSI</small>
                        <h3 class="<?= $sensorTerbaru['objek_terdeteksi'] ? 'active-text' : '' ?>">
                            <?= $sensorTerbaru['objek_terdeteksi'] ? 'Objek Terdeteksi' : 'Tidak Terdeteksi' ?>
                        </h3>
                    </div>
                    <div>
                        <small>BATAS DETEKSI</small>
                        <h3><?= e($batas) ?> cm</h3>
                    </div>
                </div>
                <p class="panel-desc">
                    Waktu pencatatan:
                    <?= date('d M Y H:i:s', strtotime($sensorTerbaru['created_at'])) ?>
                </p>
            <?php else: ?>
                <p class="panel-desc">Belum ada data sensor. Silakan lakukan pengujian melalui menu Kontrol & Simulasi Sensor.</p>
            <?php endif; ?>
        </section>

        <div class="section-title">
            <div>
                <h2>Perbandingan Metode Penyemprotan</h2>
                <p>Gambaran perbedaan proses manual dan otomatis.</p>
            </div>
        </div>

        <section class="comparison-grid">
            <div class="comparison-card">
                <span class="comparison-label manual-label">METODE MANUAL</span>
                <h3>Penyemprotan Manual</h3>
                <ul>
                    <li>Petugas melakukan penyemprotan secara langsung.</li>
                    <li>Proses bergantung pada tindakan petugas.</li>
                    <li>Pencatatan aktivitas dapat dilakukan secara terpisah.</li>
                    <li>Memerlukan keterlibatan petugas saat penyemprotan.</li>
                </ul>
            </div>

            <div class="comparison-card">
                <span class="comparison-label auto-label">METODE OTOMATIS</span>
                <h3>Sistem Desinfektan Otomatis</h3>
                <ul>
                    <li>Sensor mendeteksi objek berdasarkan jarak.</li>
                    <li>Arduino memproses hasil pembacaan sensor.</li>
                    <li>Relay mengendalikan pompa penyemprot.</li>
                    <li>Aktivitas simulasi dapat disimpan dalam database.</li>
                </ul>
            </div>
        </section>

        <div class="section-title">
            <div>
                <h2>Persentase Aktivitas</h2>
                <p>Perbandingan jumlah penyemprotan berdasarkan metode.</p>
            </div>
        </div>

        <section class="panel">
            <div class="bar-analysis">
                <div class="bar-info">
                    <span>Otomatis</span>
                    <strong><?= $persenOtomatis ?>%</strong>
                </div>
                <div class="analysis-track">
                    <div class="analysis-fill" style="width:<?= $persenOtomatis ?>%"></div>
                </div>
            </div>

            <div class="bar-analysis">
                <div class="bar-info">
                    <span>Manual</span>
                    <strong><?= $persenManual ?>%</strong>
                </div>
                <div class="analysis-track">
                    <div class="analysis-fill manual-fill" style="width:<?= $persenManual ?>%"></div>
                </div>
            </div>

            <div class="bar-analysis">
                <div class="bar-info">
                    <span>Keberhasilan pencatatan penyemprotan</span>
                    <strong><?= $persenBerhasil ?>%</strong>
                </div>
                <div class="analysis-track">
                    <div class="analysis-fill" style="width:<?= $persenBerhasil ?>%"></div>
                </div>
            </div>
        </section>

        <div class="section-title">
            <div>
                <h2>Riwayat Penyemprotan</h2>
                <p>Delapan aktivitas penyemprotan terakhir.</p>
            </div>
        </div>

        <section class="panel">
            <div class="table-scroll">
                <table class="diagram-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Mode</th>
                            <th>Jarak</th>
                            <th>Durasi</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($riwayatQ) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($riwayatQ)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= ucfirst(e($row['mode'])) ?></td>
                                    <td><?= e($row['jarak_cm'] ?? '-') ?> cm</td>
                                    <td><?= e($row['durasi_ms']) ?> ms</td>
                                    <td><?= ucfirst(e($row['status'])) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6">Belum ada riwayat penyemprotan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <footer class="footer">
            Sistem Desinfektan Otomatis · PT Tandem Makmur Abadi
        </footer>

    </main>
</div>
</body>
</html>