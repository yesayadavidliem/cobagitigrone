<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['id_user']) || !isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Periksa apakah pengguna memiliki previlege admin
if ($_SESSION['previlage'] !== 'admin') {
    echo "<script>alert('Akses ditolak! Halaman ini hanya untuk admin.'); window.location.href='index.php';</script>";
    exit();
}

// Ambil data sesi
$id_user = $_SESSION['id_user'];
$username = $_SESSION['username'];
$previlage = $_SESSION['previlage'];

// Sertakan koneksi
include('koneksi.php');

// Pesan notifikasi
$message = "";

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $nama_kelompok = trim($_POST['nama_kelompok']);
    $previlage = trim($_POST['previlage']);

    // Validasi input
    if (!empty($username) && !empty($password) && !empty($nama_kelompok) && !empty($previlage)) {
        // Enkripsi password
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Insert ke tabel user
        $sql = "INSERT INTO user (username, password, nama_kelompok, previlage, isdelete) 
                VALUES ('$username', '$password_hashed', '$nama_kelompok', '$previlage', 0)";
        if ($conn->query($sql) === TRUE) {
            $message = "Registrasi berhasil!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Semua kolom wajib diisi!";
    }
}

// Proses edit data user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id_user = intval($_POST['id_user']);
    $username = trim($_POST['username']);
    $nama_kelompok = trim($_POST['nama_kelompok']);
    $previlage = trim($_POST['previlage']);
    
    // Update data tanpa password
    if (!empty($username) && !empty($nama_kelompok) && !empty($previlage)) {
        $sql = "UPDATE user SET username = '$username', nama_kelompok = '$nama_kelompok', previlage = '$previlage' WHERE id_user = $id_user AND isdelete = 0";
        if ($conn->query($sql) === TRUE) {
            $message = "Data user berhasil diperbarui!";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "Semua kolom wajib diisi!";
    }
}

// Proses hapus data user
if (isset($_GET['delete'])) {
    $id_user = intval($_GET['delete']);
    $sql = "UPDATE user SET isdelete = 1 WHERE id_user = $id_user";
    if ($conn->query($sql) === TRUE) {
        $message = "User berhasil dihapus!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Query untuk dropdown nama_kelompok
$sql_kelompok = "SELECT nama_kelompok FROM kelompok WHERE isdelete = 0";
$result_kelompok = $conn->query($sql_kelompok);

// Query untuk mendapatkan data user
$sql_user = "SELECT id_user, username, nama_kelompok, previlage FROM user WHERE isdelete = 0";
$result_user = $conn->query($sql_user);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Register/Edit User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h3>Form Registrasi/Edit User</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Form Registrasi/Edit -->
    <form method="POST">
        <input type="hidden" name="id_user" id="id_user">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password">
            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
        </div>
        <div class="form-group">
            <label for="nama_kelompok">Nama Kelompok</label>
            <select class="form-control" id="nama_kelompok" name="nama_kelompok" required>
                <option value="">Pilih Kelompok</option>
                <?php while ($row = $result_kelompok->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['nama_kelompok']) ?>"><?= htmlspecialchars($row['nama_kelompok']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="previlage">Previlege</label>
            <select class="form-control" id="previlage" name="previlage" required>
                <option value="">Pilih Previlege</option>
                <option value="admin">Admin</option>
                <option value="medsos">Medsos</option>
                <option value="user">User</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success" name="add">Tambah User</button>
        <button type="submit" class="btn btn-info" name="edit">Edit User</button>
        <a href="index.php" class="btn btn-secondary">Kembali Ke Dashboard</a>
    </form>

    <h3 class="mt-5">Daftar User</h3>
    <table class="table table-bordered table-hover mt-3">
        <thead>
        <tr>
            <th>ID User</th>
            <th>Username</th>
            <th>Nama Kelompok</th>
            <th>Previlege</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result_user->num_rows > 0): ?>
            <?php while ($row = $result_user->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_user'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kelompok']) ?></td>
                    <td><?= htmlspecialchars($row['previlage']) ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" onclick="editUser(<?= $row['id_user'] ?>, '<?= htmlspecialchars($row['username']) ?>', '<?= htmlspecialchars($row['nama_kelompok']) ?>', '<?= htmlspecialchars($row['previlage']) ?>')">Edit</button>
                        <a href="?delete=<?= $row['id_user'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">Belum ada data user</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function editUser(id, username, kelompok, previlage) {
    document.getElementById('id_user').value = id;
    document.getElementById('username').value = username;
    document.getElementById('nama_kelompok').value = kelompok;
    document.getElementById('previlage').value = previlage;
}
</script>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
