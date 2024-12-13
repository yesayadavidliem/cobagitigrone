<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['id_user']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Periksa apakah pengguna memiliki previlage admin
if ($_SESSION['previlage'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Halaman ini hanya untuk admin.'); window.location.href='index.php';</script>";
    exit();
}

// Sertakan koneksi
include('koneksi.php');

// Periksa apakah data telah dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari formulir
    $id_poinrekap = $_POST['id_poinrekap'];
    $keaktifan = $_POST['keaktifan'];
    $sate = $_POST['sate'];
    $jiwa_baru = $_POST['jiwa_baru'];

    // Hitung ulang total (Keaktifan + Sate + Jiwa Baru)
    $total = $keaktifan + $sate + $jiwa_baru;

    // Update data di tabel poinrekap
    $sql = "
        UPDATE poinrekap
        SET keaktifan = ?, sate = ?, jiwa_baru = ?, total = ?
        WHERE id_poinrekap = ? AND isdelete = 0
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiis", $keaktifan, $sate, $jiwa_baru, $total, $id_poinrekap);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='input_poin.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memperbarui data.'); window.location.href='input_poin.php';</script>";
    }
    $stmt->close();
} else {
    echo "<script>alert('Metode tidak valid!'); window.location.href='input_poin.php';</script>";
}

$conn->close();
?>
