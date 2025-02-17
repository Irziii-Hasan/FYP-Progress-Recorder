<?php
include 'session_admin.php'; // Include session handling

// Initialize error messages and values
$emailError = "";
$nameError = "";
$designationValue = "";
$juwIdValue = "";
$professional_emailError = "";
$professional_emailValue = "";

// Database connection 
include 'config.php';

if (isset($_GET['id'])) {
    $juw_id = htmlspecialchars($_GET['id']);

    // Fetch the current values for the selected faculty member
    $stmt = $conn->prepare("SELECT * FROM faculty WHERE juw_id = ?");
    $stmt->bind_param("s", $juw_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $juwIdValue = $row['juw_id'];
        $nameValue = $row['username'];
        $emailValue = $row['email'];
        $professional_emailValue = $row['professional_email'];
        $designationValue = $row['designation'];
    } else {
        echo '<script>alert("No record found");</script>';
        echo '<script>window.location.href = "faculty.php";</script>';
    }
} else {
    $juw_id = ''; // or handle the error appropriately
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $juw_id = isset($_POST['juw_id']) ? htmlspecialchars($_POST['juw_id']) : '';
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $professional_email = isset($_POST['professional_email']) ? htmlspecialchars($_POST['professional_email']) : '';
    $designation = isset($_POST['designation']) ? htmlspecialchars($_POST['designation']) : '';

     // Server-side validation
     if (!preg_match('/^[a-zA-Z0-9]+$/', $juw_id)) {
      $juwIdError = "User ID must contain only alphanumeric characters.";
  }
  if (!preg_match('/^[a-zA-Z.\s]+$/', $name)) {
      $nameError = "Faculty name must contain only alphabets and spaces.";
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailError = "Invalid email format.";
  }
  if (!preg_match('/^[a-zA-Z0-9._%+-]+@juw\.edu\.pk$/', $professional_email)) {
      $professional_emailError = "Professional email must end with @juw.edu.pk.";
  }
  if ($email === $professional_email) {
      $professional_emailError = "Professional email cannot be the same as personal email.";
  }
    // Proceed if there are no validation errors
    if (empty($emailError) && empty($nameError) && empty($professional_emailError)) {
        // Update the faculty record in the database
        $stmt = $conn->prepare("UPDATE faculty SET juw_id=?, username=?, email=?, professional_email=?, designation=? WHERE juw_id=?");
        $stmt->bind_param("ssssss", $juw_id, $name, $email, $professional_email, $designation, $juw_id);
        if ($stmt->execute()) {
            echo '<script>alert("Record updated successfully");</script>';
            echo '<script>window.location.href = "faculty.php";</script>';
        } else {
            echo '<script>alert("Error updating faculty record: ' . $conn->error . '");</script>';
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit Faculty</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Custom styles -->
  <link rel="stylesheet" href="style.css">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
            <li class="breadcrumb-item"><a href="faculty.php">Faculty</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Faculty</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Edit Faculty</h2>
          <form action="editfaculty.php?id=<?php echo htmlspecialchars($juw_id); ?>" method="post">
            <div class="mb-3 mt-3">
              <label  for="juw_id">User ID:</label>
              <input type="text" class="form-control" id="juw_id" name="juw_id" value="<?php echo htmlspecialchars($juwIdValue); ?>" required readonly>
            </div>
            <div class="mb-3 mt-3">
              <label for="name">Faculty Name:</label>
              <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" value="<?php echo htmlspecialchars($nameValue); ?>" required>
              <span class="error"><?php echo $nameError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="email">Personal Email:</label>
              <input type="email" class="form-control" id="email" placeholder="Enter Personal Email" name="email" value="<?php echo htmlspecialchars($emailValue); ?>" required>
              <span class="error"><?php echo $emailError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="professional_email">Professional Email:</label>
              <input type="email" class="form-control" id="professional_email" placeholder="Enter Professional Email" name="professional_email" value="<?php echo htmlspecialchars($professional_emailValue); ?>" required>
              <span class="error"><?php echo $professional_emailError; ?></span>
            </div>
            <div class="mb-3 mt-3">
              <label for="designation">Designation:</label>
              <select class="form-select" id="designation" name="designation" required>
                <option>Select Designation</option>
                <option value="Professor" <?php echo ($designationValue == "Professor") ? "selected" : ""; ?>>Professor</option>
                <option value="Assistant Professor" <?php echo ($designationValue == "Assistant Professor") ? "selected" : ""; ?>>Assistant Professor</option>
                <option value="Lecturer" <?php echo ($designationValue == "Lecturer") ? "selected" : ""; ?>>Lecturer</option>
                <option value="Lab Instructor" <?php echo ($designationValue == "Lab Instructor") ? "selected" : ""; ?>>Lab Instructor</option>
              </select>
            </div>
            <div class="d-grid gap-2 d-md-block">
              <a href="faculty.php" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
</body>
</html>
