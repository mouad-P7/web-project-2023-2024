<?php
session_start();

// Include the database connection file
include 'utils/db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  // Redirect to the login page if not logged in
  header("Location: login.php");
  exit();
}

// Check if the 'eventID' parameter is set in the URL
if (isset($_GET['eventID'])) {
  // Get the event ID from the URL
  $eventID = $_GET['eventID'];

  // Get the user ID from the session
  $userID = $_SESSION['user_id'];

  // Check if the user is registered for the event
  $checkRegistrationQuery = "SELECT * FROM registrations WHERE event_id = '$eventID' AND user_id = '$userID'";
  $checkRegistrationResult = mysqli_query($conn, $checkRegistrationQuery);

  if ($checkRegistrationResult && mysqli_num_rows($checkRegistrationResult) > 0) {
    // User is registered for the event, unregister the user
    $unregisterQuery = "DELETE FROM registrations WHERE event_id = '$eventID' AND user_id = '$userID'";
    $unregisterResult = mysqli_query($conn, $unregisterQuery);

    if ($unregisterResult) {
      // User successfully unregistered from the event
      header("Location: event.php?id=$eventID");
      exit();
    } else {
      // Error occurred during unregistration
      die("Error executing the unregistration query: " . mysqli_error($conn));
    }
  } else {
    // User is not registered for the event
    header("Location: event.php?id=$eventID");
    exit();
  }
} else {
  // Redirect to the index page if 'eventID' parameter is not set
  header("Location: index.php");
  exit();
}

// Close the database connection
mysqli_close($conn);
?>