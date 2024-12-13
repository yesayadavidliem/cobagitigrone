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

// Ambil data sesi
$nama_kelompok = $_SESSION['nama_kelompok'];

// Query untuk mendapatkan data rekap dengan join ke tabel poinrekap dan kelompok, dan diurutkan berdasarkan due_date paling baru
$sql = "
    SELECT r.id_rekap, r.nama_rekap, r.due_date, 
           pr.id_poinrekap, k.nama_kelompok, pr.keaktifan, pr.medsos, pr.sate, pr.jiwa_baru, pr.total
    FROM rekap r
    LEFT JOIN poinrekap pr ON r.id_rekap = pr.id_rekap
    LEFT JOIN kelompok k ON pr.id_kelompok = k.id_kelompok
    WHERE pr.isdelete = 0 AND k.nama_kelompok = ?
    ORDER BY r.due_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $nama_kelompok);
$stmt->execute();
$result = $stmt->get_result();

// Mengambil total keseluruhan dari kelompok
$total_keseluruhan_sql = "
    SELECT SUM(total) AS total_keseluruhan
    FROM poinrekap
    WHERE id_kelompok IN (SELECT id_kelompok FROM kelompok WHERE nama_kelompok = ?)
    AND isdelete = 0
";
$total_stmt = $conn->prepare($total_keseluruhan_sql);
$total_stmt->bind_param("s", $nama_kelompok);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_keseluruhan = $total_row['total_keseluruhan'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Poin</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Mengatur tabel agar lebih simetris */
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        /* Menyelaraskan elemen input */
        .table input[type="number"] {
            width: 100%; /* Sesuaikan dengan kolom */
            max-width: 80px; /* Batasi lebar maksimum */
            text-align: center;
            padding: 0;
            margin: auto; /* Pastikan input berada di tengah */
            box-sizing: border-box; /* Pastikan padding tidak memengaruhi ukuran */
        }
        /* Tambahan untuk tampilan tombol */
        .btn {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h3 class="text-center">Input Poin</h3>

    <!-- Tampilkan total keseluruhan di atas tabel -->
    <div class="text-left mb-3">
    <h4>Total Keseluruhan Poin <?= htmlspecialchars($nama_kelompok) ?> : <?= number_format($total_keseluruhan, 0, ',', '.') ?></h4>
    </div>

    <table class="table table-bordered table-hover text-center">
        <thead class="thead-light">
            <tr>
                <th>ID Rekap</th>
                <th>ID Poin Rekap</th>
                <th>Nama Rekap</th>
                <th>Due Date</th>
                <th>Nama Kelompok</th>
                <th>Keaktifan</th>
                <th>Medsos</th>
                <th>Sate</th>
                <th>Jiwa Baru</th>
                <th>Total</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $due_date = new DateTime($row['due_date']);
                $today = new DateTime();
                $is_past_due = $due_date < $today;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_rekap']) ?></td>
                    <td><?= htmlspecialchars($row['id_poinrekap']) ?></td>
                    <td><?= htmlspecialchars($row['nama_rekap']) ?></td>
                    <td><?= htmlspecialchars($row['due_date']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kelompok']) ?></td>
                    <td>
                        <form action="update_poinrekap.php" method="post">
                            <input type="hidden" name="id_poinrekap" value="<?= $row['id_poinrekap'] ?>">
                            <input type="number" name="keaktifan" value="<?= $row['keaktifan'] ?>" <?= $is_past_due ? 'readonly' : '' ?> required>
                    </td>
                    <td><?= htmlspecialchars($row['medsos']) ?></td>
                    <td>
                        <input type="number" name="sate" value="<?= $row['sate'] ?>" <?= $is_past_due ? 'readonly' : '' ?> required>
                    </td>
                    <td>
                        <input type="number" name="jiwa_baru" value="<?= $row['jiwa_baru'] ?>" <?= $is_past_due ? 'readonly' : '' ?> required>
                    </td>
                    <td><?= htmlspecialchars($row['total']) ?></td>
                    <td>
                        <button type="submit" class="btn btn-primary" <?= $is_past_due ? 'disabled' : '' ?>>Edit</button>
                    </td>
                    </form>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
