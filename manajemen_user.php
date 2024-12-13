<?php
// Start the session
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Include the database connection file
include("koneksi.php");

// Fetch users from the database
$sql = "SELECT * FROM user WHERE isdelete = 0";  // Only active users
$result = $conn->query($sql);

// Handle actions (Ban, Unban, Delete)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];

    if (isset($_POST['ban'])) {
        // Ban user
        $sql = "UPDATE user SET isbanned = 1 WHERE id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $_SESSION['status'] = 'User banned successfully!';
        } else {
            $_SESSION['status'] = 'Failed to ban user!';
        }
    } elseif (isset($_POST['unban'])) {
        // Unban user
        $sql = "UPDATE user SET isbanned = 0 WHERE id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $_SESSION['status'] = 'User unbanned successfully!';
        } else {
            $_SESSION['status'] = 'Failed to unban user!';
        }
    } elseif (isset($_POST['delete'])) {
        // Delete user
        $sql = "UPDATE user SET isdelete = 1 WHERE id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $_SESSION['status'] = 'User deleted successfully!';
        } else {
            $_SESSION['status'] = 'Failed to delete user!';
        }
    }

    header("Location: manajemen_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">OPS TfSM</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
</nav>

<div class="container mt-5">
    <h3>User Management</h3>

    <?php if (isset($_SESSION['status'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="form.php" class="btn btn-primary">Tambah Akun</a> <!-- Button to navigate to form.php -->
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Nomor HP</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['id_user']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['nomorhp']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td><?php echo ($user['isbanned'] == 1) ? 'Banned' : 'Active'; ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id_user']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form method="post" action="manajemen_user.php" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id_user']; ?>">
                            <?php if ($user['isbanned'] == 0): ?>
                                <button type="submit" name="ban" class="btn btn-danger btn-sm">Ban</button>
                            <?php else: ?>
                                <button type="submit" name="unban" class="btn btn-success btn-sm">Unban</button>
                            <?php endif; ?>
                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>

<?php
$conn->close();
?>
