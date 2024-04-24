<?php
$emailError = ""; // Define $emailError variable to avoid undefined variable warning
$emailValue = ""; // Initialize $emailValue variable to store the value of email field
$nameValue = ""; // Initialize $nameValue variable to store the value of name field
$designationValue = ""; // Initialize $designationValue variable to store the value of designation field

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

    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $designation = $_POST['designation'];
    $role = $_POST['role'];

    // Validate email
    $email = trim($_POST["email"]);
    if (empty($email)) {
        $emailError = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format";
    } else {
        // Check if email already exists in the database
        $query = "SELECT * FROM faculty WHERE email = '$email'";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            $emailError = "Email already exists";
            $emailValue = $email; // Preserve the value of email field
            $nameValue = $_POST['name']; // Preserve the value of name field
            $designationValue = $_POST['designation']; // Preserve the value of designation field
        } else {
            // Generate password (username + last 4 digits of designation)
            $username = strtolower(str_replace(' ', '', $name));
            $password = $username . substr($designation, -4);

            // SQL to insert data into faculty table
            $sql_faculty = "INSERT INTO faculty (username, email, password, designation, role) 
                            VALUES ('$name', '$email', '$password', '$designation', '$role')"; 

            if ($conn->query($sql_faculty) === TRUE) {
                echo '<script>alert("New record created successfully");</script>';
            } else {
                echo '<script>alert("Error: ' . $sql_faculty . '<br>' . $conn->error . '");</script>';
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
  <title>FYP| Add Faculty</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .form-all {
      width: 650px;
      padding: 20px 30px;
      border: 1px solid #cbcbcb;
      border-radius: 20px;
      background-color: white;
    }

    .form-heading {
      color: #0a4a91;
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
            <li class="breadcrumb-item"><a href="showuser.php">Faculty</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Faculty</li>
          </ol>
        </nav>
        <div class="container mt-3 form-all">
          <h2 class="text-center form-heading">Add Faculty</h2>
          <form action="faculty.php" method="post">
            <div class="mb-3 mt-3">
              <label for="name">Faculty Name:</label>
              <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name" value="<?php echo htmlspecialchars($nameValue); ?>" required>
            </div>
            <div class="mb-3 mt-3">
              <label for="email">Email:</label>
              <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email" value="<?php echo htmlspecialchars($emailValue); ?>" required>
              <span class="error" style="color: red;"><?php echo $emailError; ?></span> <!-- Display email error message -->
            </div>
            <div class="mb-3 mt-3">
              <label for="designation">Designation:</label>
              <select class="form-select" id="designation" name="designation" required>
                <option selected>Select Designation</option>
                <option value="Professor">Professor</option>
                <option value="Lecturer">Lecturer</option>
                <option value="Associate Professor">Associate Professor</option>
                <option value="Assistant Professor">Assistant Professor</option>
                <option value="Assistant Lecturer">Assistant Lecturer</option>
                <option value="Lecturer">Senior Lecturer</option>
                <!-- Add more options as needed -->
              </select>
            </div>
            <div class="mb-3 mt-3">
              <label for="role">Role:</label>
              <select class="form-select" id="role" name="role" required>
                <option selected>Select Role</option>
                <option value="Admin">Admin</option>
                <option value="Supervisor">Supervisor</option>
                <option value="Coordinator">Coordinator</option>
                <option value="Evaluator">Evaluator</option>

                <!-- Add more options as needed -->
              </select>
            </div>
            <div class="d-grid gap-2 d-md-block">
              <a href="showuser.php" class="btn btn-light">Cancel</a>
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
      </div>
    </div>
  </div>
</div>

</body>
</html>
