<?php
require_once 'config/auth.php';
include 'config/koneksi.php';

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$pesan = "";
$jenisPesan = "success";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';

    if ($aksi === 'simpan_pengaturan') {
        $batas = max(1, min(400, (int)($_POST['batas'] ?? 15)));
        $durasi = max(100, min(10000, (int)($_POST['durasi'] ?? 1500)));
        $jeda = max(0, min(60000, (int)($_POST['jeda'] ?? 3000)));

        $stmt = mysqli_prepare($conn, "UPDATE pengaturan SET batas_deteksi_cm=?, durasi_semprot_ms=?, jeda_ms=? WHERE id=1");
        mysqli_stmt_bind_param($stmt, "iii", $batas, $durasi, $jeda);
        mysqli_stmt_execute($stmt);

        $pesan = "Pengaturan berhasil disimpan.";
    }

    if ($aksi === 'deteksi' || $aksi === 'manual') {
        $jarak = max(0, min(400, (float)($_POST['jarak'] ?? 0)));

        $q = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
        $setting = mysqli_fetch_assoc($q);

        $batas = (int)$setting['batas_deteksi_cm'];
        $durasi = (int)$setting['durasi_semprot_ms'];
        $jeda = (int)$setting['jeda_ms'];
        $level = (float)$setting['level_cairan'];

        if ($aksi === 'deteksi') {
            $objek = ($jarak > 0 && $jarak <= $batas) ? 1 : 0;

            $stmt = mysqli_prepare($conn, "INSERT INTO sensor_data (jarak_cm, objek_terdeteksi) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "di", $jarak, $objek);
            mysqli_stmt_execute($stmt);

            if ($objek) {
                $last = mysqli_query($conn, "SELECT created_at FROM penyemprotan ORDER BY id DESC LIMIT 1");
                $lastSpray = mysqli_fetch_assoc($last);
                $bolehSemprot = !$lastSpray || (time() - strtotime($lastSpray['created_at'])) >= ($jeda / 1000);

                if ($level <= 0) {
                    $pesan = "Objek terdeteksi, tetapi cairan desinfektan habis.";
                    $jenisPesan = "warning";
                } elseif ($bolehSemprot) {
                    $mode = 'otomatis';
                    $status = 'berhasil';

                    $stmt = mysqli_prepare($conn, "INSERT INTO penyemprotan (mode, jarak_cm, durasi_ms, status) VALUES (?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "sdis", $mode, $jarak, $durasi, $status);
                    mysqli_stmt_execute($stmt);

                    $levelBaru = max(0, $level - 1);
                    mysqli_query($conn, "UPDATE pengaturan SET level_cairan=$levelBaru WHERE id=1");

                    $log = "Penyemprotan otomatis berhasil. Jarak objek: $jarak cm.";
                    $stmt = mysqli_prepare($conn, "INSERT INTO aktivitas (jenis, pesan) VALUES ('trigger', ?)");
                    mysqli_stmt_bind_param($stmt, "s", $log);
                    mysqli_stmt_execute($stmt);

                    $pesan = "Objek terdeteksi! Penyemprotan otomatis berhasil.";
                } else {
                    $pesan = "Objek terdeteksi, tetapi sistem masih dalam masa cooldown.";
                    $jenisPesan = "warning";
                }
            } else {
                $pesan = "Tidak ada objek dalam batas deteksi. Pompa tetap mati.";
            }
        }

        if ($aksi === 'manual') {
            if ($level <= 0) {
                $pesan = "Penyemprotan gagal. Cairan desinfektan habis.";
                $jenisPesan = "warning";
            } else {
                $mode = 'manual';
                $status = 'berhasil';

                $stmt = mysqli_prepare($conn, "INSERT INTO penyemprotan (mode, jarak_cm, durasi_ms, status) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "sdis", $mode, $jarak, $durasi, $status);
                mysqli_stmt_execute($stmt);

                $levelBaru = max(0, $level - 1);
                mysqli_query($conn, "UPDATE pengaturan SET level_cairan=$levelBaru WHERE id=1");

                $log = "Penyemprotan manual berhasil dijalankan.";
                $stmt = mysqli_prepare($conn, "INSERT INTO aktivitas (jenis, pesan) VALUES ('success', ?)");
                mysqli_stmt_bind_param($stmt, "s", $log);
                mysqli_stmt_execute($stmt);

                $pesan = "Penyemprotan manual berhasil dijalankan.";
            }
        }
    }

    if ($aksi === 'isi_cairan') {
        mysqli_query($conn, "UPDATE pengaturan SET level_cairan=100 WHERE id=1");
        $pesan = "Cairan desinfektan berhasil diisi ulang menjadi 100%.";
    }

    header("Location: kontrol.php?pesan=" . urlencode($pesan) . "&jenis=" . urlencode($jenisPesan));
    exit;
}

$settingQuery = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
$setting = mysqli_fetch_assoc($settingQuery);

$batas = $setting['batas_deteksi_cm'] ?? 15;
$durasi = $setting['durasi_semprot_ms'] ?? 1500;
$jeda = $setting['jeda_ms'] ?? 3000;
$level = $setting['level_cairan'] ?? 78;

$totalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM penyemprotan");
$totalSemprot = mysqli_fetch_assoc($totalQuery)['total'] ?? 0;

$logQuery = mysqli_query($conn, "SELECT * FROM aktivitas ORDER BY id DESC LIMIT 8");

$pesan = $_GET['pesan'] ?? '';
$jenisPesan = $_GET['jenis'] ?? 'success';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontrol & Simulasi Sensor</title>
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
        <a href="kontrol.php" class="menu active"><span>⌁</span> Kontrol & Simulasi Sensor</a>
        <a href="diagram.php" class="menu">
    <span>◇</span> Diagram UML & Rangkaian
</a>
        <a href="#" class="menu"><span>▣</span> Hardware & Script Arduino</a>
        <a href="#" class="menu"><span>▤</span> Analisis & Perbandingan</a>

        <div class="sidebar-footer">
            <p>SISTEM DESINFEKTAN OTOMATIS</p>
            <small>Berbasis Arduino Uno R3</small>
            <small class="version">Version 1.0</small>
        </div>
    </aside>

    <main class="content">
        <div class="page-heading">
            <div>
                <p class="eyebrow">CONTROL / SENSOR SIMULATION</p>
                <h1>Kontrol & Simulasi Sensor</h1>
                <p class="subtitle">Uji deteksi objek dan proses penyemprotan desinfektan.</p>
            </div>
            <div class="date-box">📅 <?= date('d M Y') ?></div>
        </div>

        <?php if ($pesan): ?>
            <div class="notice <?= e($jenisPesan) ?>"><?= e($pesan) ?></div>
        <?php endif; ?>

        <div class="section-title">
            <div>
                <h2>Simulasi Sensor Ultrasonic</h2>
                <p>Atur jarak objek untuk menguji respons sensor.</p>
            </div>
            <span class="live-label">● SIMULATION MODE</span>
        </div>

        <section class="control-grid">
            <div class="panel">
                <h3>Pengaturan Jarak Objek</h3>
                <p class="panel-desc">Geser nilai untuk mensimulasikan jarak objek di depan sensor.</p>

                <form method="POST">
                    <input type="hidden" name="aksi" value="deteksi">
                    <div class="distance-display">
                        <strong id="distanceValue">10</strong>
                        <span>cm</span>
                    </div>
                    <input type="range" id="distanceSlider" name="jarak"
                           min="1" max="100" value="10" class="range-control">
                    <div class="range-labels"><span>1 cm</span><span>100 cm</span></div>

                    <div class="threshold-info">
                        Batas deteksi saat ini: <strong><?= e($batas) ?> cm</strong>
                    </div>

                    <button type="submit" class="action-button">Uji Deteksi Sensor</button>
                </form>
            </div>

            <div class="panel">
                <h3>Status Perangkat</h3>
                <p class="panel-desc">Status simulasi berdasarkan pengujian terakhir.</p>

                <div class="device-status">
                    <div class="device-status-icon">◎</div>
                    <div>
                        <strong>Ultrasonic HC-SR04</strong>
                        <small>Sensor jarak · Pin D9 / D10</small>
                    </div>
                    <span class="status-pill online">READY</span>
                </div>

                <div class="device-status">
                    <div class="device-status-icon">⚡</div>
                    <div>
                        <strong>Relay</strong>
                        <small>Pengendali pompa · Pin D7</small>
                    </div>
                    <span class="status-pill off">STANDBY</span>
                </div>

                <div class="device-status">
                    <div class="device-status-icon">◉</div>
                    <div>
                        <strong>Pompa Desinfektan</strong>
                        <small>Simulasi aktuator penyemprot</small>
                    </div>
                    <span class="status-pill off">OFF</span>
                </div>

                <div class="device-status">
                    <div class="device-status-icon">▰</div>
                    <div>
                        <strong>Level Cairan</strong>
                        <small><?= e($level) ?>% tersedia</small>
                    </div>
                    <span class="status-pill online">READY</span>
                </div>
            </div>
        </section>

        <div class="section-title">
            <div>
                <h2>Kontrol Penyemprotan</h2>
                <p>Jalankan pengujian penyemprotan secara manual.</p>
            </div>
        </div>

        <section class="control-grid">
            <div class="panel">
                <h3>Manual Spray Test</h3>
                <p class="panel-desc">Tekan tombol untuk mencatat simulasi penyemprotan manual.</p>
                <div class="manual-info">
                    <span>Durasi penyemprotan</span>
                    <strong><?= e($durasi) ?> ms</strong>
                </div>
                <form method="POST">
                    <input type="hidden" name="aksi" value="manual">
                    <input type="hidden" name="jarak" value="0">
                    <button type="submit" class="action-button manual-button"
                        onclick="return confirm('Jalankan simulasi penyemprotan manual?')">
                        ⚡ Jalankan Semprot Manual
                    </button>
                </form>
            </div>

            <div class="panel">
                <h3>Level Cairan Desinfektan</h3>
                <div class="liquid-heading">
                    <strong><?= e($level) ?>%</strong>
                    <span>Kapasitas tangki</span>
                </div>
                <div class="progress liquid-progress">
                    <div class="progress-fill" style="width:<?= min(100, max(0, $level)) ?>%"></div>
                </div>
                <form method="POST">
                    <input type="hidden" name="aksi" value="isi_cairan">
                    <button class="secondary-button" type="submit">＋ Isi Ulang Cairan</button>
                </form>
            </div>
        </section>

        <div class="section-title">
            <div>
                <h2>Parameter Sistem</h2>
                <p>Ubah nilai konfigurasi simulasi sensor dan penyemprotan.</p>
            </div>
        </div>

        <section class="panel">
            <form method="POST" class="settings-form">
                <input type="hidden" name="aksi" value="simpan_pengaturan">

                <label>
                    Batas Deteksi (cm)
                    <input type="number" name="batas" min="1" max="400" value="<?= e($batas) ?>" required>
                </label>

                <label>
                    Durasi Semprot (ms)
                    <input type="number" name="durasi" min="100" max="10000" value="<?= e($durasi) ?>" required>
                </label>

                <label>
                    Jeda / Cooldown (ms)
                    <input type="number" name="jeda" min="0" max="60000" value="<?= e($jeda) ?>" required>
                </label>

                <button type="submit" class="action-button">Simpan Pengaturan</button>
            </form>
        </section>

        <div class="section-title">
            <div>
                <h2>Riwayat Aktivitas</h2>
                <p>Catatan aktivitas simulasi terbaru.</p>
            </div>
            <strong><?= e($totalSemprot) ?> penyemprotan</strong>
        </div>

        <section class="panel">
            <?php if (mysqli_num_rows($logQuery) > 0): ?>
                <div class="activity-list">
                    <?php while ($log = mysqli_fetch_assoc($logQuery)): ?>
                        <div class="activity-item">
                            <span class="activity-dot"></span>
                            <div>
                                <strong><?= e($log['pesan']) ?></strong>
                                <small><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="panel-desc">Belum ada aktivitas. Silakan lakukan pengujian sensor.</p>
            <?php endif; ?>
        </section>

        <footer class="footer">
            Sistem Desinfektan Otomatis · PT Tandem Makmur Abadi
        </footer>
    </main>
</div>

<script>
const slider = document.getElementById('distanceSlider');
const output = document.getElementById('distanceValue');

slider.addEventListener('input', function () {
    output.textContent = this.value;
});
</script>

</body>
</html>