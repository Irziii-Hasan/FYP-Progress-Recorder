<?php
include 'session_admin.php'; // Include session handling
include 'config.php';

// Initialize error variables
$projectError = $titleError = $descriptionError = $studentError = $supervisorError = $batchError = "";
$projectValue = $titleValue = $descriptionValue = $student1Value = $student2Value = $supervisorValue = $batchValue = "";
$external_supervisor='';
// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = trim($_POST['project_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $student1 = trim($_POST['student1']);
    $student2 = trim($_POST['student2']);
    $student3 = trim($_POST['student3']);
    $student4 = trim($_POST['student4']);
    $supervisor = trim($_POST['supervisor']);
    $co_supervisor = trim($_POST['co_supervisor']);
    $external_supervisor = trim($_POST['external_supervisor']);
    $batch = trim($_POST['batch']);
    $duration = trim($_POST['duration']);

    // Validate input
    if (empty($project_id)) {
        $projectError = "Project ID is required";
    }
    if (empty($title)) {
        $titleError = "Title is required";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $title)) {
        $titleError = "Title must contain only alphabets and spaces";
    }
    if (empty($description)) {
        $descriptionError = "Description is required";
    }
    if (empty($student1) || empty($student2)) {
        $studentError = "At least two students are required";
    }
    if (empty($supervisor)) {
        $supervisorError = "Supervisor is required";
    }

    if (empty($projectError) && empty($titleError) && empty($descriptionError) && empty($studentError) && empty($supervisorError)) {
        // Insert into projects table
        $stmt = $conn->prepare("INSERT INTO projects (project_id, title, description, student1, student2, student3, student4, supervisor, co_supervisor, external_supervisor, batch, duration, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?, NOW())");
        $stmt->bind_param("sssssssssssi", $project_id, $title, $description, $student1, $student2, $student3, $student4, $supervisor, $co_supervisor, $external_supervisor, $batch, $duration);

        if ($stmt->execute()) {
          $projectt_id=$conn->insert_id;
          $api_url = "http://127.0.0.1:5000/recommend?id=" . $projectt_id;
          $response = file_get_contents($api_url);
          echo "<p>Project added successfully! AI recommendations updated.</p>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Get students who are not assigned to any project
$studentOptions = "";
$currentYear = date("Y");
$sql = "
    SELECT student_id, username 
    FROM student 
    WHERE  student_id NOT IN (
        SELECT student1 FROM projects 
        UNION 
        SELECT student2 FROM projects 
        UNION 
        SELECT student3 FROM projects 
        UNION 
        SELECT student4 FROM projects
    )
";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $studentOptions .= "<option value='{$row['student_id']}'>{$row['username']}</option>";
    }
}


// Get faculty options
$facultyOptions = "";
$sql = "SELECT faculty_id, username FROM faculty";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $facultyOptions .= "<option value='{$row['faculty_id']}'>{$row['username']}</option>";
    }
}

// Get external supervisor options
$externalOptions = "";
$sql = "SELECT external_id, name FROM external";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $externalOptions .= "<option value='{$row['external_id']}'>{$row['name']}</option>";
    }
}

// Get batch options
$batchOptions = "";
$sql = "SELECT batchID, BatchName FROM batches";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $batchOptions .= "<option value='{$row['batchID']}'>{$row['BatchName']}</option>";
    }
}


// Get Duration options
$durationOptions = "";
$sql = "SELECT id, title FROM course_durations";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $durationOptions .= "<option value='{$row['id']}'>{$row['title']}</option>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP | Assign Project</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
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
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
            <li class="breadcrumb-item"><a href="project.php">Projects</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assign Project</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Assign Project</h2>
          <form action="addproject.php" method="post">
            <div class="mb-3 mt-3">
              <label for="project_id">Project ID:</label>
              <input type="text" class="form-control" id="project_id" name="project_id" placeholder="Enter Project ID" value="<?php echo htmlspecialchars($projectValue); ?>" required>
              <span class="text-danger"><?php echo $projectError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="title">Title:</label>
              <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" value="<?php echo htmlspecialchars($titleValue); ?>" required>
              <span class="text-danger"><?php echo $titleError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="description">Description:</label>
              <textarea class="form-control" id="description" name="description" rows="3" placeholder="Enter Description" required><?php echo htmlspecialchars($descriptionValue); ?></textarea>
              <span class="text-danger"><?php echo $descriptionError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="student1">Student 1:</label>
              <select class="form-control" id="student1" name="student1" required>
                <option value="" selected>Select Student</option>
                <?php echo $studentOptions; ?>
              </select>
            </div>
            <div class="mb-3 mt-3">
              <label for="student2">Student 2:</label>
              <select class="form-control" id="student2" name="student2" required>
                <option value="" selected>Select Student</option>
                <?php echo $studentOptions; ?>
              </select>
              <span class="text-danger"><?php echo $studentError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="student3">Student 3:</label>
              <select class="form-control" id="student3" name="student3">
                <option value="" selected>Select Student</option>
                <?php echo $studentOptions; ?>
              </select>
            </div>
            <div class="mb-3 mt-3">
              <label for="student4">Student 4:</label>
              <select class="form-control" id="student4" name="student4">
                <option value="" selected>Select Student</option>
                <?php echo $studentOptions; ?>
              </select>
            </div>
            <div class="mb-3 mt-3">
              <label for="supervisor">Supervisor:</label>
              <select class="form-control" id="supervisor" name="supervisor" required>
                <option value="" selected>Select Supervisor</option>
                <?php echo $facultyOptions; ?>
              </select>
              <span class="text-danger"><?php echo $supervisorError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="co_supervisor">Co-Supervisor:</label>
              <select class="form-control" id="co_supervisor" name="co_supervisor">
                <option value="" selected>Select Co-Supervisor</option>
                <?php echo $facultyOptions; ?>
              </select>
            </div>
            <div class="mb-3 mt-3">
  <label for="external_supervisor">External Supervisor:</label>
  <input type="text" class="form-control" id="external_supervisor" name="external_supervisor" placeholder="Enter External Supervisor Name" value="<?php echo htmlspecialchars($external_supervisor); ?>">
</div>

            <div class="mb-3 mt-3">
              <label for="batch">Batch:</label>
              <select class="form-control" id="batch" name="batch" required>
                <option value="" disabled selected>Select Batch</option>
                <?php echo $batchOptions; ?>
              </select>
            </div>
            <div class="mb-3 mt-3">
              <label for="duration">FYP Duration:</label>
              <select class="form-control" id="duration" name="duration" required>
                <option value="" disabled selected>Select Duration</option>
                <?php echo $durationOptions; ?>
              </select>
            </div>
            <div class="d-grid gap-2 d-md-block">
              <a href="project.php" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
  $(document).ready(function() {
    $('#student1, #student2, #student3, #student4, #supervisor, #co_supervisor, #external_supervisor').select2({
      placeholder: 'Select',
      allowClear: true
    });
  });
</script>
</body>
</html>
