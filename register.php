<?php
include 'utils/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $email = $_POST['email'];

  // Default image path if the user does not provide one
  $defaultImage = 'images/inconnu.png';

  if (empty($username) || empty($password)) {
    $error_message = "Username and password are required.";
  } else {
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);
    $email = mysqli_real_escape_string($conn, $email);

    // Check if the username is already taken
    $check_query = "SELECT * FROM users WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);

    if ($check_result && mysqli_num_rows($check_result) > 0) {
      $error_message = "Username is already taken.";
    } else {
      // Check if user uploaded an image
      if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] == 0) {
        $target_dir = 'images/';  // Set your target directory
        $target_file = $target_dir . basename($_FILES['user_img']['name']);

        // Check file size (2MB limit)
        if ($_FILES['user_img']['size'] > 2 * 1024 * 1024) {
          $error_message = "File size exceeds the limit of 2MB.";
        } else {
          move_uploaded_file($_FILES['user_img']['tmp_name'], $target_file);
          $userImage = $target_file;
        }
      } else {
        // Use the default image
        $userImage = $defaultImage;
      }

      // Insert the new user into the database
      $insert_query =
        "INSERT INTO users (username, email, password, user_img) 
        VALUES ('$username', '$email', '$password', '$userImage')";
      $insert_result = mysqli_query($conn, $insert_query);

      if ($insert_result) {
        // Registration successful, redirect to login page
        header("Location: login.php");
        exit();
      } else {
        $error_message = "Error executing the query: " . mysqli_error($conn);
      }
    }
  }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration</title>
  <link rel="stylesheet" type="text/css" href="styles/globals.css">
</head>

<body>
  <?php require 'layout/header.php'; ?>
  <main>
    <h2>Registration From</h2>
    <?php
    if (isset($error_message)) {
      echo "<p style='color: red;'>$error_message</p>";
    }
    ?>
    <form method="post" action="" enctype="multipart/form-data">
      <label for="username">Username:</label>
      <input type="text" name="username" required>
      <label for="email">Email:</label>
      <input type="email" name="email" required>
      <label for="password">Password:</label>
      <input type="password" name="password" required>
      <label for="user_img">Profile Image:</label>
      <!-- max img size is 2Mo -->
      <input type="file" name="user_img" accept="image/*" maxlength="2000000">
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Log In</a></p>
  </main>
  <?php require 'layout/footer.php'; ?>
</body>

</html>