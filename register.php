<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Login</title>
</head>
<body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<!-- As a heading -->
<nav class="navbar navbar-light" style="background-color: #28A745;">
  <span class="navbar-brand mb-0 h1">EpicCollab</span>
 
 <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
   <span class="navbar-toggler-icon"></span>
 </button>

 <div class="collapse navbar-collapse" id="navbarSupportedContent">

   <ul class="navbar-nav mr-auto">
     <li class="nav-item">
       <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
     </li>

</div>

</nav>

<div class="container">
  <h1>Regisrasi User</h1>
  <form method="post" action="actionregister.php">
    <div class="form-group">
      <label for="name">Nama :</label>
      <input type="text" class="form-control" id="name" name="name" required="required">
    </div>
    <div class="form-group">
      <label for="password" style="display: none;">Password:</label>
      <input type="password" class="form-control" id="password" name="password" style="display: none;">
    </div>
    <div class="form-group">
      <label for="tanggal_lahir">Tanggal Lahir [tgl/bln/tahun] [contoh : 04/02/2000]:</label>
      <input type="text" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required="required">
    </div>

    <?php
    // Menyertakan file koneksi
    include 'koneksi.php';
    ?>
    <div class="form-group">
    <label for="ibadah">Ibadah :</label>
    <select class="form-control" id="ibadah" name="ibadah" required="required">
        <option value="">Pilih Ibadah</option>
        <?php
        // Mengambil data dari tabel ibadahepic
        $query = "SELECT ibadahepic FROM ibadahepic";
        $result = mysqli_query($koneksi, $query);

        // Menampilkan data dalam dropdown
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['ibadahepic'] . "'>" . $row['ibadahepic'] . "</option>";
        }
         // Menutup koneksi
         mysqli_close($koneksi);
         ?>
     </select>
     </div>

    <div class="form-group">
      <label for="nomorhp">Nomor HP :</label>
      <input type="number" class="form-control" id="nomorhp" name="nomorhp" required="required">
    </div>
      <div class="form-group">
      <label for="bidang">Bidang :</label>
      <input type="text" class="form-control" id="bidang" name="bidang" required="required">
    </div>

    <div class="form-group">
      <label for="level" style="display: none;">Status :</label>
      <select class="form-control" id="level" name="level" required="required" style="display: none;">
        <option>Tidak</option>
      </select>
    </div>
    <div class="form-group">
      <label for="keterangan" style="display: none;">Keterangan (wajib diisi jika blokir):</label>
      <input type="text" class="form-control" id="keterangan" name="keterangan" style="display: none;">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
  </form>
</div>
<br>
<hr>
<center>
<h5>Epic Teen GBI Gajahmada 2024</h5>
</center>

 
</body>
</html>
