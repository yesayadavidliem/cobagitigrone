<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include database connection
include('koneksi.php');

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

// Fetch laporan data dengan pencarian dan pagination
$sql = "SELECT laporan.id_laporan, laporan.nomor_lambung, user.username, laporan.keterangan, laporan.lokasi, laporan.tanggal, laporan.isvalid 
        FROM laporan 
        JOIN user ON laporan.username = user.username
        WHERE laporan.isdelete = 0 
        AND (laporan.id_laporan LIKE ? OR laporan.nomor_lambung LIKE ? OR user.username LIKE ?) 
        ORDER BY laporan.tanggal DESC 
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
    <title>Progres Laporan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">OPS TfSM</a>
</nav>

<div class="container mt-5">
    <!-- Tombol Kembali ke Dashboard -->
    <a href="dashboard.php" class="btn btn-primary mb-3">Kembali ke Dashboard</a>
    
    <h3>Progres Laporan</h3>

    <!-- Form Pencarian -->
    <form method="post" class="mb-3">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Cari berdasarkan ID Laporan, Nomor Lambung, atau Username">
        <button type="submit" class="btn btn-primary mt-2">Cari</button>
    </form>

    <!-- Tabel Laporan -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Laporan</th>
                <th>Nomor Lambung</th>
                <th>Username</th>
                <th>Keterangan</th>
                <th>Lokasi</th>
                <th>Tanggal</th>
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
                    <td><?php echo ($laporan['isvalid'] == 1) ? 'Valid' : (($laporan['isvalid'] == 2) ? 'Ditolak' : 'Menunggu Validasi'); ?></td>
                    <td>
                        <?php if ($laporan['isvalid'] == 0): ?>
                            <form method="post" action="progres_laporan.php" style="display:inline;">
                                <input type="hidden" name="id_laporan" value="<?php echo $laporan['id_laporan']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        <?php elseif ($laporan['isvalid'] == 1): ?>
                            <a href="cetak_laporan.php?id=<?php echo $laporan['id_laporan']; ?>" class="btn btn-success btn-sm" target="_blank">Cetak</a>
                        <?php endif; ?>
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
// Handle delete action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_laporan'])) {
    $id_laporan = $_POST['id_laporan'];

    if (isset($_POST['delete'])) {
        // Hapus laporan jika belum tervalidasi
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
