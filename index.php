<?php
// Include the database connection file
include('koneksi.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the user exists with the provided email
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User found, now check status
        $user = $result->fetch_assoc();

        // Check if the account is banned
        if ($user['isbanned'] == 1) {
            $error_message = "Akun Anda telah ditangguhkan. Silakan hubungi administrator.";
        }
        // Check if the account is marked as deleted
        elseif ($user['isdelete'] == 1) {
            $error_message = "Akun tidak ditemukan. Silakan daftar kembali atau hubungi administrator.";
        }
        // If not banned or deleted, verify password
        elseif (password_verify($password, $user['password'])) {
            // Password is correct, set session data
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nomorhp'] = $user['nomorhp'];
            $_SESSION['role'] = $user['role'];

            // Redirect to the dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Password salah.";
        }
    } else {
        $error_message = "Email tidak ditemukan.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <style>
    body {
      background-color: #1d2d50; /* Dark blue background */
      color: white; /* White text color */
    }
    .navbar {
      background-color: #14213d; /* Dark blue navbar */
    }
    .navbar-brand {
      color: #ffffff; /* White navbar brand */
    }
    .form-control {
      background-color: #4a90e2; /* Dark blue form fields */
      color: white; /* White text in form fields */
      border: 1px solid #6c757d; /* Light border for form fields */
    }
    .form-control:focus {
      background-color: #2c6a8a; /* Lighter blue when focused */
      border-color: #007bff; /* Focused border color */
    }
    .btn-primary {
      background-color: #007bff; /* Blue button */
      border-color: #007bff; /* Matching border */
    }
    .btn-primary:hover {
      background-color: #0056b3; /* Darker blue on hover */
      border-color: #0056b3; /* Matching border */
    }
    .container {
      margin-top: 50px;
    }
    hr {
      border-color: #6c757d;
    }
  </style>
  <title>Login</title>
</head>
<body>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand">OPS TfSM</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
        </li>
      </ul>
    </div>
  </nav>

  <div class="container">
    <h1 class="text-center">Login</h1>

    <?php if (isset($error_message)) { echo '<div class="alert alert-danger">'.$error_message.'</div>'; } ?>

    <form method="post" action="index.php">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" name="email" required="required">
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" class="form-control" id="password" name="password" required="required">
      </div>

      <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
  </div>

  <br>
  <hr>
  <center>
    <h5>Transport For Semarang 2024</h5>
  </center>
</body>
</html>
