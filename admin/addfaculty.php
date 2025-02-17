<?php
include 'session_admin.php'; // Include session handling
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
$nameError = "";
$nameValue = "";
$designationValue = "";
$juwIdValue = "";
$juwIdError = "";
$professional_emailError = "";
$professional_emailValue = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape user inputs for security
    $juw_id = mysqli_real_escape_string($conn, $_POST['juw_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $professional_email = mysqli_real_escape_string($conn, $_POST['professional_email']);
    $designation = mysqli_real_escape_string($conn, $_POST['designation']);
    
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

    // Proceed only if there are no errors
    if (empty($emailError) && empty($nameError) && empty($professional_emailError) && empty($juwIdError)) {
        // Prepare Statements
        $stmt1 = $conn->prepare("SELECT * FROM faculty WHERE email = ?");
        $stmt2 = $conn->prepare("INSERT INTO faculty (juw_id, username, email, professional_email, password, designation) VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt1 === false || $stmt2 === false) {
            die('MySQL prepare() failed: ' . htmlspecialchars($conn->error));
        }

        $stmt1->bind_param("s", $email);
        $stmt1->execute();
        $result_email = $stmt1->get_result();

        if ($result_email->num_rows > 0) {
            $emailError = "Personal Email already exists";
            $emailValue = $email;
            $nameValue = $name;
            $designationValue = $designation;
            $juwIdValue = $juw_id;
            $professional_emailValue = $professional_email;
        } else {
            $stmt1->close();
            $stmt1 = $conn->prepare("SELECT * FROM faculty WHERE juw_id = ?");
            $stmt1->bind_param("s", $juw_id);
            $stmt1->execute();
            $result_juw_id = $stmt1->get_result();

            if ($result_juw_id->num_rows > 0) {
                $juwIdError = "User ID already exists";
                $emailValue = $email;
                $nameValue = $name;
                $designationValue = $designation;
                $juwIdValue = $juw_id;
                $professional_emailValue = $professional_email;
            } else {
                $random_password = generateRandomPassword();
                $stmt2->bind_param("ssssss", $juw_id, $name, $email, $professional_email, $random_password, $designation);
                if ($stmt2->execute()) {
                    $sql_user = "INSERT INTO user (username, email, password, role) VALUES (?, ?, ?, ?)";
                    $stmt3 = $conn->prepare($sql_user);
                    $role = "faculty";
                    $stmt3->bind_param("ssss", $name, $email, $random_password, $role);
                    if ($stmt3->execute()) {
                        echo '<script>alert("New faculty record created successfully.");</script>';
                    } else {
                        echo '<script>alert("Error: ' . htmlspecialchars($stmt3->error) . '");</script>';
                    }
                    $stmt3->close();
                } else {
                    echo '<script>alert("Error: ' . htmlspecialchars($stmt2->error) . '");</script>';
                }
            }
        }

        $stmt1->close();
        $stmt2->close();
        $conn->close();
    }
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
                    <form action="addfaculty.php" method="post" onsubmit="return validateForm()">
                        <div class="mb-3 mt-3">
                            <label for="juw_id">User ID:</label>
                            <input type="text" class="form-control" id="juw_id" name="juw_id" required pattern="[a-zA-Z0-9]+" title="User ID must contain only alphanumeric characters." placeholder="Enter User ID" value="<?php echo htmlspecialchars($juwIdValue); ?>">
                            <span class="error" style="color: red;"><?php echo $juwIdError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="name">Faculty Name:</label>
                            <input type="text" class="form-control" id="name" name="name" required pattern="[/^[a-zA-Z.\s]+$/]+" title="Faculty name must contain only alphabets,spaces and dots." placeholder="Enter Faculty Name" value="<?php echo htmlspecialchars($nameValue); ?>">
                            <span class="error" style="color: red;"><?php echo $nameError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="email">Personal Email:</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter Personal Email"  required value="<?php echo htmlspecialchars($emailValue); ?>">
                            <span class="error" style="color: red;"><?php echo $emailError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="professional_email">Professional Email:</label>
                            <input type="email" class="form-control" id="professional_email" name="professional_email" required pattern="[a-zA-Z0-9._%+-]+@juw\.edu\.pk" title="Professional email must end with @juw.edu.pk." placeholder="Enter Professional Email"  value="<?php echo htmlspecialchars($professional_emailValue); ?>">
                            <span class="error" style="color: red;"><?php echo $professional_emailError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="designation">Designation:</label>
                            <select class="form-select" id="designation" name="designation" required>
                                <option selected>Select Designation</option>
                                <option value="Lecturer" <?php echo ($designationValue == 'Lecturer') ? 'selected' : ''; ?>>Lecturer</option>
                                <option value="Assistant Professor" <?php echo ($designationValue == 'Assistant Professor') ? 'selected' : ''; ?>>Assistant Professor</option>
                                <option value="Associate Professor" <?php echo ($designationValue == 'Associate Professor') ? 'selected' : ''; ?>>Associate Professor</option>
                                <option value="Professor" <?php echo ($designationValue == 'Professor') ? 'selected' : ''; ?>>Professor</option>
                            </select>
                        </div>
                        <a href="faculty.php" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary" name="submit" value="Submit">Submit</button>
                    </form>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
                <script>
                    function validateForm() {
                        var juwId = document.getElementById('juw_id');
                        if (!juwId.value.match(/^[a-zA-Z0-9.]+$/)) {
                            juwId.setCustomValidity("User ID must contain only alphanumeric characters.");
                            return false;
                        } else {
                            juwId.setCustomValidity("");
                        }
                        var name = document.getElementById('name');
                        if (!name.value.match(/^[a-zA-Z.\s]+$/)) {
                            name.setCustomValidity("Faculty name must contain only alphabets, spaces, and dots.");
                            return false;
                        } else {
                            name.setCustomValidity("");
                        }
                        var profEmail = document.getElementById('professional_email');
                        if (!profEmail.value.match(/^[a-zA-Z0-9._%+-]+@juw\.edu\.pk$/)) {
                            profEmail.setCustomValidity("Professional email must end with @juw.edu.pk.");
                            return false;
                        } else {
                            profEmail.setCustomValidity("");
                        }
                        return true;
                    }
                </script>
            </div>
        </div>
    </div>
</div>

</body>
</html>
