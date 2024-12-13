<?php
// Start the session
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Check if the user is an admin, if not, redirect to dashboard
if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Include database connection
include('koneksi.php');

// Function to sanitize input data
function sanitize_input($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// CRUD operations: Create, Read, Delete

// Add armada (Create)
if (isset($_POST['add_armada'])) {
    $nomor_lambung = sanitize_input($_POST['nomor_lambung']);
    $layanan = sanitize_input($_POST['layanan']);
    $koridor = sanitize_input($_POST['koridor']);
    $operator = sanitize_input($_POST['operator']);

    // Insert data into the database with isdelete default as 0
    $sql = "INSERT INTO armada (nomor_lambung, layanan, koridor, operator, tanggal_input, isdelete) 
            VALUES ('$nomor_lambung', '$layanan', '$koridor', '$operator', NOW(), 0)";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Armada added successfully');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Delete armada (Soft Delete)
if (isset($_GET['delete'])) {
    $id_armada = $_GET['delete'];
    $sql = "UPDATE armada SET isdelete = 1 WHERE id_armada = $id_armada";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Armada deleted successfully');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Fetch armada data (Read)
$sql = "SELECT * FROM armada WHERE isdelete = 0";
$result = $conn->query($sql);

// Fetch dropdown options
$sql_layanan = "SELECT layanan FROM layanan WHERE isdelete = 0";
$result_layanan = $conn->query($sql_layanan);

$sql_koridor = "SELECT koridor FROM koridor WHERE isdelete = 0";
$result_koridor = $conn->query($sql_koridor);

$sql_operator = "SELECT operator FROM operator WHERE isdelete = 0";
$result_operator = $conn->query($sql_operator);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Armada</title>
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
        .table {
            background-color: #2c3e50; /* Dark blue background for the table */
            color: white; /* White text color */
            border-radius: 8px; /* Rounded corners for the table */
        }
        th, td {
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #34495e; /* Darker blue for the header */
            color: white;
        }
        .table-bordered {
            border: 1px solid #1abc9c; /* Elegant green border */
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #1abc9c; /* Border around each cell */
        }
        .back-to-dashboard {
            margin-bottom: 20px;
        }
        /* Hover effect for table rows */
        .table tbody tr:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">OPS TfSM</a>
</nav>

<div class="container">
    <!-- Back to Dashboard button -->
    <div class="back-to-dashboard">
        <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <h2>Manajemen Armada</h2>
    
    <!-- Form to add armada -->
<h3>Add Armada</h3>
<form method="POST">
    <div class="form-group">
        <label for="nomor_lambung">Nomor Lambung:</label>
        <input type="text" name="nomor_lambung" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="layanan">Layanan:</label>
        <select name="layanan" class="form-control" required>
            <?php
            if ($result_layanan->num_rows > 0) {
                while ($row = $result_layanan->fetch_assoc()) {
                    echo "<option value='" . $row['layanan'] . "'>" . $row['layanan'] . "</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label for="koridor">Koridor:</label>
        <select name="koridor" class="form-control" required>
            <?php
            if ($result_koridor->num_rows > 0) {
                while ($row = $result_koridor->fetch_assoc()) {
                    echo "<option value='" . $row['koridor'] . "'>" . $row['koridor'] . "</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="form-group">
        <label for="operator">Operator:</label>
        <select name="operator" class="form-control" required>
            <?php
            if ($result_operator->num_rows > 0) {
                while ($row = $result_operator->fetch_assoc()) {
                    echo "<option value='" . $row['operator'] . "'>" . $row['operator'] . "</option>";
                }
            }
            ?>
        </select>
    </div>
    <button type="submit" name="add_armada" class="btn btn-primary">Add Armada</button>
</form>


    <h3>Armada List</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID Armada</th>
                <th>Nomor Lambung</th>
                <th>Layanan</th>
                <th>Koridor</th>
                <th>Operator</th>
                <th>Tanggal Input</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id_armada'] . "</td>";
                    echo "<td>" . $row['nomor_lambung'] . "</td>";
                    echo "<td>" . $row['layanan'] . "</td>";
                    echo "<td>" . $row['koridor'] . "</td>";
                    echo "<td>" . $row['operator'] . "</td>";
                    echo "<td>" . $row['tanggal_input'] . "</td>";
                    echo "<td>
                            <a href='edit_armada.php?id=" . $row['id_armada'] . "' class='btn btn-warning'>Edit</a>
                            <a href='manajemen_armada.php?delete=" . $row['id_armada'] . "' class='btn btn-danger'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No data found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
