<?php
// Start the session
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Include database connection
include("koneksi.php");

// Ambil input pencarian jika ada
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Set jumlah laporan per halaman
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;  // Default 10 laporan per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;  // Default halaman pertama

// Hitung offset berdasarkan halaman yang dipilih
$offset = ($page - 1) * $limit;

// Fetch laporan data dengan pencarian dan pagination, diurutkan berdasarkan status validasi dan tanggal terbaru
$sql = "SELECT laporan.id_laporan, laporan.nomor_lambung, user.username, laporan.keterangan, laporan.lokasi, laporan.tanggal, laporan.dokumentasi, laporan.isvalid 
        FROM laporan 
        JOIN user ON laporan.username = user.username
        WHERE laporan.isdelete = 0 
        AND (laporan.id_laporan LIKE ? OR laporan.nomor_lambung LIKE ? OR user.username LIKE ?) 
        ORDER BY laporan.isvalid ASC, laporan.tanggal DESC 
        LIMIT ?, ?";  // Pencarian, sorting, dan pagination

$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param("sssii", $searchTerm, $searchTerm, $searchTerm, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Hitung total laporan untuk pagination
$sqlTotal = "SELECT COUNT(*) AS total FROM laporan WHERE isdelete = 0 AND (id_laporan LIKE ? OR nomor_lambung LIKE ? OR username LIKE ?)";
$stmtTotal = $conn->prepare($sqlTotal);
$stmtTotal->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmtTotal->execute();
$totalResult = $stmtTotal->get_result()->fetch_assoc();
$totalLaporan = $totalResult['total'];
$totalPages = ceil($totalLaporan / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Laporan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">OPS TfSM</a>
</nav>

<div class="container mt-5">
    <!-- Tombol Kembali ke Dashboard -->
    <a href="dashboard.php" class="btn btn-primary mb-3">Kembali ke Dashboard</a>
    
    <h3>Manajemen Laporan</h3>

    <!-- Form Pencarian -->
    <form method="post" class="mb-3">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Cari berdasarkan ID Laporan, Nomor Lambung, atau Username">
        <button type="submit" class="btn btn-primary mt-2">Cari</button>
    </form>

    <!-- Dropdown untuk memilih jumlah laporan per halaman -->
    <form method="get" class="mb-3">
        <select name="limit" class="form-control w-auto d-inline-block" onchange="this.form.submit()">
            <option value="10" <?php if ($limit == 10) echo 'selected'; ?>>10 per halaman</option>
            <option value="25" <?php if ($limit == 25) echo 'selected'; ?>>25 per halaman</option>
            <option value="50" <?php if ($limit == 50) echo 'selected'; ?>>50 per halaman</option>
            <option value="100" <?php if ($limit == 100) echo 'selected'; ?>>100 per halaman</option>
            <option value="0" <?php if ($limit == 0) echo 'selected'; ?>>Tampilkan Semua</option>
        </select>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Laporan</th>
                <th>Nomor Lambung</th>
                <th>Username</th>
                <th>Keterangan</th>
                <th>Lokasi</th>
                <th>Tanggal</th>
                <th>Dokumentasi</th>
                <th>Status Validasi</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($laporan = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $laporan['id_laporan']; ?></td>
                    <td><?php echo $laporan['nomor_lambung']; ?></td>
                    <td><?php echo $laporan['username']; ?></td>
                    <td><?php echo $laporan['keterangan']; ?></td>
                    <td><?php echo $laporan['lokasi']; ?></td>
                    <td><?php echo $laporan['tanggal']; ?></td>
                    <td>
                        <?php
                        // Path gambar hanya mengarah ke folder uploads/
                        $imagePath = '' . $laporan['dokumentasi']; // Pastikan path sesuai
                        ?>
                        <?php if (file_exists($imagePath)): ?>
                            <a href="<?php echo $imagePath; ?>" target="_blank">
                                <img src="<?php echo $imagePath; ?>" alt="Dokumentasi" width="100" height="auto">
                            </a>
                        <?php else: ?>
                            <p>Gambar tidak tersedia</p>
                        <?php endif; ?>
                    </td>
                    <td><?php echo ($laporan['isvalid'] == 1) ? 'Valid' : (($laporan['isvalid'] == 2) ? 'Ditolak' : 'Menunggu Validasi'); ?></td>
                    <td>
                        <a href="detail_laporan.php?id=<?php echo $laporan['id_laporan']; ?>" class="btn btn-primary btn-sm">Detail</a>
                        <form method="post" action="manajemen_laporan.php" style="display:inline;">
                            <input type="hidden" name="id_laporan" value="<?php echo $laporan['id_laporan']; ?>">
                            <?php if ($laporan['isvalid'] == 0): ?>
                                <button type="submit" name="validate" class="btn btn-success btn-sm">Validasi</button>
                                <button type="submit" name="reject" class="btn btn-danger btn-sm">Tolak</button>
                            <?php endif; ?>
                            <button type="submit" name="delete" class="btn btn-warning btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?limit=<?php echo $limit; ?>&page=1&search=<?php echo htmlspecialchars($search); ?>">First</a></li>
                <li class="page-item"><a class="page-link" href="?limit=<?php echo $limit; ?>&page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Prev</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                    <a class="page-link" href="?limit=<?php echo $limit; ?>&page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($search); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?limit=<?php echo $limit; ?>&page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($search); ?>">Next</a></li>
                <li class="page-item"><a class="page-link" href="?limit=<?php echo $limit; ?>&page=<?php echo $totalPages; ?>&search=<?php echo htmlspecialchars($search); ?>">Last</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Handle form submissions for validation, rejection, and deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_laporan'])) {
    $id_laporan = $_POST['id_laporan'];

    if (isset($_POST['validate'])) {
        $sql = "UPDATE laporan SET isvalid = 1 WHERE id_laporan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_laporan);
        $stmt->execute();
        $_SESSION['status'] = 'Laporan telah divalidasi!';
    } elseif (isset($_POST['reject'])) {
        $sql = "UPDATE laporan SET isvalid = 2 WHERE id_laporan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_laporan);
        $stmt->execute();
        $_SESSION['status'] = 'Laporan telah ditolak!';
    } elseif (isset($_POST['delete'])) {
        $sql = "UPDATE laporan SET isdelete = 1 WHERE id_laporan = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_laporan);
        $stmt->execute();
        $_SESSION['status'] = 'Laporan telah dihapus!';
    }
}

// Close the database connection
$conn->close();
?>
