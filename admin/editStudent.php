<?php
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

// Check if student ID is provided in the URL
if(isset($_GET['id']) && !empty($_GET['id'])) {
    // Sanitize the input to prevent SQL injection
    $student_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect form data and sanitize
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $enrollment = mysqli_real_escape_string($conn, $_POST['enrollment']);
        $degreeProgram = mysqli_real_escape_string($conn, $_POST['degreeProgram']);
        $batch = mysqli_real_escape_string($conn, $_POST['batch']);
        
        // SQL to update student record
        $sql_update_student = "UPDATE student SET username='$name', email='$email', enrollment='$enrollment', degree_program='$degreeProgram', year='$batch' WHERE id='$student_id'";

        // SQL to update user record
        $sql_update_user = "UPDATE user SET username='$name', email='$email' WHERE id='$student_id'";

        // Execute both update queries
        if ($conn->query($sql_update_student) === TRUE && $conn->query($sql_update_user) === TRUE) {
            // Redirect back to the user list page
            header("Location: showuser.php");
            exit();
        } else {
            echo "Error updating student record: " . $conn->error;
        }
    }

    // SQL to fetch student record
    $sql_fetch_student = "SELECT * FROM student WHERE id = '$student_id'";
    $result = $conn->query($sql_fetch_student);

    if ($result->num_rows > 0) {
        // Fetch student record
        $row = $result->fetch_assoc();

        // Assign fetched data to variables
        $nameValue = $row["username"];
        $emailValue = $row["email"];
        $enrollmentValue = $row["enrollment"];
        $degreeProgramValue = $row["degree_program"];
        $batchValue = $row["year"];
    } else {
        echo "Student not found";
    }
} else {
    echo "Student ID not provided";
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Student</h1>
        <form action="" method="post">
            <!-- Include form fields with values fetched from the database -->
            <!-- You can use PHP to populate the value attribute of each input field -->
            <!-- For example: value="<?php echo $nameValue; ?>" -->
            <div class="mb-3">
                <label for="name" class="form-label">Student Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $nameValue; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $emailValue; ?>">
            </div>
            <div class="mb-3">
                <label for="enrollment" class="form-label">Enrollment:</label>
                <input type="text" class="form-control" id="enrollment" name="enrollment" value="<?php echo $enrollmentValue; ?>">
            </div>
            <div class="mb-3">
                <label for="degreeProgram" class="form-label">Degree Program:</label>
                <input type="text" class="form-control" id="degreeProgram" name="degreeProgram" value="<?php echo $degreeProgramValue; ?>">
            </div>
            <div class="mb-3">
                <label for="batch" class="form-label">Batch:</label>
                <input type="text" class="form-control" id="batch" name="batch" value="<?php echo $batchValue; ?>">
            </div>
            <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="showuser.php" class="btn btn-light">Cancel</a>
        </form>
    </div>
</body>
</html>
