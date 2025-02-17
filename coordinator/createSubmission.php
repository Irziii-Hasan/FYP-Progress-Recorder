<?php
include 'session_coordinator.php';
include 'config.php';

// Initialize error and value variables
$assignmentNameError = $descriptionError = $deadlineError = $fileError = "";
$assignmentNameValue = $descriptionValue = $deadlineValue = "";

// Initialize file upload variables
$fileDestination = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input fields
    $assignment_name = htmlspecialchars($_POST['assignment_name']);
    $description = htmlspecialchars($_POST['description']);
    $deadline = htmlspecialchars($_POST['deadline']);

    // File upload logic
    $file_name = $_FILES['document']['name'] ?? null;
    $file_temp = $_FILES['document']['tmp_name'] ?? null;
    $file_error = $_FILES['document']['error'] ?? null;
    $file_destination = null;

    if ($file_name && $file_error === 0) {
        $file_destination = 'uploads/' . basename($file_name);
        if (move_uploaded_file($file_temp, $file_destination)) {
            // File uploaded successfully
        } else {
            $fileError = "Error uploading file.";
        }
    } elseif ($file_name && $file_error !== 0) {
        $fileError = "Error in file upload: " . $file_error;
    }

    // Validate input fields
    if (empty($assignment_name)) {
        $assignmentNameError = "Assignment name is required";
    } else {
        $assignmentNameValue = $assignment_name;
    }

    if (empty($deadline)) {
        $deadlineError = "Deadline is required";
    } else {
        $deadlineValue = $deadline;
        // Check if the deadline is in the past
        if (strtotime($deadline) <= time()) {
            $deadlineError = "The deadline cannot be in the past. Please choose a future date and time.";
        }}

    // Check for duplicate assignment name
    $check_query = "SELECT * FROM assignments WHERE assignment_name = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("s", $assignment_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $assignmentNameError = "An assignment with this name already exists. Please choose a different name.";
    } else {
        // Insert data into database if no errors
        if (empty($assignmentNameError) && empty($descriptionError) && empty($deadlineError) && empty($fileError)) {
            $sql = "INSERT INTO assignments (assignment_name, description, deadline, document_path) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $assignment_name, $description, $deadline, $file_destination);

            if ($stmt->execute()) {
                $_SESSION['alert_type'] = "success";
            } else {
                $_SESSION['alert_message'] = "Error: " . $stmt->error;
                $_SESSION['alert_type'] = "danger";
            }

            // Redirect to clear POST data and display the message
            header("Location: viewportal.php");
            exit();
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">    
    <style>
        .container {
            max-width: 960px;
        }
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
<?php include 'sidebar.php'; ?>

<div class="wrapper">
    <div class="container-fluid" id="content">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                        <li class="breadcrumb-item"><a href="viewportal.php">Assignments</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create Assignment</li>
                    </ol>
                </nav>

                <!-- Alert Message -->
                <?php if (isset($_SESSION['alert_message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['alert_type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['alert_message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php
                    // Clear the session variables after displaying the message
                    unset($_SESSION['alert_message']);
                    unset($_SESSION['alert_type']);
                    ?>
                <?php endif; ?>

                <div class="container mt-3 form-all" style="width: 650px;">
                    <h2 class="text-center form-heading">Create Assignment</h2>
                    <form action="createsubmission.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3 mt-3">
                            <label for="assignment_name" class="form-label">Assignment Name</label>
                            <input type="text" class="form-control" id="assignment_name" placeholder="Enter assigment name"name="assignment_name" value="<?php echo htmlspecialchars($assignmentNameValue); ?>" required>
                            <span class="error"><?php echo $assignmentNameError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control"  placeholder="Enter description" id="description" name="description" rows="3"><?php echo htmlspecialchars($descriptionValue); ?></textarea>
                            <span class="error"><?php echo $descriptionError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="deadline" class="form-label">Deadline</label>
                            <input type="datetime-local" class="form-control" id="deadline" name="deadline" value="<?php echo htmlspecialchars($deadlineValue); ?>">
                            <span class="error"><?php echo $deadlineError; ?></span>
                        </div>
                        <div class="mb-3 mt-3">
                            <label for="document" class="form-label">Upload Document</label>
                            <input type="file" class="form-control" id="document" name="document">
                            <span class="error"><?php echo $fileError; ?></span>
                        </div>
                        <div class="d-grid gap-2 d-md-block">
                            <a href="dashboard.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Assignment</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
