<?php

include 'session_admin.php'; // Include session handling
include 'config.php';

$roomError = ""; // Initialize room error variable
$roomValue = ""; // Initialize room value variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_numbers = explode(',', trim($_POST['room_numbers']));
    $room_numbers = array_map('trim', $room_numbers);
    $existingRooms = [];
    $addedRooms = [];
    $errors = [];

    foreach ($room_numbers as $room_number) {
        // Validate room no.
        if (empty($room_number)) {
            $errors[] = "Room Number is required";
        } else {
            // Check if room_number already exists in the database
            $query = "SELECT * FROM rooms WHERE room_number = '$room_number'";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                $existingRooms[] = $room_number;
            } else {
                // Insert new room
                $sql = "INSERT INTO rooms (room_number) VALUES ('$room_number')";
                if ($conn->query($sql) === TRUE) {
                    $addedRooms[] = $room_number;
                } else {
                    $errors[] = "Error adding room $room_number: " . $conn->error;
                }
            }
        }
    }

    if (!empty($addedRooms)) {
        echo '<script>alert("New rooms created successfully: ' . implode(', ', $addedRooms) . '");</script>';
    }
    if (!empty($existingRooms)) {
        $roomError = "Room numbers already exist: " . implode(', ', $existingRooms);
        $roomValue = implode(', ', $room_numbers); // Preserve the value of room field
    }
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    }
}

// Check if the delete button is clicked
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    // Sanitize the input to prevent SQL injection
    $room_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

    // SQL to delete the room from the rooms table
    $sql_delete_room = "DELETE FROM rooms WHERE room_id = '$room_id'";

    if ($conn->query($sql_delete_room) === TRUE) {
        echo "<script>alert('Room deleted successfully.');</script>";
    } else {
        echo "Error deleting room: " . $conn->error;
    }
}

// Fetch existing rooms
$rooms = [];
$sql = "SELECT room_id, room_number FROM rooms";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP| Add Room</title>
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
<?php include 'nav.php'; ?>

<div class="wrapper">
  <?php include 'sidebar.php'; ?>

  <div class="container-fluid" id="content">
    <div class="row">
      <div class="col-md-12">
        <!-- BREADCRUMBS -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Room</li>
          </ol>
        </nav>

        <!-- Add Room Form -->
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Add Rooms</h2>
          <form action="addRoom.php" method="post">
            <div class="row mb-3 align-items-center">
              <div class="col-auto">
                <label for="roomNos" class="col-form-label">Room Nos.</label>
              </div>
              <div class="col">
                <input type="text" class="form-control" id="roomNos" placeholder="Enter Room numbers separated by commas..." name="room_numbers" value="<?php echo htmlspecialchars($roomValue); ?>" required>
                <div class="text-danger"><?php echo $roomError; ?></div>
              </div>
            </div>
            <div class="d-grid gap-2 d-md-block">
              <a href="room.php" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
        
      </div>
    </div>
  </div>
</div>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

</body>
</html>
