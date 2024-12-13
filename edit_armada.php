<?php
// Start session
session_start();

// Aktifkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Include database connection
include('koneksi.php');

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Validate ID parameter
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_armada = intval($_GET['id']);

    // Fetch existing data using prepared statement
    $stmt = $conn->prepare("SELECT * FROM armada WHERE id_armada = ? AND isdelete = 0");
    $stmt->bind_param("i", $id_armada);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "<script>alert('Armada not found or already deleted.'); window.location = 'manajemen_armada.php';</script>";
        exit();
    }
    $stmt->close();
} else {
    echo "<script>alert('Invalid or missing ID.'); window.location = 'manajemen_armada.php';</script>";
    exit();
}

// Update the data when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_armada'])) {
    // Sanitize form inputs
    $nomor_lambung = sanitize_input($_POST['nomor_lambung']);
    $layanan = sanitize_input($_POST['layanan']);
    $koridor = sanitize_input($_POST['koridor']);
    $operator = sanitize_input($_POST['operator']);

    // Update data using prepared statement
    $stmt = $conn->prepare("UPDATE armada SET nomor_lambung = ?, layanan = ?, koridor = ?, operator = ? WHERE id_armada = ?");
    $stmt->bind_param("ssssi", $nomor_lambung, $layanan, $koridor, $operator, $id_armada);

    if ($stmt->execute()) {
        echo "<script>alert('Armada updated successfully.'); window.location = 'manajemen_armada.php';</script>";
    } else {
        echo "<script>alert('Error updating armada: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// Dropdown query untuk layanan, koridor, dan operator
$layanan_query = "SELECT layanan FROM layanan WHERE isdelete = 0";
$koridor_query = "SELECT koridor FROM koridor WHERE isdelete = 0";
$operator_query = "SELECT operator FROM operator WHERE isdelete = 0";

$layanan_result = $conn->query($layanan_query);
$koridor_result = $conn->query($koridor_query);
$operator_result = $conn->query($operator_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Armada</title>
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
        .btn-primary, .btn-secondary {
            font-size: 1rem;
            padding: 10px 20px;
            text-align: center;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .form-control {
            font-size: 1rem;
            padding: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">OPS TfSM</a>
</nav>

<div class="container">
    <div class="back-to-dashboard">
        <a href="manajemen_armada.php" class="btn btn-secondary">Kembali ke Manajemen Armada</a>
    </div>

    <h2>Edit Armada</h2>
    
    <form method="POST">
        <div class="form-group">
            <label for="nomor_lambung">Nomor Lambung:</label>
            <input type="text" name="nomor_lambung" class="form-control" value="<?php echo htmlspecialchars($row['nomor_lambung']); ?>" required>
        </div>
        <div class="form-group">
            <label for="layanan">Layanan:</label>
            <select name="layanan" class="form-control" required>
                <?php
                if ($layanan_result->num_rows > 0) {
                    while ($layanan_row = $layanan_result->fetch_assoc()) {
                        $selected = ($row['layanan'] == $layanan_row['layanan']) ? 'selected' : '';
                        echo "<option value='{$layanan_row['layanan']}' $selected>{$layanan_row['layanan']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="koridor">Koridor:</label>
            <select name="koridor" class="form-control" required>
                <?php
                if ($koridor_result->num_rows > 0) {
                    while ($koridor_row = $koridor_result->fetch_assoc()) {
                        $selected = ($row['koridor'] == $koridor_row['koridor']) ? 'selected' : '';
                        echo "<option value='{$koridor_row['koridor']}' $selected>{$koridor_row['koridor']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="operator">Operator:</label>
            <select name="operator" class="form-control" required>
                <?php
                if ($operator_result->num_rows > 0) {
                    while ($operator_row = $operator_result->fetch_assoc()) {
                        $selected = ($row['operator'] == $operator_row['operator']) ? 'selected' : '';
                        echo "<option value='{$operator_row['operator']}' $selected>{$operator_row['operator']}</option>";
                    }
                }
                ?>
            </select>
        </div>
        <button type="submit" name="update_armada" class="btn btn-primary">Update Armada</button>
    </form>

</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
