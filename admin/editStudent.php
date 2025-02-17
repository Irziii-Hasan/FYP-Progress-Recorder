<?php
include 'session_admin.php'; // Include session handling

$emailError = ""; 
$nameValue = ""; 
$enrollmentValue = ""; 
$degreeProgramValue = ""; 
$batchValue = ""; 
$phone_number = "";
$seat_number = "";
$juw_id = "";
$juwIdError = "";

$batches = [];
$degreePrograms = [];
$batchesAvailable = false;

include 'config.php';

// Fetch batches from the batches table
$sql = "SELECT batchName FROM batches";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $batchesAvailable = true;
    while($row = $result->fetch_assoc()) {
        $batches[] = $row['batchName'];
    }
}

// Fetch degree programs from the degree_programs table
$sql_degree_programs = "SELECT * FROM batches"; // Assuming you have a degree_programs table
$result_degree_programs = $conn->query($sql_degree_programs);
if ($result_degree_programs->num_rows > 0) {
    while($row = $result_degree_programs->fetch_assoc()) {
        $degreePrograms[] = $row['abbreviation']; // Assuming the degree_programs table has a column named 'program_name'
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $enrollment = htmlspecialchars($_POST['enrollment']);
    $phone_number = htmlspecialchars($_POST['phone_number']);
    $seat_number = htmlspecialchars($_POST['seat_number']);
    $juw_id = htmlspecialchars($_POST['juw_id']);
    $role = "Student";
    $degree_program = htmlspecialchars($_POST['degree-program']);
    $batch = htmlspecialchars($_POST['batch']);

    $juw_id_old = htmlspecialchars($_POST['juw_id_old']);

    $email = trim($_POST["email"]);
    if (empty($email)) {
        $emailError = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format";
    } else {
        $query = "SELECT * FROM user WHERE email = '$email' AND email != (SELECT email FROM student WHERE juw_id = '$juw_id_old')";
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $emailError = "Email already exists";
        }
    }

    if (empty($emailError)) {
        $username = strtolower(str_replace(' ', '', $name));
        $password = $username . substr($enrollment, -4);

        $sql_student = "UPDATE student SET 
                            juw_id = '$juw_id', 
                            username = '$name', 
                            email = '$email', 
                            enrollment = '$enrollment', 
                            batch = '$batch', 
                            degree_program = '$degree_program', 
                            phone_number = '$phone_number', 
                            seat_number = '$seat_number' 
                        WHERE juw_id = '$juw_id_old'";

        if ($conn->query($sql_student) === TRUE) {
            $sql_user = "UPDATE user SET 
                            username = '$name', 
                            email = '$email', 
                            password = '$password' 
                        WHERE email = (SELECT email FROM student WHERE juw_id = '$juw_id')";

            if ($conn->query($sql_user) === TRUE) {
                echo '<script>alert("Record updated successfully");</script>';
                echo '<script>window.location.href = "student.php";</script>';
            } else {
                echo '<script>alert("Error: ' . $sql_user . '<br>' . $conn->error . '");</script>';
            }
        } else {
            echo '<script>alert("Error: ' . $sql_student . '<br>' . $conn->error . '");</script>';
        }
    }
} else {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $juw_id = htmlspecialchars($_GET['id']);

        $sql_student = "SELECT * FROM student WHERE juw_id = '$juw_id'";
        $result_student = $conn->query($sql_student);

        if ($result_student->num_rows > 0) {
            $student = $result_student->fetch_assoc();
            $nameValue = $student['username'];
            $emailValue = $student['email'];
            $enrollmentValue = $student['enrollment'];
            $degreeProgramValue = $student['degree_program'];
            $batchValue = $student['batch'];
            $phone_number = $student['phone_number'];
            $seat_number = $student['seat_number'];
            $juw_id = $student['juw_id'];
        } else {
            echo '<script>alert("Student not found");</script>';
            echo '<script>window.location.href = "student.php";</script>';
        }
    } else {
        echo '<script>alert("Invalid request");</script>';
        echo '<script>window.location.href = "student.php";</script>';
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP| Edit Student</title>
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
    .form-all{
      padding: 20px 30px;
      border: 1px solid #cbcbcb;
      border-radius: 20px;
      background-color:white;
    }

    .form-heading{
      color:#0a4a91;
      font-weight: 700;
    }

    label{
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
            <li class="breadcrumb-item"><a href="student.php">Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Edit Student</h2>
          <?php if (!$batchesAvailable): ?>
            <div class="alert alert-warning" role="alert">
              No batches found. Please enroll batches first.
            </div>
            <div class="d-grid gap-2 d-md-block">
              <a href="addBatch.php" class="btn btn-primary">Enroll Batches</a>
            </div>
          <?php else: ?>
            <form action="editstudent.php" method="post">
              <!-- User ID field (with validation) -->
              <div class="mb-3 mt-3">
                <label for="juw_id" class="form-label">User ID:</label>
                <input type="text" class="form-control" id="juw_id" placeholder="Enter User ID" name="juw_id" value="<?php echo htmlspecialchars($juw_id); ?>" required readonly>
              </div>

              <!-- Student Name -->
              <div class="mb-3 mt-3">
                <label for="name">Student Name:</label>
                <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" value="<?php echo htmlspecialchars($nameValue); ?>" required>
              </div>

              <!-- Email -->
              <div class="mb-3 mt-3">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?php echo htmlspecialchars($emailValue); ?>" required>
                <span class="error" style="color: red;"><?php echo $emailError; ?></span>
              </div>

              <!-- Enrollment Number -->
              <div class="mb-3 mt-3">
                <label for="enrollment">Enrollment Number:</label>
                <input type="text" class="form-control" id="enrollment" placeholder="Enter Enrollment" name="enrollment" value="<?php echo htmlspecialchars($enrollmentValue); ?>" required>
              </div>

              <!-- Phone Number -->
              <div class="mb-3 mt-3">
                <label for="phone_number" class="form-label">Phone Number:</label>
                <input type="text" class="form-control" id="phone_number" placeholder="Enter Phone Number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
              </div>

              <!-- Seat Number -->
              <div class="mb-3 mt-3">
                <label for="seat_number" class="form-label">Seat Number:</label>
                <input type="text" class="form-control" id="seat_number" placeholder="Enter Seat Number" name="seat_number" value="<?php echo htmlspecialchars($seat_number); ?>" required>
              </div>

              <!-- Degree Program (Fetched from database) -->
              <div class="mb-3">
                <label for="degree-program">Degree Program:</label>
                <select class="form-select" id="degree-program" name="degree-program" required>
                  <?php foreach ($degreePrograms as $program): ?>
                    <option value="<?php echo htmlspecialchars($program); ?>" <?php echo ($degreeProgramValue == $program) ? 'selected' : ''; ?>><?php echo htmlspecialchars($program); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <!-- Batch (Fetched from database) -->
              <div class="mb-3">
                <label for="batch">Batch:</label>
                <select class="form-select" id="batch" name="batch" required>
                  <?php foreach ($batches as $batch): ?>
                    <option value="<?php echo htmlspecialchars($batch); ?>" <?php echo ($batchValue == $batch) ? 'selected' : ''; ?>><?php echo htmlspecialchars($batch); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <a href="student.php" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">Update</button>
              <input type="hidden" name="juw_id_old" value="<?php echo htmlspecialchars($juw_id); ?>">
            </form>

          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.sidebar ul li').classList.remove('active');
    document.querySelector('#li_students').classList.add('active');
  });
</script>
</body>
</html>
