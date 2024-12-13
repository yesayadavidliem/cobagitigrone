<?php
session_start();

// Periksa apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Include database connection
include('koneksi.php');

// Query untuk mengambil data notulensi
$sql = "SELECT id_notulensi, username, tanggal_notulen, judul_notulen, cc_notulen, isi_notulen, issign, isdelete 
        FROM notulensi WHERE isdelete = 0";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Notulensi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Manajemen Notulensi</h3>
    
    <div class="mb-3">
        <a href="form_notulen.php" class="btn btn-primary">Tambah Notulen</a>
        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Tanggal</th>
            <th>Judul</th>
            <th>CC</th>
            <th>Isi</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id_notulensi'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['tanggal_notulen'] ?></td>
                <td><?= htmlspecialchars($row['judul_notulen']) ?></td>
                <td><?= htmlspecialchars($row['cc_notulen']) ?></td>
                <td><?= htmlspecialchars(substr($row['isi_notulen'], 0, 50)) ?>...</td>
                <td><?= $row['issign'] == 0 ? 'Belum Ditandatangani' : 'Ditandatangani' ?></td>
                <td>
                    <a href="detail_notulen.php?id=<?= $row['id_notulensi'] ?>" class="btn btn-info btn-sm">Detail</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
