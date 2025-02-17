<?php
include 'session_admin.php'; // Include session handling
include 'config.php';

$batchError = ""; // Initialize batch error variable
$abbError = "";
$batchValue = ""; // Initialize batch value variable
$abbValue = "";
$batchId = ""; // Initialize batch ID variable
$selectedCourseDurationId = ""; // Initialize selected course duration variable

// Fetch batch details if the ID is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $batchId = $_GET['id'];
    $query = "SELECT * FROM batches WHERE BatchID = '$batchId'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $batchValue = $row['BatchName'];
        $abbValue = $row['abbreviation'];
        
        $selectedCourseDurationId = $row['course_duration_id']; // Fetch the selected course duration ID
    } else {
        echo "<script>alert('Batch not found.'); window.location.href='batch.php';</script>";
        exit();
    }
}

// Update batch details when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['batch_id'])) {
    $batchId = $_POST['batch_id'];
    $batchValue = trim($_POST['batch_name']);
    $abbValue = trim($_POST['batch_abbreviation']);
    

    $selectedCourseDurationId = $_POST['course_duration_id']; // Get the selected course duration

    if (empty($batchValue)) {
        $batchError = "Batch name is required";
    } else if(empty($abbValue)){
      $abbError = "Degree Program is required";

    } else{
        // Check if batch name already exists in the database, excluding the current batch being edited
        $query = "SELECT * FROM batches WHERE BatchName = '$batchValue' AND BatchID != '$batchId'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $batchError = "Batch name already exists: $batchValue";
        } else {
            // Update batch details
            $sql = "UPDATE batches SET BatchName = '$batchValue', course_duration_id = '$selectedCourseDurationId', abbreviation ='$abbValue' WHERE BatchID = '$batchId'";
            if ($conn->query($sql) === TRUE) {
                echo '<script>alert("Batch updated successfully."); window.location.href="batch.php";</script>';
            } else {
                echo "Error updating batch: " . $conn->error;
            }
        }
    }
}

// Fetch all course durations
$courseDurations = [];
$durationQuery = "SELECT id, title FROM course_durations";
$durationResult = $conn->query($durationQuery);
if ($durationResult->num_rows > 0) {
    while ($row = $durationResult->fetch_assoc()) {
        $courseDurations[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP | Edit Batch</title>
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
            <li class="breadcrumb-item"><a href="batch.php">Batch</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Batch</li>
          </ol>
        </nav>

        <!-- Edit Batch Form -->
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Edit Batch</h2>
          <form action="editBatch.php" method="post">
            <input type="hidden" name="batch_id" value="<?php echo htmlspecialchars($batchId); ?>">
            <div class="row mb-3 align-items-center">
              <div class="col-auto">
                <label for="batchName" class="col-form-label">Batch Name</label>
              </div>
              <div class="col">
                <input type="text" class="form-control" id="batchName" placeholder="Enter Batch Here..." name="batch_name" value="<?php echo htmlspecialchars($batchValue); ?>" required>
                <div class="text-danger"><?php echo $batchError; ?></div>
              </div>
            </div>

            <div class="row mb-3 align-items-center">
              <div class="col-auto">
                <label for="abbreviation" class="col-form-label">Degree Program</label>
              </div>
              <div class="col">
                <input type="text" class="form-control" id="abbreviation" placeholder="Software Engineering" name="batch_abbreviation" value="<?php echo htmlspecialchars($abbValue); ?>" required>
                <div class="text-danger"><?php echo $abbError; ?></div>
              </div>
            </div>

            <!-- Course Duration Dropdown -->
            <div class="row mb-3 align-items-center">
              <div class="col-auto">
                <label for="courseDuration" class="col-form-label">Course Duration</label>
              </div>
              <div class="col">
                <select class="form-select" id="courseDuration" name="course_duration_id">
                  <option value="">Select Course Duration</option>
                  <?php
                  foreach ($courseDurations as $duration) {
                      echo '<option value="' . $duration['id'] . '"' . ($selectedCourseDurationId == $duration['id'] ? ' selected' : '') . '>' . $duration['title'] . '</option>';
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="d-grid gap-2 d-md-block">
              <a href="batch.php" class="btn btn-light">Cancel</a>
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
