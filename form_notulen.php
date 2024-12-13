<?php
// Start the session
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include database connection
include('koneksi.php');

// Initialize variables for error and success messages
$error_message = "";
$success_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $username = $_SESSION['username'];
    $judul_notulen = trim($_POST['judul_notulen']);
    $cc_notulen = trim($_POST['cc_notulen']);
    $isi_notulen = trim($_POST['isi_notulen']);
    $issign = 0;
    $isdelete = 0;

    // Handle file upload (optional)
    $lampiran_notulen = null;
    if (!empty($_FILES['lampiran_notulen']['name'])) {
        if ($_FILES['lampiran_notulen']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['lampiran_notulen']['tmp_name'];
            $file_size = $_FILES['lampiran_notulen']['size'];

            // Limit file size to 2MB
            if ($file_size > 2 * 1024 * 1024) {
                $error_message = "Lampiran tidak boleh lebih dari 2MB.";
            } else {
                $lampiran_notulen = file_get_contents($file_tmp); // Read file content
            }
        } else {
            $error_message = "Terjadi kesalahan saat mengunggah lampiran.";
        }
    }

    // Validasi input wajib
    if (empty($judul_notulen) || empty($isi_notulen)) {
        $error_message = "Judul Notulen dan Isi Notulen wajib diisi.";
    } elseif (empty($error_message)) {
        // Insert data ke database
        $sql_insert = "INSERT INTO notulensi (username, judul_notulen, cc_notulen, isi_notulen, lampiran_notulen, issign, isdelete)
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);

        // Check preparation status
        if ($stmt === false) {
            $error_message = "Error prepare statement: " . $conn->error;
        } else {
            // Bind parameter
            $stmt->bind_param("ssssbii", $username, $judul_notulen, $cc_notulen, $isi_notulen, $lampiran_notulen, $issign, $isdelete);

            // Send data BLOB (lampiran)
            if ($lampiran_notulen !== null) {
                $stmt->send_long_data(4, $lampiran_notulen); // Indeks ke-4 untuk lampiran_notulen
            }

            // Eksekusi statement
            if ($stmt->execute()) {
                $success_message = "Notulensi berhasil ditambahkan.";
            } else {
                $error_message = "Terjadi kesalahan: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Notulensi</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1d2d50;
            color: white;
        }
        .container {
            margin-top: 50px;
        }
        .form-control, .btn {
            margin-bottom: 15px;
        }
        .btn-primary {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }
        .btn-primary:hover {
            background-color: #5a32a0;
            border-color: #512a8b;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>Form Manajemen Notulensi</h3>
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <!-- Username (from session, readonly) -->
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" class="form-control" value="<?php echo $_SESSION['username']; ?>" readonly>
        </div>

        <!-- Judul Notulen (required) -->
        <div class="form-group">
            <label for="judul_notulen">Judul Notulen <span style="color: red;">*</span></label>
            <input type="text" id="judul_notulen" name="judul_notulen" class="form-control" placeholder="Masukkan judul notulen" required>
        </div>

        <!-- CC Notulen (optional) -->
        <div class="form-group">
            <label for="cc_notulen">CC</label>
            <input type="text" id="cc_notulen" name="cc_notulen" class="form-control" placeholder="Masukkan CC jika ada">
        </div>

        <!-- Isi Notulen (required) -->
        <div class="form-group">
            <label for="isi_notulen">Isi Notulen <span style="color: red;">*</span></label>
            <textarea id="isi_notulen" name="isi_notulen" rows="5" class="form-control" placeholder="Masukkan isi notulen" required></textarea>
        </div>

        <!-- Lampiran Notulen (optional) -->
        <div class="form-group">
            <label for="lampiran_notulen">Lampiran (Opsional)</label>
            <input type="file" id="lampiran_notulen" name="lampiran_notulen" class="form-control">
            <small class="text-muted">Ukuran maksimal 2MB. Format: JPG, PNG, PDF.</small>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Simpan Notulensi</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
