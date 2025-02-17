<?php
include 'session_supervisor.php';

// Initialize error and value variables
$titleError = $dateError = $timeError = $projectError = $descriptionError = "";
$titleValue = $dateValue = $timeValue = $projectValue = $descriptionValue = "";

// Initialize projects array and available flag
$projects = [];
$projectsAvailable = false;

// Get supervisor ID from session
$supervisor_id = $_SESSION['faculty_id']; // Use session faculty_id for the current supervisor

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input fields
    $title = htmlspecialchars($_POST['title']);
    $date = htmlspecialchars($_POST['date']);
    $time = htmlspecialchars($_POST['time']);
    $project = htmlspecialchars($_POST['project']);
    $description = htmlspecialchars($_POST['description']);

    if (empty($title)) {
        $titleError = "Title is required";
    } else {
        $titleValue = $title;
    }

    if (empty($date)) {
      $dateError = "Date is required";
  } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
      $dateError = "The meeting date cannot be in the past.";
  } else {
      $dateValue = $date;
  }
  

    // Validate meeting time to ensure it's not during the night hours (e.g., 8 PM to 7 AM)
if (empty($time)) {
  $timeError = "Time is required";
} elseif (strtotime($time) >= strtotime('20:00') || strtotime($time) < strtotime('07:00')) {
  $timeError = "Meetings cannot be scheduled at night (between 8 PM and 7 AM).";
} else {
  $timeValue = $time;
}

    if (empty($project)) {
        $projectError = "Project is required";
    } else {
        $projectValue = $project;
    }

    if (empty($description)) {
        $descriptionError = "Description is required";
    } else {
        $descriptionValue = $description;
    }

    if (empty($titleError) && empty($dateError) && empty($timeError) && empty($projectError) && empty($descriptionError)) {
        include 'config.php';

        // Prepare the SQL statement with faculty_id
        $stmt = $conn->prepare("INSERT INTO meetings (title, description, date, time, project_id, supervisor_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssii", $title, $description, $date, $time, $project, $supervisor_id);

        if ($stmt->execute()) {
          header("Location: meetings.php");

            echo '<script>alert("Meeting scheduled successfully");</script>';
            echo '<script>window.location.href = "scheduleMeeting.php";</script>';
        } else {
            echo '<script>alert("Error: ' . $stmt->error . '");</script>';
        }

        $stmt->close();
        $conn->close();
    }
} else {
    // Fetch projects where the current supervisor is assigned
    include 'config.php';

    $sql = "SELECT id, title FROM projects WHERE supervisor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supervisor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $projectsAvailable = true;
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP | Schedule Meeting</title>
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
    .error {
      color: red;
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
            <li class="breadcrumb-item"><a href="meetings.php">Meetings</a></li>
            <li class="breadcrumb-item active" aria-current="page">Schedule Meeting</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Schedule Meeting</h2>
        
            <form action="schedulemeeting.php" method="post">
              <div class="mb-3 mt-3">
                <label for="title" class="form-label">Meeting Title:</label>
                <input type="text" class="form-control" id="title" placeholder="Enter Meeting Title" name="title" value="<?php echo htmlspecialchars($titleValue); ?>" required>
                <span class="error"><?php echo $titleError; ?></span>
              </div>
              <div class="mb-3 mt-3">
                <label for="description" class="form-label">Meeting Description:</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($descriptionValue); ?></textarea>
                <span class="error"><?php echo $descriptionError; ?></span>
              </div>
              <div class="mb-3 mt-3">
                <label for="project" class="form-label">Project Name:</label>
                <select id="project" class="form-select" name="project" required>
                  <option value="" disabled selected>Select project</option>
                  <?php foreach($projects as $project): ?>
                    <option value="<?php echo $project['id']; ?>" <?php if ($projectValue == $project['id']) echo 'selected'; ?>>
                      <?php echo $project['title']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <span class="error"><?php echo $projectError; ?></span>
              </div>
              <div class="mb-3 mt-3">
                <label for="date" class="form-label">Meeting Date:</label>
                <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($dateValue); ?>" min="<?php echo date('Y-m-d'); ?>" required>
                <span class="error"><?php echo $dateError; ?></span>
              </div>
              <div class="mb-3 mt-3">
                <label for="time" class="form-label">Meeting Time:</label>
                <input type="time" class="form-control" id="time" name="time" value="<?php echo htmlspecialchars($timeValue); ?>" required>
                <span class="error"><?php echo $timeError; ?></span>
              </div>              
              <div class="d-grid gap-2 d-md-block">
                <a href="meetings.php" class="btn btn-light">Cancel</a>
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
