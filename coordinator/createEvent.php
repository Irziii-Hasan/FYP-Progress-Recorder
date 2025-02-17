<?php
include'session_coordinator.php'; // Include session handling
include'config.php';

$eventError = ""; // Initialize event error variable
$eventNameValue = ""; // Initialize event name value variable
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = trim($_POST['event_name']);
    
    // Validate event name and date
    if (empty($event_name)) {
        $eventError = "Event name is required";
    } else {
        // Check if event name already exists on the same date
        $query = "SELECT * FROM events WHERE eventName = '$event_name'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $eventError = "An event with the same name already exists";
            $eventNameValue = $event_name; // Preserve the value of event name field
        } else {
            // Insert new event
            $sql = "INSERT INTO events (eventName) VALUES ('$event_name')";
            if ($conn->query($sql) === TRUE) {
                echo'<script>alert("New event created successfully");</script>';
            } else {
                echo"<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP | Add Event</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <!-- Custom styles -->
  <link rel="stylesheet" href="style.css">
  <style>
    .form-all {
      padding: 20px 30px;
      border: 1px solid #cbcbcb;
      border-radius: 20px;
      background-color: white;
    }

    .form-heading {
      color: #0a4a91;
      font-weight: 700;
    }

    label {
      font-weight: 500;
    }
  </style>
</head>
<body>
<?php include'nav.php'; ?>

<div class="wrapper">
  <?php include 'sidebar.php'; ?>

  <div class="container-fluid" id="content">
    <div class="row">
      <div class="col-md-12">
        <!-- BREADCRUMBS -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
            <li class="breadcrumb-item"><a href="event.php">Events</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Event</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Add Event</h2>
          <form action="createEvent.php" method="post">
            <div class="row mb-3 align-items-center">
              <div class="col-auto">
                <label for="eventName" class="col-form-label">Event Name</label>
              </div>
              <div class="col">
                <input type="text" class="form-control" id="eventName" placeholder="e.g. FYP-I Mid" name="event_name" value="<?php echo htmlspecialchars($eventNameValue); ?>" required>
              </div>
            </div>
            <div class="text-dangermb-3"><?  $eventError; ?></div>
            <div class="d-grid gap-2 d-md-block">
              <a href="event.php" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- FontAwesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

</body>
</html>
