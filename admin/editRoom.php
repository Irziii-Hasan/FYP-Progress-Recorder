<?php
include 'session_admin.php'; // Include session handling
include 'config.php';

$roomError = ""; // Initialize room error variable
$roomValue = ""; // Initialize room value variable
$roomId = ""; // Initialize room ID variable

// Fetch room details if the ID is provided
if (isset($_GET['roomId']) && !empty($_GET['roomId'])) {
    $roomId = $_GET['roomId'];
    $query = "SELECT * FROM rooms WHERE room_id = '$roomId'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $roomValue = $row['room_number'];
    } else {
        echo "<script>alert('Room not found.'); window.location.href='room.php';</script>";
        exit();
    }
}

// Update room details when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['room_id'])) {
    $roomId = $_POST['room_id'];
    $roomValue = trim($_POST['room_number']);

    if (empty($roomValue)) {
        $roomError = "Room Number is required";
    } else {
        // Check if room_number already exists in the database, excluding the current room being edited
        $query = "SELECT * FROM rooms WHERE room_number = '$roomValue' AND room_id != '$roomId'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $roomError = "Room number already exists: $roomValue";
        } else {
            // Update room details
            $sql = "UPDATE rooms SET room_number = '$roomValue' WHERE room_id = '$roomId'";
            if ($conn->query($sql) === TRUE) {
                echo '<script>alert("Room updated successfully."); window.location.href="room.php";</script>';
            } else {
                echo "Error updating room: " . $conn->error;
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP | Edit Room</title>
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
            <li class="breadcrumb-item"><a href="room.php">Room</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Room</li>
          </ol>
        </nav>

        <!-- Edit Room Form -->
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Edit Room</h2>
          <form action="editRoom.php" method="post">
            <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($roomId); ?>">
            <div class="row mb-3 align-items-center">
              <div class="col-auto">
                <label for="roomNos" class="col-form-label">Room No.</label>
              </div>
              <div class="col">
                <input type="text" class="form-control" id="roomNos" placeholder="Enter Room number..." name="room_number" value="<?php echo htmlspecialchars($roomValue); ?>" required>
                <div class="text-danger"><?php echo $roomError; ?></div>
              </div>
            </div>
            <div class="d-grid gap-2 d-md-block">
              <a href="room.php" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">Update</button>
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
