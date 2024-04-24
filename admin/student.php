<?php
$emailError = ""; // Define $emailError variable to avoid undefined variable warning
$emailValue = ""; // Initialize $emailValue variable to store the value of email field
$nameValue = ""; // Initialize $nameValue variable to store the value of name field
$enrollmentValue = ""; // Initialize $enrollmentValue variable to store the value of enrollment field
$degreeProgramValue = ""; // Initialize $degreeProgramValue variable to store the value of degree-program field
$batchValue = ""; // Initialize $batchValue variable to store the value of batch field

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "localhost"; // Change this if your database server is hosted elsewhere
    $username = "root"; // Change this if your database username is different
    $password = ""; // Change this if your database password is different
    $dbname = "FYP_Progress_Recorder"; // Change this to the desired database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS `$dbname`";
    if ($conn->query($sql) === TRUE) {
        // echo "Database created successfully<br>";
    } else {
        echo "Error creating database: " . $conn->error;
    }

    // Select the database
    $conn->select_db($dbname);

    // Create user table
    $sql = "CREATE TABLE IF NOT EXISTS user (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) NOT NULL
    )";
    if ($conn->query($sql) === TRUE) {
        // echo "User table created successfully<br>";
    } else {
        echo "Error creating user table: " . $conn->error;
    }

    // Create student table
    $sql = "CREATE TABLE IF NOT EXISTS student (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        enrollment VARCHAR(50) NOT NULL,
        year INT(4) NOT NULL,
        degree_program VARCHAR(50) NOT NULL
    )";
    if ($conn->query($sql) === TRUE) {
        // echo "Student table created successfully<br>";
    } else {
        echo "Error creating student table: " . $conn->error;
    }

    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $enrollment = $_POST['enrollment'];
    $role = "Student"; // Since it's fixed in the form
    $degree_program = $_POST['degree-program'];
    $batch = $_POST['batch'];

    // Validate email
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $emailError = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format";
    } else {
        // Check if email already exists in the database
        $query = "SELECT * FROM student WHERE email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $emailError = "Email already exists";
            $emailValue = $email; // Preserve the value of email field
            $nameValue = $_POST['name']; // Preserve the value of name field
            $enrollmentValue = $_POST['enrollment']; // Preserve the value of enrollment field
            $degreeProgramValue = $_POST['degree-program']; // Preserve the value of degree-program field
            $batchValue = $_POST['batch']; // Preserve the value of batch field
        } else {
            // Generate password (username + last 4 digits of enrollment number)
            $username = strtolower(str_replace(' ', '', $name));
            $password = $username . substr($enrollment, -4);

            // SQL to insert data into student table
            $sql_student = "INSERT INTO student (username, email, enrollment, year, degree_program) 
                            VALUES ('$name', '$email', '$enrollment', '$batch', '$degree_program')";

            if ($conn->query($sql_student) === TRUE) {
                // SQL to insert data into user table
                $sql_user = "INSERT INTO user (username, email, password, role) 
                             VALUES ('$username', '$email', '$password', '$role')";

                if ($conn->query($sql_user) === TRUE) {
                    echo '<script>alert("New record created successfully");</script>';
                } else {
                    echo '<script>alert("Error: ' . $sql_user . '<br>' . $conn->error . '");</script>';
                }
            } else {
                echo '<script>alert("Error: ' . $sql_student . '<br>' . $conn->error . '");</script>';
            }
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>FYP| Student</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
      <!-- Bootstrap CSS -->
      <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <!-- Custom styles -->
  <link rel="stylesheet" href="style.css">
  <style>
    .form-all{
      width: 650px;
      padding: 20px 30px;
      border: 1px solid #cbcbcb;
      border-radius: 20px;
      background-color:white;
    }

    .form-heading{
      color:#0a4a91;
      font-weight: 700;
    }
  </style>
</head>
<body>

<div class="navbar header sticky-top">
  <div class="toggle-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars fa-2x"></i>
  </div>
  <div class="header-title">FYP Progress Recorder</div>
  <div class="user-name">John Doe <i class="fas fa-caret-down user-dropdown-icon"></i></div>
</div>
<div class="wrapper">
  <div class="sidebar" id="sidebar">
    <a href="#">Dashboard</a>
    <a href="#">Faculty</a>
    <a href="#">Student</a>
    <a href="#">Project</a>
    <a href="#">Result and Progress</a>
  </div>

  <div class="container-fluid" id="content">
    <div class="row">
      <div class="col-md-12">

        <!-- BREADCRUMBS -->
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb"> 
          <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
          <li class="breadcrumb-item"><a href="showuser.php">Student</a></li>
          <li class="breadcrumb-item active" aria-current="page">Add Student</li>
          </ol>
        </nav>
<div class="container mt-3 form-all">
  <h2 class="text-center form-heading">Add Student</h2>
  <form action="student.php" method="post">
    <div class="mb-3 mt-3">
        <label for="name">Student Name:</label>
        <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" value="<?php echo htmlspecialchars($nameValue); ?>" required>
      </div>
      <div class="mb-3 mt-3">
        <label for="email">Email:</label>
        <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?php echo htmlspecialchars($emailValue); ?>" required>
        <span class="error" style="color: red;"><?php echo $emailError; ?></span> <!-- Display email error message -->
      </div>
      <div class="mb-3 mt-3">
        <label for="enrollment">Enrollment Number:</label>
        <input type="text" class="form-control" id="enrollment" placeholder="Enter Enrollment" name="enrollment" value="<?php echo htmlspecialchars($enrollmentValue); ?>" required>
      </div>
      <div class="mb-3 mt-3">
        <label for="role" class="form-label">Role:</label>
        <input type="text" id="role" class="form-control" value="Student" readonly>
      </div>
      <div class="mb-3 mt-3">
        <label for="degree-program">Degree Program:</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="degree-program" id="degree-program-cs" value="CS" <?php if ($degreeProgramValue === 'CS') echo 'checked'; ?> required>
            <label class="form-check-label" for="degree-program-cs">CS</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="degree-program" id="degree-program-se" value="SE" <?php if ($degreeProgramValue === 'SE') echo 'checked'; ?> required>
            <label class="form-check-label" for="degree-program-se">SE</label>
          </div>
          <div class="mb-3 mt-3">
            <label for="batch" class="form-label">Batch</label>
            <select id="batch" class="form-select" name="batch" required>
              <option value="" disabled selected>Select batch</option>
              <option <?php if ($batchValue === '2021') echo 'selected'; ?>>2021</option>
              <option <?php if ($batchValue === '2022') echo 'selected'; ?>>2022</option>
              <option <?php if ($batchValue === '2023') echo 'selected'; ?>>2023</option>
              <option <?php if ($batchValue === '2024') echo 'selected'; ?>>2024</option>
              <option <?php if ($batchValue === '2025') echo 'selected'; ?>>2025</option>
              <option <?php if ($batchValue === '2026') echo 'selected'; ?>>2026</option>
            </select>
          </div>
    </div>

    <div class="d-grid gap-2 d-md-block">
      <a href="showStudent.php" class="btn btn-light">Cancle</a>
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>

    
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

<script>
  function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var content = document.getElementById('content');
    sidebar.classList.toggle('show');
    if (sidebar.classList.contains('show')) {
      content.style.marginLeft = '250px';
    } else {
      content.style.marginLeft = '0';
    }
  }
</script>
<script>
    $(document).ready(function() {
      $('#validationForm').submit(function(event) {
        event.preventDefault();
        var inputNumber = $('#enrollment').val();
        // Check if the last four characters are integers
        var lastFourDigits = inputNumber.slice(-4);
        if (!/^\d+$/.test(lastFourDigits)) {
          // Last four digits are not integers
          alert("Last four digits should be integers!");
          return;
        }
        // Proceed with form submission if validation passes
        // You can add your further processing here
        alert("Validation passed! Last four digits are integers: " + lastFourDigits);
        // Uncomment the line below to submit the form
        // $('#validationForm').submit();
      });
    });
</script>

</body>
</html>
