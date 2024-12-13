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

// Count users where isdelete = 0
$sql_user = "SELECT COUNT(*) AS total_users FROM user WHERE isdelete = 0";
$result_user = $conn->query($sql_user);
$row_user = $result_user->fetch_assoc();
$total_users = $row_user['total_users'];

// Count armada where isdelete = 0
$sql_armada = "SELECT COUNT(*) AS total_armada FROM armada WHERE isdelete = 0";
$result_armada = $conn->query($sql_armada);
$row_armada = $result_armada->fetch_assoc();
$total_armada = $row_armada['total_armada'];

// Count laporan where isvalid = 0 and isdelete = 0
$sql_pending_laporan = "SELECT COUNT(*) AS total_pending FROM laporan WHERE isvalid = 0 AND isdelete = 0";
$result_pending_laporan = $conn->query($sql_pending_laporan);
$row_pending_laporan = $result_pending_laporan->fetch_assoc();
$total_pending_laporan = $row_pending_laporan['total_pending'];

// Count laporan where isvalid = 1 and isdelete = 0
$sql_valid_laporan = "SELECT COUNT(*) AS total_valid FROM laporan WHERE isvalid = 1 AND isdelete = 0";
$result_valid_laporan = $conn->query($sql_valid_laporan);
$row_valid_laporan = $result_valid_laporan->fetch_assoc();
$total_valid_laporan = $row_valid_laporan['total_valid'];

// Count laporan based on the logged-in user's username and isdelete = 0
$sql_user_laporan = "SELECT COUNT(*) AS total_user_laporan FROM laporan WHERE username = ? AND isdelete = 0";
$stmt_user_laporan = $conn->prepare($sql_user_laporan);
$stmt_user_laporan->bind_param("s", $_SESSION['username']);
$stmt_user_laporan->execute();
$result_user_laporan = $stmt_user_laporan->get_result();
$row_user_laporan = $result_user_laporan->fetch_assoc();
$total_user_laporan = $row_user_laporan['total_user_laporan'];

// Count notulensi where issign = 0 and isdelete = 0
$sql_notulensi = "SELECT COUNT(*) AS total_notulensi FROM notulensi WHERE issign = 1 AND isdelete = 0";
$result_notulensi = $conn->query($sql_notulensi);
$row_notulensi = $result_notulensi->fetch_assoc();
$total_notulensi = $row_notulensi['total_notulensi'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
        .btn-custom {
            font-size: 1.5rem;
            padding: 20px 40px;
            text-align: center;
            width: 100%; /* Make buttons same size */
            margin-bottom: 20px;
            color: white; /* Ensure text is white */
        }
        .btn-user {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-user:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-armada {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-armada:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .btn-laporan {
            background-color: #ffc107;
            border-color: #ffc107;
        }
        .btn-laporan:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }
        .btn-screening {
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .btn-screening:hover {
            background-color: #138496;
            border-color: #117a8b;
        }
        .btn-progres-laporan {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-progres-laporan:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        .btn-notulen {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }
        .btn-notulen:hover {
            background-color: #5a32a0;
            border-color: #512a8b;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">OPS TfSM</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item active">
                <a class="nav-link" href="edit_profile.php">Edit Profile</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h3>Welcome, <?php echo $_SESSION['username']; ?>!</h3>
    <p class="mb-1"><?php echo $_SESSION['email']; ?></p>
    <p class="mb-1"><?php echo $_SESSION['nomorhp']; ?></p>
    <p class="mb-1"><?php echo $_SESSION['role']; ?></p>

    <!-- Admin-only buttons -->
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="manajemen_user.php" class="btn btn-custom btn-user">Manajemen User | <?php echo $total_users; ?></a>
        <a href="manajemen_armada.php" class="btn btn-custom btn-armada">Manajemen Armada | <?php echo $total_armada; ?></a>
        <a href="manajemen_laporan.php" class="btn btn-custom btn-laporan">
            Manajemen Laporan | Pending: <?php echo $total_pending_laporan; ?> | Valid: <?php echo $total_valid_laporan; ?>
        </a>
        <a href="manajemen_notulen.php" class="btn btn-custom btn-notulen">Manajemen Notulen (Coming soon) | <?php echo $total_notulensi; ?></a>
    <?php endif; ?>

    <!-- Progres Laporan button for all users -->
    <a href="progres_laporan.php" class="btn btn-custom btn-progres-laporan">Progres Laporan | <?php echo $total_user_laporan; ?></a>

    <!-- Screening Armada button, visible to all roles -->
    <a href="screening_armada.php" class="btn btn-custom btn-screening">Screening Armada</a>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
