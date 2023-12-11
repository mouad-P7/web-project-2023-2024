<?php
// Include the database connection file
include 'utils/db_connection.php';

// Start session to persist user login
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get user input from the form
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Validate user input (you can add more validation as needed)
  if (empty($username) || empty($password)) {
    $error_message = "Username and password are required.";
  } else {
    // Sanitize user input to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // Query the database to check user credentials
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result) {
      // Check if a matching user is found
      if (mysqli_num_rows($result) == 1) {
        // User is authenticated, store user data in session
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['user_role'];

        // Redirect to a dashboard or home page
        if ($_SESSION['user_role'] == "admin") {
          header("Location: viewEvents.php");
        } else if ($_SESSION['user_role'] == "user") {
          header("Location: index.php");
        }
        exit();
      } else {
        $error_message = "Invalid username or password.";
      }
    } else {
      $error_message = "Error executing the query: " . mysqli_error($conn);
    }
  }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" type="text/css" href="styles/globals.css">
</head>

<body>
  <?php require 'layout/header.php'; ?>
  <main>
    <h1>Login Form</h1>
    <?php
    // Display error message if any
    if (isset($error_message)) {
      echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form method="post" action="">
      <label for="username">Username:</label>
      <input type="text" name="username" required>
      <label for="password">Password:</label>
      <input type="password" name="password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
  </main>
  <?php require 'layout/footer.php'; ?>
</body>

</html>