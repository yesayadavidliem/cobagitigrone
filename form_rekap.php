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

// Pesan notifikasi
$message = "";

// Proses tambah rekap
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nama_rekap = trim($_POST['nama_rekap']);
    $due_date = trim($_POST['due_date']);

    if (!empty($nama_rekap) && !empty($due_date)) {
        // Insert ke tabel rekap
        $sql = "INSERT INTO rekap (nama_rekap, due_date, isdelete) VALUES ('$nama_rekap', '$due_date', 0)";
        if ($conn->query($sql)) {
            $id_rekap = $conn->insert_id;

            // Insert ke tabel poinrekap untuk semua kelompok
            $sql_kelompok = "SELECT id_kelompok FROM kelompok WHERE isdelete = 0";
            $result_kelompok = $conn->query($sql_kelompok);

            while ($row = $result_kelompok->fetch_assoc()) {
                $id_kelompok = $row['id_kelompok'];
                $sql_poinrekap = "INSERT INTO poinrekap (id_rekap, id_kelompok, keaktifan, medsos, sate, jiwa_baru, total, isdelete)
                                VALUES ('$id_rekap', '$id_kelompok', 0, 0, 0, 0, 0, 0)";
                $conn->query($sql_poinrekap);
            }

            $message = "Rekap berhasil ditambahkan!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Semua kolom wajib diisi!";
    }
}

// Proses edit rekap
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id_rekap = intval($_POST['id_rekap']);
    $nama_rekap = trim($_POST['nama_rekap']);
    $due_date = trim($_POST['due_date']);

    if (!empty($nama_rekap) && !empty($due_date)) {
        // Update tabel rekap
        $sql = "UPDATE rekap SET nama_rekap = '$nama_rekap', due_date = '$due_date' WHERE id_rekap = $id_rekap AND isdelete = 0";
        if ($conn->query($sql)) {
            // Hapus data dari poinrekap yang terkait
            $sql_delete_poinrekap = "UPDATE poinrekap SET isdelete = 1 WHERE id_rekap = $id_rekap";
            $conn->query($sql_delete_poinrekap);

            // Insert ulang untuk semua kelompok terkait
            $sql_kelompok = "SELECT id_kelompok FROM kelompok WHERE isdelete = 0";
            $result_kelompok = $conn->query($sql_kelompok);

            while ($row = $result_kelompok->fetch_assoc()) {
                $id_kelompok = $row['id_kelompok'];
                $sql_poinrekap = "INSERT INTO poinrekap (id_rekap, id_kelompok, keaktifan, medsos, sate, jiwa_baru, total, isdelete)
                                VALUES ('$id_rekap', '$id_kelompok', 0, 0, 0, 0, 0, 0)";
                $conn->query($sql_poinrekap);
            }

            $message = "Rekap berhasil diperbarui!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Semua kolom wajib diisi!";
    }
}

// Proses hapus rekap
if (isset($_GET['delete'])) {
    $id_rekap = intval($_GET['delete']);

    // Hapus dari tabel rekap
    $sql_delete_rekap = "UPDATE rekap SET isdelete = 1 WHERE id_rekap = $id_rekap";
    if ($conn->query($sql_delete_rekap)) {
        // Hapus dari tabel poinrekap yang memiliki id_rekap yang sesuai
        $sql_delete_poinrekap = "UPDATE poinrekap SET isdelete = 1 WHERE id_rekap = $id_rekap";
        $conn->query($sql_delete_poinrekap);

        $message = "Rekap dan poinrekap terkait berhasil dihapus!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Query untuk mendapatkan data rekap
$sql_rekap = "SELECT id_rekap, nama_rekap, due_date FROM rekap WHERE isdelete = 0";
$result_rekap = $conn->query($sql_rekap);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Rekap</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Manajemen Rekap</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Form tambah/edit -->
    <form method="POST" id="form-rekap">
        <input type="hidden" name="id_rekap" id="id_rekap">
        <div class="form-group">
            <label for="nama_rekap">Nama Rekap</label>
            <input type="text" class="form-control" id="nama_rekap" name="nama_rekap" required>
        </div>
        <div class="form-group">
            <label for="due_date">Due Date</label>
            <input type="date" class="form-control" id="due_date" name="due_date" required>
        </div>
        <button type="submit" class="btn btn-primary" name="add">Tambah Rekap</button>
        <button type="submit" class="btn btn-success" name="edit">Edit Rekap</button>
        <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </form>

    <h3 class="mt-4">Daftar Rekap</h3>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Rekap</th>
                <th>Due Date</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_rekap->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id_rekap']) ?></td>
                    <td><?= htmlspecialchars($row['nama_rekap']) ?></td>
                    <td><?= htmlspecialchars($row['due_date']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="editRekap(<?= $row['id_rekap'] ?>, '<?= htmlspecialchars($row['nama_rekap']) ?>', '<?= htmlspecialchars($row['due_date']) ?>')">Edit</button>
                        <a href="?delete=<?= $row['id_rekap'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus rekap ini?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
function editRekap(id, nama, due) {
    document.getElementById('id_rekap').value = id;
    document.getElementById('nama_rekap').value = nama;
    document.getElementById('due_date').value = due;
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
