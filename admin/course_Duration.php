<?php
include 'session_admin.php'; // Include session handling
include 'config.php'; // Include database configuration

// Initialize variables
$titleError = "";
$startDateError = "";
$endDateError = "";
$titleValue = "";
$startDateValue = "";
$endDateValue = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $start_date_raw = trim($_POST['start_date']);
    $end_date_raw = trim($_POST['end_date']);

    // Convert input dates to DateTime format for validation and display
    $start_date = DateTime::createFromFormat('Y-m-d', $start_date_raw);
    $end_date = DateTime::createFromFormat('Y-m-d', $end_date_raw);

    // Validate inputs
    if (empty($title)) {
        $titleError = "Course title is required";
    }
    if (empty($start_date)) {
        $startDateError = "Start date is required";
    }
    if (empty($end_date)) {
        $endDateError = "End date is required";
    } elseif ($start_date > $end_date) {
        $endDateError = "End date cannot be earlier than start date";
    }

    // Check if there are no errors before inserting into database
    if (empty($titleError) && empty($startDateError) && empty($endDateError)) {
        // Format dates back to Y-m-d for the database
        $formatted_start_date = $start_date->format('Y-m-d');
        $formatted_end_date = $end_date->format('Y-m-d');

        // Insert new course duration
        $sql = "INSERT INTO course_durations (title, start_date, end_date) VALUES ('$title', '$formatted_start_date', '$formatted_end_date')";
        if ($conn->query($sql) === TRUE) {
            echo '<script>alert("New course duration created successfully");</script>';
            // Reset values
            $titleValue = "";
            $startDateValue = "";
            $endDateValue = "";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    } else {
        // Preserve the values if there's an error
        $titleValue = $title;
        // Reformat dates to DD-MM-YYYY for display
        $startDateValue = $start_date->format('d-m-Y');
        $endDateValue = $end_date->format('d-m-Y');
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Add Course Duration</title>
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
                        <li class="breadcrumb-item"><a href="viewDuration.php">View Durations</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Course Duration</li>
                    </ol>
                </nav>
                <div class="container mt-3 form-all" style="width: 650px;">
                    <h2 class="text-center form-heading">Add Course Duration</h2>
                    <form action="course_Duration.php" method="post">
                        <div class="row mb-3 align-items-center">
                            <div class="col-auto">
                                <label for="title" class="col-form-label">Course Title</label>
                            </div>
                            <div class="col">
                                <input type="text" class="form-control" id="title" placeholder="e.g. FYP-I" name="title" value="<?php echo htmlspecialchars($titleValue); ?>" required>
                                <div class="text-danger"><?php echo $titleError; ?></div>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-auto">
                                <label for="start_date" class="col-form-label">Start Date</label>
                            </div>
                            <div class="col">
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDateValue); ?>" required>
                                <div class="text-danger"><?php echo $startDateError; ?></div>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-auto">
                                <label for="end_date" class="col-form-label">End Date</label>
                            </div>
                            <div class="col">
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDateValue); ?>" required>
                                <div class="text-danger"><?php echo $endDateError; ?></div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-block">
                            <a href="viewDuration.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FontAwesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

</body>
</html>
