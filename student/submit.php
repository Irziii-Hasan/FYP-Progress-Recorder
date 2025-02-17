<?php
session_start(); // Start PHP session

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

// Check if user is a student, otherwise redirect to unauthorized access page
if ($_SESSION['role'] !== 'student') {
    header("Location: unauthorized.php");
    exit;
}

$student_id = $_SESSION['student_id']; // Get the logged-in student's ID
$assignment_id = $_GET['assignment_id'] ?? null;

if (!$assignment_id) {
    die("Assignment ID is required.");
}

include 'config.php';

// Get the project ID where the student is listed
$sql_project = "SELECT id AS project_id FROM projects 
                WHERE student1 = '$student_id' 
                OR student2 = '$student_id' 
                OR student3 = '$student_id' 
                OR student4 = '$student_id'";
$result_project = mysqli_query($conn, $sql_project);

if (mysqli_num_rows($result_project) > 0) {
    $project = mysqli_fetch_assoc($result_project);
    $project_id = $project['project_id'];
} else {
    die("Project not found for the student.");
}

// Check if assignment already submitted
$check_sql = "SELECT * FROM submission WHERE assignment_id = '$assignment_id' AND project_id = '$project_id'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    $submission_exists = true;
    $submission = mysqli_fetch_assoc($check_result);
} else {
    $submission_exists = false;
    // Fetch assignment details
    $sql = "SELECT * FROM assignments WHERE id = $assignment_id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $assignment = mysqli_fetch_assoc($result);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $target_dir = "../coordinator/"; // Adjusted path for student folder
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Handle file upload
    if (isset($_FILES['file'])) {
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $file_error = $_FILES["file"]["error"];

        if ($file_error === 0) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                // Determine submission status
                $submission_time = date("Y-m-d H:i:s"); // Current time
                $status = '';

                // Fetch deadline from the assignments table
                $assignment_sql = "SELECT deadline FROM assignments WHERE id = '$assignment_id'";
                $assignment_result = mysqli_query($conn, $assignment_sql);
                $assignment_data = mysqli_fetch_assoc($assignment_result);
                $deadline = $assignment_data['deadline'];

                if ($submission_time > $deadline) {
                    $status = 'Late';
                } elseif ($submission_time == $deadline) {
                    $status = 'On Time';
                } else {
                    $status = 'Early';
                }

                // Insert submission details into the database
                $sql_insert = "INSERT INTO submission (assignment_id, submission_path, submission_time, status, project_id) 
                               VALUES ('$assignment_id', '$target_file', '$submission_time', '$status', '$project_id')";

                if (mysqli_query($conn, $sql_insert)) {
                    echo "<script>
                            alert('The file has been uploaded and submission recorded.');
                            window.location.href = 'assignments.php';
                          </script>";
                } else {
                    echo "<script>
                            alert('Error recording submission: " . mysqli_real_escape_string($conn, mysqli_error($conn)) . "');
                            window.location.href = 'assignments.php';
                          </script>";
                }
            } else {
                echo "<script>
                        alert('Sorry, there was an error uploading your file.');
                        window.location.href = 'assignments.php';
                      </script>";
            }
        } else {
            echo "<script>
                    alert('Error in file upload: $file_error');
                    window.location.href = 'assignments.php';
                  </script>";
        }
    }
}
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            margin-top: 20px;
        }
        .card-header {
            background-color: white;
        }
        .card-body {
            background-color: white;
        }

        .heading {
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
                <!-- BREADCRUMBS -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                    <li class="breadcrumb-item"><a href="Assignments.php">View Assignments</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Submit Assignment</li>
                    </ol>
                </nav>
                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <div class="card shadow">
                                <div class="card-header text-center">
                                    <h2 class="heading">Submit Assignment</h2>
                                </div>
                                <div class="card-body">
                                    <?php
                                    if ($assignment_id && isset($assignment)) {
                                        if ($submission_exists) {
                                            echo "<div class='alert alert-success'>You have already submitted this assignment.</div>";
                                            echo "<a href='" . $submission['submission_path'] . "' class='btn btn-success' target='_blank'>View Submitted File</a>";
                                        } else {
                                            $assignment_name = $assignment['assignment_name'];
                                            $description = $assignment['description'];
                                            $deadline = $assignment['deadline'];
                                            $document_path = $assignment['document_path'];

                                            echo "<h4>Title: $assignment_name</h4>";
                                            echo "<p class='text-muted'>Description: $description</p>";
                                            echo "<p><strong>Deadline:</strong> " . date("F j, Y, g:i a", strtotime($deadline)) . "</p>";
                                            echo "<p><strong>Document:</strong> <a href='../coordinator/$document_path' target='_blank' class='btn btn-link'>View Document</a></p>";

                                            // Add submission form
                                            echo "<form action='' method='post' enctype='multipart/form-data'>";
                                            echo "<div class='mb-3'>";
                                            echo "<label for='file' class='form-label'>Choose file</label>";
                                            echo "<input type='file' name='file' class='form-control' required>";
                                            echo "</div>";
                                            echo "<input type='hidden' name='assignment_id' value='$assignment_id'>";
                                            echo "<button type='submit' name='submit' class='btn btn-primary'>Upload Submission</button>";
                                            echo "</form>";
                                        }
                                    } else {
                                        echo "<div class='alert alert-danger'>No assignment ID provided or no assignment found.</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
