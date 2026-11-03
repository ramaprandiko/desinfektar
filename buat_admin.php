<?php
include 'config/koneksi.php';

$nama = "Administrator";
$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users (nama, username, password, role)
     VALUES (?, ?, ?, ?)"
);

mysqli_stmt_bind_param($stmt, "ssss", $nama, $username, $password, $role);

if (mysqli_stmt_execute($stmt)) {
    echo "Akun admin berhasil dibuat. Silakan hapus file buat_admin.php.";
} else {
    echo "Gagal membuat akun: " . mysqli_error($conn);
}
?>