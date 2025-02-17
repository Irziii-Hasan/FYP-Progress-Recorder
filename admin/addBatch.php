<?php

include 'session_admin.php'; // Include session handling
include 'config.php';

$batchError = "";
$abbError = "";
$batchValue = "";
$durationError = "";
$durationValue = "";

// Fetch course durations for the dropdown
$courseDurations = $conn->query("SELECT id, title FROM course_durations");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $batch_name = trim($_POST['batch_name']);
    $batch_abbreviation = trim( $_POST['batch_abbreviation']);
    $course_duration_id = isset($_POST['course_duration']) ? $_POST['course_duration'] : NULL;

    // Validate batch name and abbreviation
    if (empty($batch_name)) {
      $batchError = "Batch name is required";
  } elseif (!preg_match("/^[A-Za-z]+-[0-9]+$/", $batch_name)) {
      $batchError = "Batch name must be in format (e.g., SE-2021)";
      $batchValue = $batch_name;
  } elseif (empty($batch_abbreviation)) {
      $abbError = "Batch Abbreviation is required";
  } elseif (!preg_match("/^[A-Za-z ]+$/", $batch_abbreviation)) {
      $abbError = "Batch Abbreviation can only contain letters and spaces";
  } else {
    
        // Check if batch name already exists in the database
        $query = "SELECT * FROM batches WHERE BatchName = '$batch_name'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $batchError = "Batch already exists";
            $batchValue = $batch_name;
        } else {
            // Insert new batch
            $sql = "INSERT INTO batches (BatchName, abbreviation, course_duration_id) VALUES ('$batch_name', '$batch_abbreviation', " . ($course_duration_id ? "'$course_duration_id'" : "NULL") . ")";
            if ($conn->query($sql) === TRUE) {
                echo '<script>alert("New batch created successfully");</script>';
            } else {
                echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP | Add Batch</title>
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
            <li class="breadcrumb-item active" aria-current="page">Add Batch</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Add Batch</h2>
          <form action="addBatch.php" method="post">
            <div class="row mb-3 align-items-center">
              <div class="col-auto">
                <label for="batchName" class="col-form-label">Batch Name</label>
              </div>
              <div class="col">
                <input type="text" class="form-control" id="batchName" placeholder="SE-2021" name="batch_name" value="<?php echo htmlspecialchars($batchValue); ?>" required>
                <div class="text-danger"><?php echo $batchError; ?></div>
              </div>
            </div>
            <div class="row mb-3 align-items-center">
              <div class="col-auto">
                <label for="abbreviation" class="col-form-label">Degree Program</label>
              </div>
              <div class="col">
                <input type="text" class="form-control" id="abbreviation" placeholder="Software Engineering" name="batch_abbreviation" required>
                <div class="text-danger"><?php echo $abbError; ?></div>
                </div>
            </div>

            <!-- Course Duration Dropdown -->
            <div class="row mb-3 align-items-center">
              <div class="col-auto">
                <label for="courseDuration" class="col-form-label">Course Duration (Optional)</label>
              </div>
              <div class="col">
                <select class="form-select" id="courseDuration" name="course_duration">
                  <option value="">Select Duration</option>
                  <?php while ($row = $courseDurations->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo $row['title']; ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
            </div>

            <div class="d-grid gap-2 d-md-block">
              <a href="batch.php" class="btn btn-light">Cancel</a>
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
