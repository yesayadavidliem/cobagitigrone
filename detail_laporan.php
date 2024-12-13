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

// Get the report ID from the URL
if (isset($_GET['id'])) {
    $id_laporan = $_GET['id'];

    // Fetch laporan data with join to armada table and sorting by isvalid
    $sql = "SELECT laporan.*, armada.layanan, armada.koridor, armada.operator 
            FROM laporan
            LEFT JOIN armada ON laporan.nomor_lambung = armada.nomor_lambung 
            WHERE laporan.id_laporan = ? AND laporan.isdelete = 0
            ORDER BY laporan.isvalid ASC";  // Sorting by isvalid (0 will be shown first)
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_laporan);
    $stmt->execute();
    $result = $stmt->get_result();
    $laporan = $result->fetch_assoc();

    if (!$laporan) {
        echo "Laporan tidak ditemukan.";
        exit();
    }
} else {
    echo "Laporan ID tidak ditemukan.";
    exit();
}

// Handle form submissions for validation, rejection, and deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_laporan = $_GET['id'];

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan #<?php echo $laporan['id_laporan']; ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-group {
            margin-bottom: 10px; /* Mengurangi jarak antar elemen form */
        }
        .col-form-label {
            font-weight: bold;
        }
        .container {
            padding-left: 10px;
            padding-right: 10px;
        }
        .col-sm-9 p {
            margin-bottom: 0; /* Mengurangi jarak antar elemen p */
        }
        .form-group.row {
            align-items: center; /* Menyelaraskan label dan input dalam satu baris */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">OPS TfSM</a>
</nav>

<div class="container mt-5">
    <h3>Detail Laporan #<?php echo $laporan['id_laporan']; ?></h3>

    <!-- Show status message -->
    <?php if (isset($_SESSION['status'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['status']; unset($_SESSION['status']); ?>
        </div>
    <?php endif; ?>

    <!-- Display laporan details -->
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Pelapor:</label>
        <div class="col-sm-9">
            <p><?php echo $laporan['username']; ?></p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Nomor Lambung:</label>
        <div class="col-sm-9">
            <p><?php echo $laporan['nomor_lambung']; ?></p>
        </div>
    </div>
    <!-- Armada Details -->
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Layanan:</label>
        <div class="col-sm-9">
            <p><?php echo $laporan['layanan']; ?></p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Koridor:</label>
        <div class="col-sm-9">
            <p><?php echo $laporan['koridor']; ?></p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Operator:</label>
        <div class="col-sm-9">
            <p><?php echo $laporan['operator']; ?></p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Keterangan:</label>
        <div class="col-sm-9">
            <p><?php echo $laporan['keterangan']; ?></p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Lokasi:</label>
        <div class="col-sm-9">
            <p><?php echo $laporan['lokasi']; ?></p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Tanggal:</label>
        <div class="col-sm-9">
            <p><?php echo $laporan['tanggal']; ?></p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Dokumentasi:</label>
        <div class="col-sm-9">
            <img src="<?php echo $laporan['dokumentasi']; ?>" alt="Dokumentasi" style="max-width: 40%; height: auto;">
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Status Validasi:</label>
        <div class="col-sm-9">
            <p><?php echo ($laporan['isvalid'] == 1) ? 'Valid' : (($laporan['isvalid'] == 2) ? 'Ditolak' : 'Menunggu Validasi'); ?></p>
        </div>
    </div>

    <!-- Actions for validation, rejection, and deletion -->
    <form method="post" action="detail_laporan.php?id=<?php echo $laporan['id_laporan']; ?>">
        <div class="form-group">
            <button type="submit" name="validate" class="btn btn-success btn-sm">Validasi</button>
            <button type="submit" name="reject" class="btn btn-danger btn-sm">Tolak</button>
            <button type="submit" name="delete" class="btn btn-warning btn-sm">Hapus</button>
        </div>
    </form>

    <!-- Display print buttons if the report is valid (isvalid = 1) and not deleted (isdelete = 0) -->
    <?php if ($laporan['isvalid'] == 1 && $laporan['isdelete'] == 0): ?>
    <div class="d-flex justify-content-start gap-4"> <!-- Meningkatkan gap -->
        <form method="post" action="cetak_laporan.php?id=<?php echo $laporan['id_laporan']; ?>">
            <button type="submit" name="generate_pdf" class="btn btn-primary btn-sm">Cetak dengan Nama Pelapor</button>
        </form>

        <form method="post" action="cetak_laporan_tanpa_nama.php?id=<?php echo $laporan['id_laporan']; ?>">
            <button type="submit" name="generate_pdf_without_name" class="btn btn-secondary btn-sm">Cetak Tanpa Nama Pelapor</button>
        </form>
    </div>
<?php endif; ?>




    <a href="manajemen_laporan.php" class="btn btn-primary mt-3">Kembali ke Manajemen Laporan</a>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
