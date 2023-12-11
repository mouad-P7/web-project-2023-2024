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
include 'utils/functions.php';
if (!is_admin()) {
  header("Location: accessDenied.php");
  exit();
}

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
  // Get the event ID from the URL
  $eventID = $_GET['id'];

  // Retrieve the event details from the database
  $selectQuery = "SELECT * FROM events WHERE event_id = '$eventID'";
  $result = mysqli_query($conn, $selectQuery);

  if (!$result) {
    die("Error executing the query: " . mysqli_error($conn));
  }

  // Fetch event details
  $event = mysqli_fetch_assoc($result);

} else {
  // Redirect to the viewEvents.php page if 'id' parameter is not set
  header("Location: viewEvents.php");
  exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $eventId = $_POST['event_id']; // Assuming you have a hidden input for event_id in your form
  $eventName = $_POST['event_name'];
  $eventType = $_POST['event_type'];
  $eventDate = $_POST['event_date'];
  $eventDetails = $_POST['event_details'];
  $eventLatitude = $_POST['event_latitude'];
  $eventLongitude = $_POST['event_longitude'];

  // Sanitize inputs
  $eventId = mysqli_real_escape_string($conn, $eventId);
  $eventName = mysqli_real_escape_string($conn, $eventName);
  $eventDate = mysqli_real_escape_string($conn, $eventDate);
  $eventDetails = mysqli_real_escape_string($conn, $eventDetails);

  // Get the organizer ID from the session
  $organizerID = $_SESSION['user_id'];

  // Check if an image was uploaded
  if (isset($_FILES['event_img']) && $_FILES['event_img']['error'] == 0) {
    $target_dir = 'images/';  // Set your target directory
    $target_file = $target_dir . basename($_FILES['event_img']['name']);

    // Check file size (2MB limit)
    if ($_FILES['event_img']['size'] > 2 * 1024 * 1024) {
      $error_message = "File size exceeds the limit of 2MB.";
    } else {
      if (move_uploaded_file($_FILES['event_img']['tmp_name'], $target_file)) {
        $eventImage = $target_file;

        // Update the existing event in the database with the image
        $updateQuery =
          "UPDATE events 
          SET event_name = '$eventName', 
            event_type = '$eventType',
            event_date = '$eventDate', 
            event_details = '$eventDetails', 
            event_img = '$eventImage',
            event_latitude = '$eventLatitude', 
            event_longitude = '$eventLongitude'
          WHERE event_id = '$eventId' AND organizer_id = '$organizerID'";
        $updateResult = mysqli_query($conn, $updateQuery);

        if ($updateResult) {
          header("Location: viewEvents.php");
          exit();
        } else {
          $error_message = "Error executing the query: " . mysqli_error($conn);
        }
      } else {
        $error_message = "Error moving the uploaded file.";
      }
    }
  } else {
    // Update the existing event in the database without changing the image
    $updateQuery =
      "UPDATE events 
      SET event_name = '$eventName', 
        event_type = '$eventType',
        event_date = '$eventDate', 
        event_details = '$eventDetails',
        event_latitude = '$eventLatitude',
        event_longitude = '$eventLongitude'
      WHERE event_id = '$eventId' AND organizer_id = '$organizerID'";
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
      header("Location: viewEvents.php");
      exit();
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
  <title>Edit Event</title>
  <link rel="stylesheet" type="text/css" href="styles/globals.css">
  <?php require 'utils/config.php'; ?>
  <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsApiKey; ?>&callback=initMap"></script>
</head>

<body>
  <?php require 'layout/header.php'; ?>
  <main>
    <h1>Edit Event</h1>
    <form method="post" action="" enctype="multipart/form-data">
      <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">

      <label for="event_name">Event Name:</label>
      <input type="text" id="event_name" name="event_name" value='<?php echo $event['event_name']; ?>' required>

      <label for="event_type">Event Type:</label>
      <select id="event_type" name="event_type" required>
        <option value="conférence">Conférence</option>
        <option value="forum">Forum</option>
        <option value="formation">Formation</option>
        <option value="voyage">Voyage</option>
      </select>

      <label for="event_date">Event Date:</label>
      <input type="date" id="event_date" name="event_date" value="<?php echo $event['event_date']; ?>" required>

      <label for="event_details">Event Details:</label>
      <textarea name="event_details" id="event_details" rows="6" cols="35"
        required><?php echo $event['event_details']; ?></textarea>

      <img src="<?php echo $event['event_img']; ?>" alt="Current Event Image" width="150" height="100">
      <label for="event_img">Upload a new image:</label>
      <input type="file" id="event_img" name="event_img" accept="image/*" maxlength="2000000">

      <label for="map">Choose Location:</label>
      <div id="map"></div>
      <input type="hidden" id="event_latitude" name="event_latitude" value="<?php echo $event['event_latitude']; ?>">
      <input type="hidden" id="event_longitude" name="event_longitude" value="<?php echo $event['event_longitude']; ?>">

      <button type="submit">Save Changes</button>
    </form>
  </main>
  <?php require 'layout/footer.php'; ?>

  <script>
    // Set default value to event_type using JavaScript
    document.getElementById("event_type").value = "<?php echo $event['event_type']; ?>";

    var map;
    var marker;

    function initMap() {
      // Initialize the map
      map = new google.maps.Map(document.getElementById('map'), {
        center: {
          lat: <?php echo $event['event_latitude']; ?>,
          lng: <?php echo $event['event_longitude']; ?>
        },
        zoom: 15 // Adjust the initial zoom level as needed
      });
      // Add a click event listener to get the location when the user clicks on the map
      map.addListener('click', function (event) {
        updateLocation(event.latLng.lat(), event.latLng.lng());
      });
      // Add a marker for the default location 
      marker = new google.maps.Marker({
        position: {
          lat: <?php echo $event['event_latitude']; ?>,
          lng: <?php echo $event['event_longitude']; ?>
        },
        map: map,
        draggable: true, // Allow the marker to be dragged to select a precise location
        title: 'Event Location'
      });
      // Add a drag event listener to update the location when the marker is dragged
      marker.addListener('dragend', function (event) {
        updateLocation(event.latLng.lat(), event.latLng.lng());
      });
    }

    function updateLocation(latitude, longitude) {
      // Update the hidden input fields with the selected coordinates
      document.getElementById('event_latitude').value = latitude;
      document.getElementById('event_longitude').value = longitude;
      // Update the marker position on the map
      marker.setPosition({ lat: latitude, lng: longitude });
    }
  </script>

</body>

</html>