<?php
session_start();

// Sertakan koneksi
include('koneksi.php');

// Pesan notifikasi
$message = "";

// Validasi jika user sudah login dan memiliki previlage 'admin'
if (!isset($_SESSION['username']) || $_SESSION['previlage'] !== 'admin') {
    header('Location: index.php'); // Redirect jika tidak memiliki previlage yang diperlukan
    exit();
}

// Proses penambahan data kelompok
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nama_kelompok = trim($_POST['nama_kelompok']);
    $epic_kelompok = trim($_POST['epic_kelompok']);
    
    // Validasi input
    if (!empty($nama_kelompok) && !empty($epic_kelompok)) {
        $nama_kelompok = $conn->real_escape_string($nama_kelompok);
        $epic_kelompok = $conn->real_escape_string($epic_kelompok);
        
        $sql = "INSERT INTO kelompok (nama_kelompok, epic_kelompok, isdelete) VALUES ('$nama_kelompok', '$epic_kelompok', 0)";
        if ($conn->query($sql) === TRUE) {
            $message = "Kelompok berhasil ditambahkan!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Semua kolom wajib diisi!";
    }
}

// Proses edit data kelompok
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id_kelompok = intval($_POST['id_kelompok']);
    $nama_kelompok = trim($_POST['nama_kelompok']);
    $epic_kelompok = trim($_POST['epic_kelompok']);
    
    // Validasi input
    if (!empty($nama_kelompok) && !empty($epic_kelompok)) {
        $nama_kelompok = $conn->real_escape_string($nama_kelompok);
        $epic_kelompok = $conn->real_escape_string($epic_kelompok);
        
        $sql = "UPDATE kelompok SET nama_kelompok = '$nama_kelompok', epic_kelompok = '$epic_kelompok' WHERE id_kelompok = $id_kelompok AND isdelete = 0";
        if ($conn->query($sql) === TRUE) {
            $message = "Kelompok berhasil diperbarui!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Semua kolom wajib diisi!";
    }
}

// Proses hapus data kelompok
if (isset($_GET['delete'])) {
    $id_kelompok = intval($_GET['delete']);
    $sql = "UPDATE kelompok SET isdelete = 1 WHERE id_kelompok = $id_kelompok";
    if ($conn->query($sql) === TRUE) {
        $message = "Kelompok berhasil dihapus!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Query untuk mendapatkan data kelompok
$sql_kelompok = "
    SELECT id_kelompok, nama_kelompok, epic_kelompok
    FROM kelompok
    WHERE isdelete = 0
";
$result_kelompok = $conn->query($sql_kelompok);

// Cek apakah $result_kelompok adalah objek yang valid
if (!$result_kelompok) {
    $message = "Error dalam mengambil data kelompok: " . $conn->error;
}

// Query untuk mendapatkan daftar pembina
$sql_pembina = "SELECT id_user, username FROM user WHERE previlage = 'pembina' AND isdelete = 0";
$result_pembina = $conn->query($sql_pembina);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Tambah/Edit Kelompok</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Tambah/Edit Kelompok</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="id_kelompok" id="id_kelompok">
        <div class="form-group">
            <label for="nama_kelompok">Nama Kelompok</label>
            <input type="text" class="form-control" id="nama_kelompok" name="nama_kelompok" required>
        </div>
        <div class="form-group">
            <label for="epic_kelompok">Epic Kelompok</label>
            <select class="form-control" id="epic_kelompok" name="epic_kelompok" required>
                <option value="">Pilih Epic</option>
                <option value="Epic 1">Epic 1</option>
                <option value="Epic 2">Epic 2</option>
                <option value="Epic 3">Epic 3</option>
                <option value="Epic 4">Epic 4</option>
                <option value="Epic 5">Epic 5</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" name="add">Tambah Kelompok</button>
        <button type="submit" class="btn btn-success" name="edit">Edit Kelompok</button>
        <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </form>

    <h3 class="mt-5">Daftar Kelompok</h3>
    <table class="table table-bordered table-hover mt-3">
        <thead>
        <tr>
            <th>ID Kelompok</th>
            <th>Nama Kelompok</th>
            <th>Epic Kelompok</th>
            <th>Pembina</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result_kelompok && $result_kelompok->num_rows > 0): ?>
            <?php while ($row = $result_kelompok->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_kelompok'] ?></td>
                    <td><?= htmlspecialchars($row['nama_kelompok']) ?></td>
                    <td><?= htmlspecialchars($row['epic_kelompok']) ?></td>
                    <td>
                        <?php
                        $sql_pembina_kelompok = "
                            SELECT GROUP_CONCAT(u.username) AS pembina
                            FROM user u
                            WHERE u.id_user IN (
                                SELECT id_user FROM user WHERE nama_kelompok = '{$row['nama_kelompok']}' AND isdelete = 0
                            )
                        ";
                        $result_pembina_kelompok = $conn->query($sql_pembina_kelompok);
                        if ($result_pembina_kelompok && $result_pembina_kelompok->num_rows > 0) {
                            $pembina = $result_pembina_kelompok->fetch_assoc();
                            echo htmlspecialchars($pembina['pembina']);
                        }
                        ?>
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="editKelompok(<?= $row['id_kelompok'] ?>, '<?= htmlspecialchars($row['nama_kelompok']) ?>', '<?= htmlspecialchars($row['epic_kelompok']) ?>')">Edit</button>
                        <a href="?delete=<?= $row['id_kelompok'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kelompok ini?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">Belum ada data kelompok</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function editKelompok(id, nama, epic) {
    document.getElementById('id_kelompok').value = id;
    document.getElementById('nama_kelompok').value = nama;
    document.getElementById('epic_kelompok').value = epic;
}
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
