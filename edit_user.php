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

// Get the user ID from the URL
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    header("Location: manajemen_user.php");
    exit();
}

// Fetch the user data from the database
$sql = "SELECT * FROM user WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the updated form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nomorhp = $_POST['nomorhp'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // If the password is provided, hash it
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        // Update the password and other details
        $sql = "UPDATE user SET username = ?, email = ?, nomorhp = ?, role = ?, password = ? WHERE id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $username, $email, $nomorhp, $role, $password, $user_id);
    } else {
        // Update without changing the password
        $sql = "UPDATE user SET username = ?, email = ?, nomorhp = ?, role = ? WHERE id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $email, $nomorhp, $role, $user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['status'] = 'User updated successfully!';
        header("Location: manajemen_user.php");
        exit();
    } else {
        $_SESSION['status'] = 'Error updating user!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">OPS TfSM</a>
</nav>

<div class="container mt-5">
    <h3>Edit User</h3>

    <?php if (isset($_SESSION['status'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['status']; unset($_SESSION['status']); ?></div>
    <?php endif; ?>

    <form method="post" action="edit_user.php?id=<?php echo $user['id_user']; ?>">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
        </div>
        <div class="form-group">
            <label for="nomorhp">Nomor HP:</label>
            <input type="text" class="form-control" id="nomorhp" name="nomorhp" value="<?php echo $user['nomorhp']; ?>" required>
        </div>
        <div class="form-group">
            <label for="role">Role:</label>
            <select class="form-control" id="role" name="role" required>
                <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
            </select>
        </div>
        <div class="form-group">
            <label for="password">New Password (leave empty to keep current):</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="manajemen_user.php" class="btn btn-secondary">Back to User Management</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Close the database connection
$stmt->close();
$conn->close();
?>
