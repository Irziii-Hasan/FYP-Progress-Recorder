<?php
include 'session_coordinator.php';
// Include session handling
include 'config.php';

// Generate random password
function generateRandomPassword($length = 12) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

// Initialize variables for error handling and form prefilling
$emailError = "";
$emailValue = "";
$nameValue = "";
$designationValue = "";
$juwIdValue = "";
$juwIdError = "";
$professional_emailError = "";
$professional_emailValue = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "FYP_Progress_Recorder";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Escape user inputs for security
    $juw_id = mysqli_real_escape_string($conn, $_POST['juw_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']); // Personal Email
    $professional_email = mysqli_real_escape_string($conn, $_POST['professional_email']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);

    // Generate a random password
    $random_password = generateRandomPassword();

    // Prepare Statements
    $stmt1 = $conn->prepare("SELECT * FROM faculty WHERE email = ?");
    $stmt2 = $conn->prepare("INSERT INTO faculty (juw_id, username, email, professional_email, password, designation) VALUES (?, ?, ?, ?, ?, ?)");

    // Check if prepare() succeeded
    if ($stmt1 === false || $stmt2 === false) {
        die('MySQL prepare() failed: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters for SELECT statement
    $stmt1->bind_param("s", $email);
    $stmt1->execute();
    $result_email = $stmt1->get_result();

    // Check if email already exists
    if ($result_email->num_rows > 0) {
        $emailError = "Personal Email already exists";
        $emailValue = $email;
        $nameValue = $name;
        $designationValue = $designation;
        $juwIdValue = $juw_id;
        $professional_emailValue = $professional_email;
    } else {
        // Close and reset $stmt1 for reuse
        $stmt1->close();

        // Prepare and bind parameters for another SELECT statement
        $stmt1 = $conn->prepare("SELECT * FROM faculty WHERE juw_id = ?");
        $stmt1->bind_param("s", $juw_id);
        $stmt1->execute();
        $result_juw_id = $stmt1->get_result();

        // Check if juw_id already exists
        if ($result_juw_id->num_rows > 0) {
            $juwIdError = "User ID already exists";
            $emailValue = $email;
            $nameValue = $name;
            $designationValue = $designation;
            $juwIdValue = $juw_id;
            $professional_emailValue = $professional_email;
        } else {
            // Bind parameters for INSERT statement
            $stmt2->bind_param("ssssss", $juw_id, $name, $email, $professional_email, $random_password, $designation);

            // Execute INSERT statement
            if ($stmt2->execute()) {
                // Insert into user table
                $sql_user = "INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)";
                $stmt3 = $conn->prepare($sql_user);
                $role = "faculty"; // Assuming role for faculty
                $stmt3->bind_param("ssss", $name, $email, $random_password, $role);

                if ($stmt3->execute()) {
                    echo '<script>alert("New faculty record created successfully. ");</script>';
                } else {
                    echo '<script>alert("Error: ' . htmlspecialchars($stmt3->error) . '");</script>';
                }

                $stmt3->close();
            } else {
                echo '<script>alert("Error: ' . htmlspecialchars($stmt2->error) . '");</script>';
            }
        }
    }

    // Close all statements and connection
    $stmt1->close();
    $stmt2->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Add Faculty</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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
                        <li class="breadcrumb-item active" aria-current="page">Add Faculty</li>
                    </ol>
                </nav>
                <div class="container mt-3 form-all" style="width: 650px;">
                    <h2 class="text-center form-heading">Add Faculty</h2>
                    <form action="addfaculty.php" method="post">
                        <div class="mb-3 mt-3">
                            <label for="juw_id">User ID:</label>
                            <input type="text" class="form-control" id="juw_id" placeholder="Enter User ID"
                                   name="juw_id" value="<?php echo htmlspecialchars($juwIdValue); ?>" required>
                            <span class="error" style="color: red;"><?php echo $juwIdError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="name">Faculty Name:</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter Name"
                                   name="name" value="<?php echo htmlspecialchars($nameValue); ?>" required>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="email">Personal Email:</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter Personal Email"
                                   name="email" value="<?php echo htmlspecialchars($emailValue); ?>" required>
                            <span class="error" style="color: red;"><?php echo $emailError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="professional_email">Professional Email:</label>
                            <input type="email" class="form-control" id="professional_email"
                                   placeholder="Enter Professional Email" name="professional_email" value="<?php echo htmlspecialchars($professional_emailValue); ?>">
                        </div>

                        <div class="mb-3 mt-3">
                            <label for="designation">Designation:</label>
                            <select class="form-select" id="designation" name="designation" required>
                                <option selected>Select Designation</option>
                                <option value="Chairperson">Chairperson</option>
                                <option value="Professor">Professor</option>
                                <option value="Assistant Professor">Assistant Professor</option>
                                <option value="Lecturer">Lecturer</option>
                                <option value="Lab Instructor">Lab Instructor</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2 d-md-block">
                            <a href="faculty.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
            </div>
        </div>
    </div>
</div>

</body>
</html>
