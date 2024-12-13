<?php 
// mengaktifkan session pada php
session_start();
 
// menghubungkan php dengan koneksi database
include 'koneksi.php';
 
// menangkap data yang dikirim dari form login
$name = $_POST['name'];
$password = $_POST['password'];

 
// menyeleksi data user dengan username dan password yang sesuai
$login = mysqli_query($koneksi, "SELECT * FROM user WHERE name='$name' AND password='$password'");
// menghitung jumlah data yang ditemukan
$cek = mysqli_num_rows($login);
 
// cek apakah username dan password di temukan pada database
if ($cek > 0) {
 
    $data = mysqli_fetch_assoc($login);
 
    // cek level user
    switch ($data['level']) {
        case "Koordinator":
            $_SESSION['id'] = $data['id']; 
            $_SESSION['name'] = $name;
            $_SESSION['level'] = "Koordinator";
            header("location:halaman_koordinator.php");
            break;
            
        case "Aktif":
            $_SESSION['id'] = $data['id']; 
            $_SESSION['name'] = $name;
            $_SESSION['level'] = "Aktif";
            header("location:halaman_aktif.php");
            break;
            
        case "Nonaktif":
            $_SESSION['id'] = $data['id']; 
            $_SESSION['name'] = $name;
            $_SESSION['level'] = "Nonaktif";
            header("location:halaman_nonaktif.php");
            break;
            
        default:
            header("location:index.php");
            break;
    }
} else {
    $message = "Login gagal. Silakan coba lagi.";
    echo "<script>alert('$message'); window.location.href='index.php';</script>";
}
?>
