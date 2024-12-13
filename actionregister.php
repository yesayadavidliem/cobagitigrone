<?php
// Include the database connection file
include('koneksi.php');

// Check if the form fields exist before accessing them
if (isset($_POST['nomorhp'])) {
    // Retrieve form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $nomorhp = $_POST['nomorhp'];  // Make sure this is the correct index
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];
    $isbanned = $_POST['isbanned'];
    $isdelete = $_POST['isdelete'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    // Hash password for security
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Check if the username, email, or nomorhp already exist in the database
    $check_sql = "SELECT * FROM user WHERE username = ? OR email = ? OR nomorhp = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("sss", $username, $email, $nomorhp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If a user with the same username, email, or nomorhp exists, show an error message
        echo "Username, Email, or Nomor HP already exists. Please choose a different one.";
    } else {
        // If no match, proceed to insert the new user
        $sql = "INSERT INTO user (username, email, nomorhp, password, role, isbanned, isdelete) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $username, $email, $nomorhp, $password_hash, $role, $isbanned, $isdelete);

        if ($stmt->execute()) {
            echo "New user created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Close the connection
    $conn->close();
} else {
    echo "Error: nomorhp field is missing from the form submission.";
}
?>
