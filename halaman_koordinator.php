<?php 
session_start();

// Cek apakah pengguna sudah login dan memiliki level peran
if (!isset($_SESSION['name']) || !isset($_SESSION['level'])) {
  session_destroy(); // Menghapus sesi

  $error_message = "Silahkah login terlebih dahulu!" . mysqli_error($koneksi);
  echo "<script>
          alert('$error_message');
          window.location.href='index.php';
        </script>";
  exit;
}

// Cek apakah level peran adalah "admin", jika bukan, tendang dari sesi dan redirect ke halaman login
if ($_SESSION['level'] !== "Koordinator") {
  session_destroy(); // Menghapus sesi

  $error_message = "Akses tidak diizinkan!" . mysqli_error($koneksi);
  echo "<script>
          alert('$error_message');
          window.location.href='index.php';
        </script>";
  exit; // Tambahkan exit untuk memastikan tidak ada kode ekstra yang dijalankan setelahnya
}

?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<title>Welcome</title>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #FE5722">
 
<a class="navbar-brand">SIMandorTfSM</a>
 
 <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
   <span class="navbar-toggler-icon"></span>
 </button>

 <div class="collapse navbar-collapse" id="navbarSupportedContent">

   <ul class="navbar-nav mr-auto">
     <li class="nav-item">
       <a class="nav-link" href="halaman_admin.php">Home <span class="sr-only">(current)</span></a>
     </li>
     <li class="nav-item">
       <a class="nav-link" href="tambahmemberadmin.php">Tambah Member</a>
     </li>
     <li class="nav-item">
       <a class="nav-link" href="lihatmemberadmin.php">List Member</a>
     </li>
     <li class="nav-item">
       <a class="nav-link" href="tulispesanadmin.php">Tulis Pesan</a>
     </li>
     <li class="nav-item">
       <a class="nav-link" href="logpesanadmin.php">Log Pesan</a>
     </li>
     <li class="nav-item">
       <a class="nav-link" href="editprofileadmin.php">Edit Profile</a>
     </li>
     
     
   </ul>
   <a href="logout.php" class="btn btn-danger">Logout</a>

</div>

</nav>

<h3>Selamat datang	<b><?php echo $_SESSION['username']; ?></b><h3>
<h5><b><?php echo $_SESSION['level']; ?></b><h5>

<br>
<hr>
<center>
<h5>Transport For Semarang 2023</h5>
</center>
</body>
</html>
