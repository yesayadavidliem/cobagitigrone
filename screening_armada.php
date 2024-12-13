<?php
// Start session
session_start();

// Check if the user is logged in by verifying the 'username' session variable
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include database connection
include('koneksi.php');

// Initialize variables
$search_nomor_lambung = '';
$armada_data = [];
$error_message = '';
$success_message = ''; // Tambahkan pesan sukses

// Check if search form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_nomor_lambung'])) {
    // Get the search input
    $search_nomor_lambung = $_POST['nomor_lambung'];

    // Query to search for exact nomor_lambung in armada where isdelete = 0
    $sql = "SELECT id_armada, nomor_lambung, layanan, koridor, operator 
            FROM armada 
            WHERE nomor_lambung = ? AND isdelete = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_nomor_lambung);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch data if available
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $armada_data[] = $row;
        }
    }
}

// Handle the form submission for the report
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_laporan'])) {
    // Sanitize form input
    $nomor_lambung = $_POST['nomor_lambung'];
    $username = $_SESSION['username'];  // Using 'username' from session
    $keterangan = $_POST['keterangan'];
    $lokasi = $_POST['lokasi'];
    $tanggal = date('Y-m-d H:i:s');
    $isvalid = 0;
    $isdelete = 0;
    $dokumentasi = null;

    // Handle file upload for dokumentasi (optional)
    if (isset($_FILES['dokumentasi']) && $_FILES['dokumentasi']['error'] == 0) {
        // Set upload directory
        $upload_dir = 'uploads/uploads/';
        $file_name = $_FILES['dokumentasi']['name'];
        $file_tmp = $_FILES['dokumentasi']['tmp_name'];
        $file_path = $upload_dir . basename($file_name);

        // Check if the upload directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the uploads folder if it doesn't exist
        }

        // Check if the file is a valid image (optional validation)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['dokumentasi']['type'], $allowed_types)) {
            // Move the uploaded file to the upload directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                $dokumentasi = $file_path;  // Store the file path in the database
            } else {
                $error_message = 'Error uploading file. Check file permissions.';
            }
        } else {
            $error_message = 'Invalid file type. Only images are allowed.';
        }
    }

    // Insert the report into the 'laporan' table if no errors
    if (empty($error_message)) {
        $sql_insert = "INSERT INTO laporan (nomor_lambung, username, keterangan, lokasi, tanggal, dokumentasi, isvalid, isdelete)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);

        // Check for any errors preparing the query
        if (!$stmt_insert) {
            $error_message = 'Error preparing statement: ' . $conn->error;
        } else {
            $stmt_insert->bind_param("ssssssii", $nomor_lambung, $username, $keterangan, $lokasi, $tanggal, $dokumentasi, $isvalid, $isdelete);

            // Execute the query
            if ($stmt_insert->execute()) {
                $success_message = 'Laporan berhasil ditambahkan.';
            } else {
                $error_message = 'Gagal menambahkan laporan: ' . $stmt_insert->error;
            }
        }
    }
}

// Query for report history in the last 30 days with valid status
$history_data = [];
$thirty_days_ago = date('Y-m-d H:i:s', strtotime('-30 days'));

$sql_history = "SELECT id_laporan, keterangan, lokasi, tanggal 
                FROM laporan 
                WHERE nomor_lambung = ? 
                AND isvalid = 1 
                AND isdelete = 0 
                AND tanggal >= ?
                ORDER BY nomor_lambung, tanggal DESC";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("ss", $search_nomor_lambung, $thirty_days_ago);
$stmt_history->execute();
$result_history = $stmt_history->get_result();

// Fetch history data
if ($result_history->num_rows > 0) {
    while ($row_history = $result_history->fetch_assoc()) {
        $history_data[] = $row_history;
    }
}
?>

<!-- HTML Content -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Screening Armada</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1d2d50;
            color: white;
        }
        .navbar {
            background-color: #14213d;
        }
        .navbar-brand {
            color: #ffffff;
        }
        .container {
            margin-top: 50px;
        }
        .form-control {
            font-size: 1rem;
            padding: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .armada-data {
            margin-top: 20px;
            background-color: #333;
            padding: 15px;
            border-radius: 5px;
        }
        .armada-data p {
            margin: 5px 0;
        }
        .btn-back {
            background-color: #007bff;
            color: white;
        }
        .btn-back:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
        .instruction {
            margin-top: 20px;
            font-size: 1.2rem;
            color: #f0f0f0;
        }
        table {
            margin-top: 20px;
            width: 100%;
            background-color: #333;
            border-radius: 5px;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #14213d;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">OPS TfSM</a>
</nav>

<div class="container">
    <!-- Button to go back to Dashboard -->
    <a href="dashboard.php" class="btn btn-back mb-4">Kembali ke Dashboard</a>

    <h2>Screening Armada</h2>

    <!-- Search form -->
    <form method="POST" action="screening_armada.php">
        <div class="form-group">
            <label for="nomor_lambung">Cari Nomor Armada:</label>
            <input type="text" name="nomor_lambung" class="form-control" value="<?php echo htmlspecialchars($search_nomor_lambung); ?>" placeholder="Masukkan nomor armada" required>
        </div>
        <button type="submit" name="search_nomor_lambung" class="btn btn-primary">Cari</button>
    </form>

    <h3 class="mt-4">Hasil Pencarian:</h3>

    <?php
    // Check if any results were found
    if (!empty($armada_data)) {
        foreach ($armada_data as $armada) {
            echo "<div class='armada-data'>
                    <p><strong>ID Armada</strong>: " . $armada['id_armada'] . "</p>
                    <p><strong>Nomor Lambung</strong>: " . $armada['nomor_lambung'] . "</p>
                    <p><strong>Layanan</strong>: " . $armada['layanan'] . "</p>
                    <p><strong>Koridor</strong>: " . $armada['koridor'] . "</p>
                    <p><strong>Operator</strong>: " . $armada['operator'] . "</p>
                  </div>";
        }
    ?>

    <!-- Riwayat Laporan -->
    <h3 class="mt-4">Riwayat Laporan (30 Hari Terakhir):</h3>
    <?php if (!empty($history_data)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Laporan</th>
                    <th>Keterangan</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history_data as $history): ?>
                    <tr>
                        <td><?php echo $history['id_laporan']; ?></td>
                        <td><?php echo $history['keterangan']; ?></td>
                        <td><?php echo $history['lokasi']; ?></td>
                        <td><?php echo $history['tanggal']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada riwayat laporan dalam 30 hari terakhir.</p>
    <?php endif; ?>
    <div class="instruction">
        <h4>Mohon untuk dipastikan:</h4>
        <ul>
            <li>Spedometer berfungsi dengan baik</li>
            <li>AC mengeluarkan angin dingin</li>
            <li>APAR tersedia dan berfungsi dengan normal (tekanan APAR berada di ruas hijau)</li>
            <li>Penomoran lambung sesuai dan mudah terbaca, dan tersedia di depan, belakang, dan samping armada</li>
            <li>Dilengkapi P3K</li>
            <li>Asap knalpot tidak wajar (keluar terus menerus dalam waktu lama, dan atau asap bewarna putih)</li>
            <li>Laporkan jika melihat hal-hal di luar yang normal pada kendaraan</li>
        </ul>
    </div>
    <h3>Ada kendala pada armada? Yuk laporkan :</h3>

    <!-- Form to submit the report -->
    <form method="POST" action="screening_armada.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="keterangan">Keterangan:</label>
            <textarea name="keterangan" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="lokasi">Lokasi:</label>
            <input type="text" name="lokasi" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="dokumentasi">Dokumentasi (Opsional):</label>
            <input type="file" name="dokumentasi" class="form-control">
        </div>
        <input type="hidden" name="nomor_lambung" value="<?php echo $armada['nomor_lambung']; ?>">
        <button type="submit" name="submit_laporan" class="btn btn-success">Kirim Laporan</button>
    </form>

    <?php
    } else {
        echo "<p>Tidak ada data yang ditemukan.</p>";
    }
    ?>

    <!-- Display success message -->
    <?php if (!empty($success_message)): ?>
        <p class="success-message"><?php echo $success_message; ?></p>
    <?php endif; ?>

    <!-- Display error message -->
    <?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
