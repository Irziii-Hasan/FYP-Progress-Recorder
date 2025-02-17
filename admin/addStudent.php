<?php
include 'session_admin.php'; // Include session handling

$nameError = $emailError = $enrollmentError = $phoneError = $seatError = $juwIdError = $batchError = "";
$batches = [];
$batchesAvailable = false;

include 'config.php';

// Fetch batches and degree programs from the database
$sql = "SELECT batchName, abbreviation FROM batches";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $batchesAvailable = true;
    while ($row = $result->fetch_assoc()) {
        $batches[] = $row; // Store both batchName and abbreviation
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $juw_id = strtolower(trim($_POST['juw_id']));
    $name = htmlspecialchars(trim($_POST['name']));
    $email = strtolower(trim($_POST['email']));
    $enrollment = htmlspecialchars(trim($_POST['enrollment']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $seat_number = htmlspecialchars(trim($_POST['seat_number']));
    $degree_program = htmlspecialchars(trim($_POST['degree-program']));
    $batch = htmlspecialchars(trim($_POST['batch']));
    $role = "Student";

    // Validate inputs
    if (empty($juw_id) || !preg_match('/^[a-z0-9]+$/', $juw_id)) {
        $juwIdError = "User ID must be alphanumeric.";
    } else {
        $query = "SELECT * FROM student WHERE juw_id = '$juw_id'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $juwIdError = "User ID already exists.";
        }
    }

    if (empty($name) || !preg_match('/^[a-zA-Z ]+$/', $name)) {
        $nameError = "Name must only contain alphabets.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format.";
    } else {
        $query = "SELECT * FROM user WHERE email = '$email'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $emailError = "Email already exists.";
        }
    }

    if (empty($enrollment) || !preg_match('/^\d{4}\/Comp\/(BS|MS)\(\w{2}\)\/\d+$/', $enrollment)) {
        $enrollmentError = "Invalid enrollment format.";
    }

    if (empty($phone_number) || !preg_match('/^03\d{9}$/', $phone_number)) {
        $phoneError = "Phone number must start with 03 and contain 11 digits.";
    }

    if (empty($seat_number) || !preg_match('/^[a-zA-Z0-9]+$/', $seat_number)) {
        $seatError = "Seat number must not contain spaces or special characters.";
    }

    $validBatches = array_column($batches, 'batchName');
    if (empty($batch) || !in_array($batch, $validBatches)) {
        $batchError = "Please select a valid batch.";
    }

    if (empty($emailError) && empty($juwIdError) && empty($batchError)) {
        // Generate a strong random password
        $password = bin2hex(random_bytes(4)) . rand(1000, 9999) . "!@";

        // Correct the password insertion by enclosing the password in single quotes
$sql_student = "INSERT INTO student (
  juw_id, username, email, enrollment, 
  batch, degree_program, phone_number, seat_number, password
) VALUES (
  '$juw_id', '$name', '$email', '$enrollment', 
  '$batch', '$degree_program', '$phone_number', '$seat_number', '$password'
)";


       
            if ($conn->query($sql_student) === TRUE) {
                echo '<script>alert("Student added successfully");</script>';
                echo '<script>window.location.href = "student.php";</script>';
            } else {
                echo '<script>alert("Error: ' . $conn->error . '");</script>';
            }
        
        }
    }


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP | Add Student</title>
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

    .error-message {
      color: red;
      font-size: 0.875em;
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
            <li class="breadcrumb-item active" aria-current="page">Add Student</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all" style="width: 650px;">
          <h2 class="text-center form-heading">Add Student</h2>
          <?php if (!$batchesAvailable): ?>
            <div class="alert alert-warning" role="alert">
              No batches found. Please enroll batches first.
            </div>
            <div class="d-grid gap-2 d-md-block">
              <a href="addBatch.php" class="btn btn-primary">Enroll Batches</a>
            </div>
          <?php else: ?>
            <form action="addstudent.php" method="post">
              <!-- User ID -->
              <div class="mb-3">
                  <label for="juw_id" class="form-label">User ID:</label>
                  <input type="text" class="form-control" id="juw_id" name="juw_id" placeholder="Enter User ID" required value="<?= htmlspecialchars($_POST['juw_id'] ?? '') ?>">
                  <div class="error-message"><?= $juwIdError; ?></div>
              </div>

              <!-- Student Name -->
              <div class="mb-3">
                  <label for="name" class="form-label">Student Name:</label>
                  <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                  <div class="error-message"><?= $nameError; ?></div>
              </div>

              <!-- Email -->
              <div class="mb-3">
                  <label for="email" class="form-label">Email:</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                  <div class="error-message"><?= $emailError; ?></div>
              </div>

              <!-- Enrollment Number -->
              <div class="mb-3">
                  <label for="enrollment" class="form-label">Enrollment Number:</label>
                  <input type="text" class="form-control" id="enrollment" name="enrollment" placeholder="e.g., 2021/Comp/BS(XX)/22222" required value="<?= htmlspecialchars($_POST['enrollment'] ?? '') ?>">
                  <small class="form-text text-muted">Format: YYYY/Comp/BS(XX)/XXXXX</small>
                  <div class="error-message"><?= $enrollmentError; ?></div>
              </div>

              <!-- Phone Number -->
              <div class="mb-3">
                  <label for="phone_number" class="form-label">Phone Number:</label>
                  <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter Phone Number" required value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
                  <small class="form-text text-muted">Format: 03XXXXXXXXX (11 digits, no spaces or dashes)</small>
                  <div class="error-message"><?= $phoneError; ?></div>
              </div>

              <!-- Seat Number -->
              <div class="mb-3">
                  <label for="seat_number" class="form-label">Seat Number:</label>
                  <input type="text" class="form-control" id="seat_number" name="seat_number" placeholder="Enter Seat Number" required value="<?= htmlspecialchars($_POST['seat_number'] ?? '') ?>">
                  <div class="error-message"><?= $seatError; ?></div>
              </div>

              <!-- Degree Program -->
              <div class="mb-3">
                  <label for="degree-program" class="form-label">Degree Program:</label>
                  <select class="form-select" id="degree-program" name="degree-program" required>
                      <option value="" disabled <?= empty($_POST['degree-program']) ? 'selected' : '' ?>>Select Degree Program</option>
                      <?php foreach ($batches as $batch): ?>
                          <option value="<?= $batch['abbreviation'] ?>" <?= (isset($_POST['degree-program']) && $_POST['degree-program'] === $batch['abbreviation']) ? 'selected' : '' ?>>
                              <?= $batch['abbreviation'] ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
              </div>

              <!-- Batch -->
              <div class="mb-3">
                  <label for="batch" class="form-label">Batch:</label>
                  <select class="form-select" id="batch" name="batch" required>
                      <option value="" disabled <?= empty($_POST['batch']) ? 'selected' : '' ?>>Select Batch</option>
                      <?php foreach ($batches as $batch): ?>
                          <option value="<?= $batch['batchName'] ?>" <?= (isset($_POST['batch']) && $_POST['batch'] === $batch['batchName']) ? 'selected' : '' ?>>
                              <?= $batch['batchName'] ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
                  <div class="error-message"><?= $batchError; ?></div>
              </div>

              <!-- Submit Button -->
              <a href="student.php" class="btn btn-light">Cancel</a>
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.sidebar ul li').classList.remove('active');
    document.querySelector('#li_students').classList.add('active');
  });
</script>
</body>
</html>
