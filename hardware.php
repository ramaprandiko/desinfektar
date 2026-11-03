<?php
require_once 'config/auth.php';
$arduinoCode = <<<'ARDUINO'
const int trigPin = 9;
const int echoPin = 10;
const int relayPin = 7;

const int batasDeteksi = 15;
const unsigned long durasiSemprot = 1500;
const unsigned long cooldown = 3000;

unsigned long waktuTerakhir = 0;

void setup() {
    Serial.begin(9600);

    pinMode(trigPin, OUTPUT);
    pinMode(echoPin, INPUT);
    pinMode(relayPin, OUTPUT);

    digitalWrite(relayPin, LOW);
}

void loop() {
    long durasi;
    float jarak;

    digitalWrite(trigPin, LOW);
    delayMicroseconds(2);

    digitalWrite(trigPin, HIGH);
    delayMicroseconds(10);
    digitalWrite(trigPin, LOW);

    durasi = pulseIn(echoPin, HIGH, 30000);

    if (durasi == 0) {
        Serial.println("Sensor tidak membaca objek");
        delay(200);
        return;
    }

    jarak = durasi * 0.0343 / 2;

    Serial.print("Jarak objek: ");
    Serial.print(jarak);
    Serial.println(" cm");

    if (jarak > 0 && jarak <= batasDeteksi) {
        if (millis() - waktuTerakhir >= cooldown) {
            digitalWrite(relayPin, HIGH);
            Serial.println("Pompa aktif");

            delay(durasiSemprot);

            digitalWrite(relayPin, LOW);
            Serial.println("Pompa mati");

            waktuTerakhir = millis();
        }
    }

    delay(100);
}
ARDUINO;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hardware & Script Arduino</title>
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
        <a href="hardware.php" class="menu active"><span>▣</span> Hardware & Script Arduino</a>
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
                <p class="eyebrow">HARDWARE / ARDUINO SOURCE CODE</p>
                <h1>Hardware & Script Arduino</h1>
                <p class="subtitle">Informasi perangkat keras dan program pengendali sistem.</p>
            </div>
            <div class="date-box">📅 <?= date('d M Y') ?></div>
        </div>

        <div class="section-title">
            <div>
                <h2>Komponen Hardware</h2>
                <p>Perangkat utama yang digunakan dalam sistem desinfektan otomatis.</p>
            </div>
            <span class="live-label">● HARDWARE INFO</span>
        </div>

        <section class="hardware-grid">
            <div class="hardware-card">
                <div class="hardware-icon">▣</div>
                <small>MIKROKONTROLER</small>
                <h3>Arduino Uno R3</h3>
                <p>Memproses data sensor dan mengendalikan relay untuk mengaktifkan pompa.</p>
            </div>

            <div class="hardware-card">
                <div class="hardware-icon">◎</div>
                <small>SENSOR JARAK</small>
                <h3>Ultrasonic HC-SR04</h3>
                <p>Mengukur jarak objek menggunakan gelombang ultrasonik.</p>
            </div>

            <div class="hardware-card">
                <div class="hardware-icon">⚡</div>
                <small>MODUL PENGENDALI</small>
                <h3>Relay Module</h3>
                <p>Mengendalikan aliran daya menuju pompa berdasarkan perintah Arduino.</p>
            </div>

            <div class="hardware-card">
                <div class="hardware-icon">◉</div>
                <small>AKTUATOR</small>
                <h3>Pompa Desinfektan</h3>
                <p>Mengalirkan cairan desinfektan saat sistem memberikan perintah penyemprotan.</p>
            </div>
        </section>

        <div class="section-title">
            <div>
                <h2>Konfigurasi Pin</h2>
                <p>Hubungan pin komponen dengan Arduino Uno R3.</p>
            </div>
        </div>

        <section class="panel">
            <div class="table-scroll">
                <table class="diagram-table">
                    <thead>
                        <tr>
                            <th>Komponen</th>
                            <th>Pin Arduino</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>Ultrasonic TRIG</td><td>D9</td><td>Output pemicu sensor</td></tr>
                        <tr><td>Ultrasonic ECHO</td><td>D10</td><td>Input pantulan sensor</td></tr>
                        <tr><td>Relay IN</td><td>D7</td><td>Kontrol relay pompa</td></tr>
                        <tr><td>Ultrasonic VCC</td><td>5V</td><td>Tegangan sensor</td></tr>
                        <tr><td>Ultrasonic GND</td><td>GND</td><td>Ground sensor</td></tr>
                    </tbody>
                </table>
            </div>
        </section>

        <div class="section-title">
            <div>
                <h2>Program Arduino</h2>
                <p>Contoh kode untuk membaca sensor dan mengendalikan pompa.</p>
            </div>
            <span class="live-label">C++ / ARDUINO</span>
        </div>

        <section class="code-panel">
            <div class="code-toolbar">
                <div><span class="code-dot"></span> desinfektan.ino</div>
                <button type="button" onclick="copyCode()">Salin Kode</button>
            </div>
            <pre><code id="arduinoCode"><?= htmlspecialchars($arduinoCode, ENT_QUOTES, 'UTF-8') ?></code></pre>
        </section>

        <div class="notice warning">
            Perhatian: program ini merupakan contoh awal. Sebelum diunggah ke Arduino, pastikan jenis relay, kondisi aktif HIGH/LOW, sumber daya pompa, dan rangkaian listrik sudah sesuai. Kode ini belum memiliki pembacaan level cairan atau pencatatan database.
        </div>

        <footer class="footer">
            Sistem Desinfektan Otomatis · PT Tandem Makmur Abadi
        </footer>
    </main>
</div>

<script>
function copyCode() {
    const code = document.getElementById('arduinoCode').innerText;
    navigator.clipboard.writeText(code).then(() => {
        alert('Kode Arduino berhasil disalin!');
    }).catch(() => {
        alert('Gagal menyalin. Silakan salin kode secara manual.');
    });
}
</script>

</body>
</html>