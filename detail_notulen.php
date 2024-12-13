<?php
session_start();
include('koneksi.php');

// Periksa ID notulensi
if (!isset($_GET['id'])) {
    header("Location: manajemen_notulen.php");
    exit();
}

$id_notulensi = intval($_GET['id']);

// Ambil data notulensi
$sql = "SELECT * FROM notulensi WHERE id_notulensi = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_notulensi);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    header("Location: manajemen_notulen.php");
    exit();
}

// Proses penghapusan
if (isset($_POST['delete'])) {
    if ($data['issign'] == 0) {
        $sql_delete = "UPDATE notulensi SET isdelete = 1 WHERE id_notulensi = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id_notulensi);
        $stmt_delete->execute();
        header("Location: manajemen_notulen.php");
        exit();
    }
}

// Proses tanda tangan
if (isset($_POST['sign'])) {
    $sql_sign = "UPDATE notulensi SET issign = 1 WHERE id_notulensi = ?";
    $stmt_sign = $conn->prepare($sql_sign);
    $stmt_sign->bind_param("i", $id_notulensi);
    $stmt_sign->execute();
    header("Location: detail_notulen.php?id=$id_notulensi");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Notulensi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Detail Notulensi</h3>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td>KartoTFSM / <?= $data['id_notulensi'] ?> <?= $data['tanggal_notulen'] ?> </td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?= htmlspecialchars($data['username']) ?></td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td><?= $data['tanggal_notulen'] ?></td>
        </tr>
        <tr>
            <th>Judul</th>
            <td><?= htmlspecialchars($data['judul_notulen']) ?></td>
        </tr>
        <tr>
            <th>CC</th>
            <td><?= htmlspecialchars($data['cc_notulen']) ?></td>
        </tr>
        <tr>
            <th>Isi</th>
            <td><?= nl2br(htmlspecialchars($data['isi_notulen'])) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= $data['issign'] == 0 ? 'Belum Ditandatangani' : 'Ditandatangani' ?></td>
        </tr>
        <tr>
            <th>Lampiran</th>
            <td>
                <?php if ($data['lampiran_notulen'] !== null): ?>
                    <?php 
                        // Jika lampiran adalah gambar, tampilkan dalam tag <img>
                        $img_data = base64_encode($data['lampiran_notulen']);
                        echo '<img src="data:image/jpeg;base64,' . $img_data . '" alt="Lampiran" class="img-fluid" style="max-width: 50%; height: auto;">';
                    ?>
                <?php else: ?>
                    Tidak ada lampiran.
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <form method="POST">
    <?php if ($data['issign'] == 0): ?>
        <a href="form_notulen.php?id=<?= $id_notulensi ?>" class="btn btn-warning">Edit</a>
        <button type="submit" name="delete" class="btn btn-danger">Hapus</button>
        <button type="submit" name="sign" class="btn btn-success">Tanda Tangani</button>
    <?php else: ?>
        <a href="cetak_pdf.php?id=<?= $id_notulensi ?>" class="btn btn-primary">Cetak PDF</a>
        <p class="text-warning mt-2">Notulensi ini sudah ditandatangani dan tidak bisa diedit atau dihapus.</p>
    <?php endif; ?>
    <a href="manajemen_notulen.php" class="btn btn-secondary">Kembali ke Manajemen Notulen</a>
</form>

</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
