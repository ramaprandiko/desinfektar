<?php
require_once 'config/auth.php';
$tab = $_GET['tab'] ?? 'rangkaian';

$tabs = [
    'rangkaian' => 'Diagram Rangkaian',
    'usecase' => 'Use Case',
    'activity' => 'Activity',
    'sequence' => 'Sequence',
    'class' => 'Class Diagram'
];

if (!array_key_exists($tab, $tabs)) {
    $tab = 'rangkaian';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagram UML & Rangkaian</title>
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
        <a href="diagram.php" class="menu active"><span>◇</span> Diagram UML & Rangkaian</a>
        <a href="hardware.php" class="menu"><span>▣</span> Hardware & Script Arduino</a>
        <a href="analisis.php" class="menu"><span>▤</span> Analisis & Perbandingan</a>

        <div class="sidebar-footer">
            <p>SISTEM DESINFEKTAN OTOMATIS</p>
            <small>Berbasis Arduino Uno R3</small>
            <small class="version">Version 1.0</small>
        </div>
    </aside>

    <main class="content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">DOCUMENTATION / SYSTEM DESIGN</p>
                <h1>Diagram UML & Rangkaian</h1>
                <p class="subtitle">Dokumentasi rancangan perangkat keras dan alur kerja sistem.</p>
            </div>
            <div class="date-box">📅 <?= date('d M Y') ?></div>
        </div>

        <div class="diagram-tabs">
            <?php foreach ($tabs as $key => $label): ?>
                <a href="diagram.php?tab=<?= $key ?>"
                   class="<?= $tab === $key ? 'selected' : '' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($tab === 'rangkaian'): ?>
            <section class="panel">
                <h3>Diagram Rangkaian Perangkat</h3>
                <p class="panel-desc">Hubungan komponen utama dalam sistem desinfektan otomatis berbasis Arduino Uno R3.</p>

                <div class="circuit-area">
                    <div class="circuit-device sensor-device">
                        <span>◎</span>
                        <strong>Ultrasonic HC-SR04</strong>
                        <small>TRIG · ECHO</small>
                    </div>

                    <div class="circuit-arrow">⟶</div>

                    <div class="circuit-device arduino-device">
                        <span>▣</span>
                        <strong>Arduino Uno R3</strong>
                        <small>Microcontroller</small>
                    </div>

                    <div class="circuit-arrow">⟶</div>

                    <div class="circuit-device relay-device">
                        <span>⚡</span>
                        <strong>Relay Module</strong>
                        <small>Digital Pin D7</small>
                    </div>

                    <div class="circuit-arrow">⟶</div>

                    <div class="circuit-device pump-device">
                        <span>◉</span>
                        <strong>Pompa</strong>
                        <small>Penyemprot</small>
                    </div>
                </div>

                <h3 class="subheading">Konfigurasi Pin Arduino</h3>
                <div class="table-scroll">
                    <table class="diagram-table">
                        <thead>
                            <tr><th>Komponen</th><th>Pin Arduino</th><th>Fungsi</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>Ultrasonic TRIG</td><td>D9</td><td>Mengirim sinyal ultrasonik</td></tr>
                            <tr><td>Ultrasonic ECHO</td><td>D10</td><td>Menerima pantulan sinyal</td></tr>
                            <tr><td>Relay</td><td>D7</td><td>Mengendalikan pompa</td></tr>
                            <tr><td>VCC</td><td>5V</td><td>Sumber tegangan sensor</td></tr>
                            <tr><td>GND</td><td>GND</td><td>Ground perangkat</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="diagram-note">Catatan: diagram ini merupakan gambaran hubungan komponen berdasarkan konfigurasi simulasi. Pastikan sambungan listrik dan kebutuhan daya pompa diverifikasi sebelum perakitan fisik.</p>
            </section>
        <?php endif; ?>

        <?php if ($tab === 'usecase'): ?>
            <section class="panel">
                <h3>Use Case Diagram</h3>
                <p class="panel-desc">Gambaran interaksi pengguna dengan sistem desinfektan otomatis.</p>

                <div class="uml-area">
                    <div class="actor">
                        <span>♙</span>
                        <strong>Admin / Petugas</strong>
                    </div>
                    <div class="usecase-list">
                        <div class="usecase">Mengakses Dashboard</div>
                        <div class="usecase">Melihat Status Sensor</div>
                        <div class="usecase">Mengatur Parameter Sensor</div>
                        <div class="usecase">Melakukan Semprot Manual</div>
                        <div class="usecase">Melihat Riwayat Aktivitas</div>
                        <div class="usecase">Melihat Informasi Perangkat</div>
                    </div>
                </div>
                <p class="diagram-note">Admin atau petugas dapat memantau sistem, mengubah parameter, menjalankan simulasi, dan melihat riwayat aktivitas.</p>
            </section>
        <?php endif; ?>

        <?php if ($tab === 'activity'): ?>
            <section class="panel">
                <h3>Activity Diagram</h3>
                <p class="panel-desc">Alur aktivitas sistem ketika mendeteksi objek dan menjalankan penyemprotan.</p>

                <div class="activity-flow">
                    <div class="flow-node start-node">Mulai</div>
                    <div class="flow-line"></div>
                    <div class="flow-node">Sensor membaca jarak objek</div>
                    <div class="flow-line"></div>
                    <div class="flow-node decision-node">Apakah jarak ≤ 15 cm?</div>
                    <div class="flow-branches">
                        <div class="branch">
                            <span class="branch-label">YA</span>
                            <div class="flow-node">Periksa level cairan dan cooldown</div>
                            <div class="flow-line"></div>
                            <div class="flow-node">Relay aktif dan pompa menyemprot</div>
                            <div class="flow-line"></div>
                            <div class="flow-node">Simpan riwayat penyemprotan</div>
                        </div>
                        <div class="branch">
                            <span class="branch-label">TIDAK</span>
                            <div class="flow-node">Pompa tetap mati</div>
                            <div class="flow-line"></div>
                            <div class="flow-node">Menunggu pembacaan berikutnya</div>
                        </div>
                    </div>
                    <div class="flow-line"></div>
                    <div class="flow-node end-node">Selesai / Ulangi pemantauan</div>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($tab === 'sequence'): ?>
            <section class="panel">
                <h3>Sequence Diagram</h3>
                <p class="panel-desc">Urutan komunikasi antara petugas, sensor, Arduino, relay, dan pompa.</p>

                <div class="sequence-grid">
                    <div class="sequence-head">Petugas</div>
                    <div class="sequence-head">Sensor</div>
                    <div class="sequence-head">Arduino</div>
                    <div class="sequence-head">Relay</div>
                    <div class="sequence-head">Pompa</div>

                    <div class="sequence-cell">Mulai simulasi</div>
                    <div class="sequence-cell"> </div>
                    <div class="sequence-cell"> </div>
                    <div class="sequence-cell"> </div>
                    <div class="sequence-cell"> </div>

                    <div class="sequence-cell"></div>
                    <div class="sequence-cell">Baca jarak</div>
                    <div class="sequence-cell"> </div>
                    <div class="sequence-cell"></div>
                    <div class="sequence-cell"></div>

                    <div class="sequence-cell"></div>
                    <div class="sequence-cell">Kirim hasil</div>
                    <div class="sequence-cell">Periksa jarak</div>
                    <div class="sequence-cell"></div>
                    <div class="sequence-cell"></div>

                    <div class="sequence-cell"></div>
                    <div class="sequence-cell"></div>
                    <div class="sequence-cell">Aktifkan relay</div>
                    <div class="sequence-cell">Relay ON</div>
                    <div class="sequence-cell"> </div>

                    <div class="sequence-cell"></div>
                    <div class="sequence-cell"></div>
                    <div class="sequence-cell"></div>
                    <div class="sequence-cell">Kirim daya</div>
                    <div class="sequence-cell">Pompa menyemprot</div>
                </div>
                <p class="diagram-note">Urutan di atas menggambarkan proses ketika objek terdeteksi dalam batas jarak dan kondisi penyemprotan terpenuhi.</p>
            </section>
        <?php endif; ?>

        <?php if ($tab === 'class'): ?>
            <section class="panel">
                <h3>Class Diagram</h3>
                <p class="panel-desc">Gambaran kelas data dan fungsi yang digunakan dalam sistem.</p>

                <div class="class-grid">
                    <div class="class-box">
                        <h4>Pengaturan</h4>
                        <div>id</div><div>batas_deteksi_cm</div>
                        <div>durasi_semprot_ms</div><div>jeda_ms</div>
                        <div class="class-method">simpanPengaturan()</div>
                    </div>

                    <div class="class-box">
                        <h4>SensorData</h4>
                        <div>id</div><div>jarak_cm</div>
                        <div>objek_terdeteksi</div><div>created_at</div>
                        <div class="class-method">bacaSensor()</div>
                    </div>

                    <div class="class-box">
                        <h4>Penyemprotan</h4>
                        <div>id</div><div>mode</div>
                        <div>jarak_cm</div><div>durasi_ms</div>
                        <div>status</div>
                        <div class="class-method">jalankanSemprot()</div>
                    </div>

                    <div class="class-box">
                        <h4>Aktivitas</h4>
                        <div>id</div><div>jenis</div>
                        <div>pesan</div><div>created_at</div>
                        <div class="class-method">catatAktivitas()</div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <footer class="footer">
            Sistem Desinfektan Otomatis · PT Tandem Makmur Abadi
        </footer>
    </main>
</div>
</body>
</html>