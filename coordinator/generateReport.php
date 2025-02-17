<?php
include 'session_coordinator.php';
include 'config.php';

// Fetch event types from the database
$query = "SELECT DISTINCT eventName FROM events";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching event types: " . $conn->error);
}

$eventTypes = [];
while ($row = $result->fetch_assoc()) {
    $eventTypes[] = $row['eventName'];
}

// Fetch batch names from the database
$queryBatches = "SELECT DISTINCT batchName, batchid FROM batches";
$resultBatches = $conn->query($queryBatches);

if (!$resultBatches) {
    die("Error fetching batch names: " . $conn->error);
}

$batches = [];
while ($rowBatch = $resultBatches->fetch_assoc()) {
    $batches[] = $rowBatch;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Generate Report</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <!-- Custom styles -->
  <link rel="stylesheet" href="style.css">
  <style>
    .centered-form {
      border: 1.5px solid #ddd;
      border-radius: 10px;
      padding: 30px; /* Reduced padding */
      max-width: 700px; /* Increased width */
      margin: auto;
      background-color: #f9f9f9;
      padding-left: 50px; /* Added left padding */
    }

    .form-check {
      margin-bottom: 10px; /* Reduced space between attributes */
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 15px; /* Reduced space between columns */
    }

    .form-check {
      flex: 1 1 45%; /* Ensure two attributes fit in one row */
    }

    .form-group {
      margin-bottom: 15px; /* Reduced margin-bottom */
    }

    .breadcrumb {
      margin-bottom: 20px;
    }

    h1 {
      margin-bottom: 20px; /* Space below the heading */
    }

    /* Center align table headings and cells */
    .table th, .table td {
      text-align: center;
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
            <li class="breadcrumb-item"><a href="view_Schedule.php">View Schedule</a></li>
            <li class="breadcrumb-item active" aria-current="page">Generate Report</li>
          </ol>
        </nav>
        <div class="container mt-5">
          <h1 class="text-center">Generate Report</h1>
          <form action="processReport.php" method="post" class="d-flex flex-column align-items-center">
            <div class="centered-form mb-3">
              <div class="form-row">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="columns[]" value="batch" id="batch">
                  <label class="form-check-label" for="batch">Batch</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="columns[]" value="date" id="date">
                  <label class="form-check-label" for="date">Date</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="columns[]" value="time" id="time">
                  <label class="form-check-label" for="time">Time</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="columns[]" value="project_id" id="project_id">
                  <label class="form-check-label" for="project_id">Project Title</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="columns[]" value="internal_evaluator_id" id="internal_evaluator_id">
                  <label class="form-check-label" for="internal_evaluator_id">Internal Evaluator</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="columns[]" value="external_evaluator_id" id="external_evaluator_id">
                  <label class="form-check-label" for="external_evaluator_id">External Evaluator</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="columns[]" value="room_number" id="room_number">
                  <label class="form-check-label" for="room_number">Room Number</label>
                </div>
              </div>
              <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-select" id="type" name="type" required>
                  <option value="" disabled selected>Select a type</option>
                  <?php foreach ($eventTypes as $eventType): ?>
                    <option value="<?php echo htmlspecialchars($eventType); ?>"><?php echo htmlspecialchars($eventType); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="batch" class="form-label">Batch</label>
                <select class="form-select" id="batch" name="batch" required>
                  <option value="" disabled selected>Select a batch</option>
                  <?php foreach ($batches as $batch): ?>
                    <option value="<?php echo htmlspecialchars($batch['batchid']); ?>"><?php echo htmlspecialchars($batch['batchName']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <button type="submit" class="btn btn-primary mt-4">Generate Report</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>