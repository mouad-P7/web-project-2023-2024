<?php
// Include the database connection file
include 'utils/db_connection.php';
include 'utils/functions.php';

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
  // Get the event ID from the URL
  $eventID = $_GET['id'];

  // Retrieve the event details from the database
  $selectQuery =
    "SELECT events.*, 
      users.username as organizer_username, 
      users.user_img as organizer_img 
    FROM events 
    JOIN users ON events.organizer_id = users.user_id
    WHERE events.event_id = '$eventID'";
  $result = mysqli_query($conn, $selectQuery);

  if (!$result) {
    die("Error executing the query: " . mysqli_error($conn));
  }

  // Fetch event details
  $event = mysqli_fetch_assoc($result);

  // Check if the user is already registered for the event
  $isRegistered = false;
  if (is_logged_in()) {
    $userID = $_SESSION['user_id'];
    $checkRegistrationQuery = "SELECT * FROM registrations WHERE event_id = '$eventID' AND user_id = '$userID'";
    $registrationResult = mysqli_query($conn, $checkRegistrationQuery);
    $isRegistered = mysqli_num_rows($registrationResult) > 0;
  }

} else {
  // Redirect to the index page if 'id' parameter is not set
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Details</title>
  <link rel="stylesheet" type="text/css" href="styles/globals.css">
  <?php require 'utils/config.php'; ?>
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsApiKey; ?>&callback=initMap"></script>
  <script>
    var map;
    var marker;
    function initMap() {
      // Initialize the map
      map = new google.maps.Map(document.getElementById('map'), {
        center: {
          lat: <?php echo $event['event_latitude']; ?>,
          lng: <?php echo $event['event_longitude']; ?>
        },
        zoom: 15 // You can adjust the zoom level as needed
      });
      // Add a marker for the event's location
      marker = new google.maps.Marker({
        position: {
          lat: <?php echo $event['event_latitude']; ?>,
          lng: <?php echo $event['event_longitude']; ?>
        },
        map: map,
        title: 'Event Location'
      });
    }
  </script>
</head>

<body>
  <?php require 'layout/header.php'; ?>
  <main>
    <div>
      <?php
      if (isset($event)) {
        echo
          "<div>
            <img src='{$event['event_img']}' alt='Event Image' width='250px'>
          </div>
          <div>
            <h3>{$event['event_name']}</h3>";
        if ($isRegistered) {
          echo
            "<a href='unregisterEvent.php?eventID={$event['event_id']}'>
              Unregister from this Event
            </a>";
        } else {
          echo
            "<a href='registerEvent.php?eventID={$event['event_id']}'>
              Register for this Event
            </a>";
        }
        echo
          "</div>
          <div>
            <p>Categorie: {$event['event_type']}</p>
            <p>Date: {$event['event_date']}</p>
            <div>
              <p>Organisé par:</p>
              <img 
                src='{$event['organizer_img']}' alt='Organizer Image' 
                width='40' height='40'
              >
              <p>{$event['organizer_username']}</p>
            </div>
          </div>
          <p>{$event['event_details']}</p>";
        if ($event['event_latitude'] != 0 && $event['event_longitude'] != 0) {
          echo "<div id='map'></div>";
        } else {
          echo "<p>No Location in this event.</p>";
        }
      } else {
        echo "<p>Event not found.</p>";
      }
      ?>
    </div>
  </main>
  <?php require 'layout/footer.php'; ?>

</body>

</html>