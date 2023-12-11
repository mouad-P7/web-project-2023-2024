<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  // Redirect to the login page if not logged in
  header("Location: login.php");
  exit();
}
include 'utils/functions.php';
if (!is_admin()) {
  header("Location: accessDenied.php");
  exit();
}

// Include the database connection file
include 'utils/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get the event_id from the submitted form
  $eventID = $_POST['event_id'];

  // Get the organizer ID from the session
  $organizerID = $_SESSION['user_id'];

  // Delete the event from the database (make sure to check if the event belongs to the logged-in user)
  $deleteEventQuery = "DELETE FROM events WHERE event_id = '$eventID' AND organizer_id = '$organizerID'";
  $deleteEventResult = mysqli_query($conn, $deleteEventQuery);

  // Delete registrations associated with the event
  $deleteRegistrationsQuery = "DELETE FROM registrations WHERE event_id = '$eventID'";
  $deleteRegistrationsResult = mysqli_query($conn, $deleteRegistrationsQuery);

  if (!$deleteEventResult || !$deleteRegistrationsResult) {
    die("Error executing the query: " . mysqli_error($conn));
  }

  // Redirect back to the viewEvents.php page
  header("Location: viewEvents.php");
  exit();
}

mysqli_close($conn);
?>