<?php
// Start the session
session_start();

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Include the database connection file
include("koneksi.php");

// Get the user data from the form
$username = $_POST['username'];
$email = $_POST['email'];
$nomorhp = $_POST['nomorhp'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Fetch the current username from the session
$current_username = $_SESSION['username'];

// Check if the password is provided and if it matches the confirmation
if (!empty($password) && $password === $confirm_password) {
    // Hash the new password
    $password = password_hash($password, PASSWORD_DEFAULT);
    
    // Update the profile with the new password (no role field)
    $sql = "UPDATE user SET username=?, email=?, nomorhp=?, password=? WHERE username=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error); // Debugging line
    }
    $stmt->bind_param("sssss", $username, $email, $nomorhp, $password, $current_username);
} else {
    // Update the profile without changing the password (no role field)
    $sql = "UPDATE user SET username=?, email=?, nomorhp=? WHERE username=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error in preparing statement: " . $conn->error); // Debugging line
    }
    $stmt->bind_param("ssss", $username, $email, $nomorhp, $current_username);
}

// Execute the query
if ($stmt->execute()) {
    // Update the session variables
    $_SESSION['username'] = $username;
    $_SESSION['email'] = $email;
    $_SESSION['nomorhp'] = $nomorhp;

    // Set a session variable for success
    $_SESSION['update_status'] = 'success';
} else {
    // If the update failed, set a session variable for failure
    $_SESSION['update_status'] = 'error';
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();

// Notify the user
echo "<script type='text/javascript'>
        alert('Profile updated successfully!');
        window.location.href = 'dashboard.php'; // Redirect to dashboard
      </script>";
exit();
?>
