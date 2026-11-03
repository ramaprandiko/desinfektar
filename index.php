<?php
require_once 'config/auth.php';
include 'config/koneksi.php';

$pengaturan = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
$setting = mysqli_fetch_assoc($pengaturan);

$totalSemprot = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penyemprotan");
$dataSemprot = mysqli_fetch_assoc($totalSemprot);

$levelCairan = $setting['level_cairan'] ?? 78;
$batasDeteksi = $setting['batas_deteksi_cm'] ?? 15;
$durasiSemprot = $setting['durasi_semprot_ms'] ?? 1500;
$jedaSemprot = $setting['jeda_ms'] ?? 3000;
$jumlahSemprot = $dataSemprot['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Desinfektan Otomatis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="app">

    <header class="topbar">
        <div class="brand">
            <div class="brand-icon">✦</div>
            <div>
                <h2>PT TANDEM MAKMUR ABADI</h2>
                <p>UNIT FEEDMILL & BIOSECURITY</p>
            </div>
        </div>

        <div class="topbar-right">
            <span class="system-status">
                <span class="status-dot"></span>
                SYSTEM ACTIVE
            </span>
<span class="user-info">
    <?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?>
</span>

<div class="avatar">
    <?= strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)) ?>
</div>

<a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>

    <div class="main-layout">

        <aside class="sidebar">
            <div class="sidebar-label">MENU UTAMA</div>

            <a href="index.php" class="menu active">
                <span>▦</span> Dashboard Overview
            </a>

            <a href="#" class="menu">
                <span>⌁</span> Kontrol & Simulasi Sensor
            </a>

            <a href="diagram.php" class="menu">
    <span>◇</span> Diagram UML & Rangkaian
</a>
            <a href="#" class="menu">
                <span>▣</span> Hardware & Script Arduino
            </a>

            <a href="#" class="menu">
                <span>▤</span> Analisis & Perbandingan
            </a>
<a href="laporan.php" class="nav-link">
    Laporan Penyemprotan
</a>
            <div class="sidebar-footer">
                <p>SISTEM DESINFEKTAN OTOMATIS</p>
                <small>Berbasis Arduino Uno R3</small>
                <small class="version">Version 1.0</small>
            </div>
        </aside>

        <main class="content">

            <div class="page-heading">
                <div>
                    <p class="eyebrow">OVERVIEW / DASHBOARD</p>
                    <h1>Dashboard Overview</h1>
                    <p class="subtitle">
                        Monitoring dan pemantauan sistem desinfektan otomatis.
                    </p>
                </div>

                <div class="date-box">
                    <span>📅</span>
                    <?= date('d M Y') ?>
                </div>
            </div>

            <section class="welcome-card">
                <div>
                    <span class="welcome-tag">ARDUINO AUTOMATION SYSTEM</span>
                    <h2>Sistem Desinfektan Otomatis</h2>
                    <p>
                        Sistem penyemprotan desinfektan otomatis menggunakan
                        sensor Ultrasonic HC-SR04 dan Arduino Uno R3.
                    </p>
                </div>
                <div class="welcome-symbol">✧</div>
            </section>

            <div class="section-title">
                <div>
                    <h2>Ringkasan Sistem</h2>
                    <p>Informasi kondisi sistem saat ini.</p>
                </div>
                <span class="live-label">● LIVE MONITORING</span>
            </div>

            <section class="stats-grid">

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Level Cairan</span>
                        <span class="stat-icon green">◉</span>
                    </div>
                    <h2><?= number_format($levelCairan, 0) ?>%</h2>
                    <div class="progress">
                        <div class="progress-fill" style="width: <?= min(100, max(0, $levelCairan)) ?>%"></div>
                    </div>
                    <small>Kapasitas cairan desinfektan</small>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Total Penyemprotan</span>
                        <span class="stat-icon blue">⌁</span>
                    </div>
                    <h2><?= number_format($jumlahSemprot) ?></h2>
                    <small>Total aktivitas penyemprotan tersimpan</small>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Batas Deteksi</span>
                        <span class="stat-icon purple">◎</span>
                    </div>
                    <h2><?= $batasDeteksi ?> <small>cm</small></h2>
                    <small>Jarak pemicu sensor ultrasonic</small>
                </div>

                <div class="stat-card">
                    <div class="stat-top">
                        <span>Status Sistem</span>
                        <span class="stat-icon green">✓</span>
                    </div>
                    <h2 class="active-text">Siap</h2>
                    <small>Dashboard simulasi aktif</small>
                </div>

            </section>

            <div class="section-title">
                <div>
                    <h2>Konfigurasi Perangkat</h2>
                    <p>Parameter utama perangkat desinfektan otomatis.</p>
                </div>
            </div>

            <section class="device-grid">

                <div class="device-card">
                    <span class="device-icon">⌁</span>
                    <div>
                        <small>SENSOR JARAK</small>
                        <h3>Ultrasonic HC-SR04</h3>
                        <p>Mendeteksi objek di depan sensor.</p>
                    </div>
                </div>

                <div class="device-card">
                    <span class="device-icon">▣</span>
                    <div>
                        <small>MIKROKONTROLER</small>
                        <h3>Arduino Uno R3</h3>
                        <p>Pengendali utama sistem otomatis.</p>
                    </div>
                </div>

                <div class="device-card">
                    <span class="device-icon">⚡</span>
                    <div>
                        <small>RELAY / POMPA</small>
                        <h3>Pin Digital D7</h3>
                        <p>Mengendalikan proses penyemprotan.</p>
                    </div>
                </div>

            </section>

            <div class="section-title">
                <div>
                    <h2>Parameter Penyemprotan</h2>
                    <p>Pengaturan waktu dan proses penyemprotan.</p>
                </div>
            </div>

            <section class="parameter-grid">
                <div class="parameter-card">
                    <span>Durasi Semprot</span>
                    <strong><?= $durasiSemprot ?> ms</strong>
                </div>

                <div class="parameter-card">
                    <span>Jeda Penyemprotan</span>
                    <strong><?= $jedaSemprot ?> ms</strong>
                </div>

                <div class="parameter-card">
                    <span>Sensor Trigger</span>
                    <strong>Pin D9</strong>
                </div>

                <div class="parameter-card">
                    <span>Sensor Echo</span>
                    <strong>Pin D10</strong>
                </div>
            </section>

            <footer class="footer">
                Sistem Desinfektan Otomatis · PT Tandem Makmur Abadi
            </footer>

        </main>
    </div>
</div>

</body>
</html>