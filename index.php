<?php
// Include the database connection file
include 'utils/db_connection.php';

// Retrieve all event types
$selectEventTypeQuery = "SELECT DISTINCT event_type FROM events";
$resultEventType = mysqli_query($conn, $selectEventTypeQuery);

if (!$resultEventType) {
  die("Error executing the query: " . mysqli_error($conn));
}

// Fetch event types
$eventTypes = mysqli_fetch_all($resultEventType, MYSQLI_ASSOC);

// Retrieve all events from the database
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterEventType = isset($_GET['event_type']) ? $_GET['event_type'] : '';

$selectQuery =
  "SELECT * FROM events
    WHERE (event_name LIKE '%$search%'
    OR event_details LIKE '%$search%')"
  . ($filterEventType ? "AND event_type = '$filterEventType'" : "");
$result = mysqli_query($conn, $selectQuery);

if (!$result) {
  die("Error executing the query: " . mysqli_error($conn));
}

// Fetch and display events
$events = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" type="text/css" href="styles/globals.css">
</head>

<body>
  <?php require 'layout/header.php'; ?>
  <main>
    <form method="get" action="">
      <input type="text" name="search" placeholder="Search for events..."
        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

      <label for="event_type">Categorie:</label>
      <select id="event_type" name="event_type">
        <option value="">All</option>
        <?php foreach ($eventTypes as $eventType): ?>
          <option value="<?php echo $eventType['event_type']; ?>">
            <?php echo $eventType['event_type']; ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit">Submit</button>
    </form>

    <div id="event-card-ctr">
      <?php
      if (empty($events)) {
        echo "<p>No events found.</p>";
      } else {
        foreach ($events as $event) {
          echo
            "<div class='event-card'>
              <div>
                <div>
                  <img src='{$event['event_img']}' alt='img' width='250px'>
                </div>
                <h3>{$event['event_name']}</h3>
                <p>Date: {$event['event_date']}</p>
              </div>
              <a href='event.php?id={$event['event_id']}'>
                View More Details
              </a>
            </div>";
        }
      }
      ?>
    </div>
  </main>
  <?php require 'layout/footer.php'; ?>
</body>

<script>
  // Set default value to event_type using JavaScript
  document.getElementById("event_type").value = "<?php echo $filterEventType; ?>";
</script>

</html>