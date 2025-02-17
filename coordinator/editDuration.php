<?php include 'session_coordinator.php'; ?>
<?php include 'config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course Duration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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
                        <li class="breadcrumb-item"><a href="viewDuration.php">Course Durations</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Duration</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <h1 class="mb-4">Edit Course Duration</h1>

                    <?php
                    // Check if the ID is set in the URL
                    if (isset($_GET['id']) && !empty($_GET['id'])) {
                        // Sanitize the input
                        $duration_id = mysqli_real_escape_string($conn, $_GET['id']);

                        // SQL to fetch the course duration details
                        $sql = "SELECT * FROM course_durations WHERE id = '$duration_id'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 1) {
                            $row = $result->fetch_assoc();
                        } else {
                            echo "<div class='alert alert-danger'>Invalid Duration ID.</div>";
                            exit;
                        }
                    } else {
                        echo "<div class='alert alert-danger'>No Duration ID specified.</div>";
                        exit;
                    }

                    // Update the course duration details when the form is submitted
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $title = mysqli_real_escape_string($conn, $_POST['title']);
                        $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
                        $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);

                        // Update query
                        $sql_update = "UPDATE course_durations SET 
                                        title = '$title', 
                                        start_date = '$start_date', 
                                        end_date = '$end_date' 
                                       WHERE id = '$duration_id'";

                        if ($conn->query($sql_update) === TRUE) {
                            echo "<div class='alert alert-success'>Course duration updated successfully.</div>";
                            echo "<script>setTimeout(function() { window.location.href = 'viewDuration.php'; }, 2000);</script>";
                        } else {
                            echo "<div class='alert alert-danger'>Error updating duration: " . $conn->error . "</div>";
                        }
                    }
                    ?>

                    <!-- Edit Duration Form -->
                    <form method="POST" action="" class="bg-light p-4 rounded shadow-sm">
                        <div class="row mb-3">
                            <label for="title" class="col-sm-2 col-form-label">Title</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="start_date" class="col-sm-2 col-form-label">Start Date</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($row['start_date']); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="end_date" class="col-sm-2 col-form-label">End Date</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($row['end_date']); ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary">Update Duration</button>
                                <a href="viewDuration.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

</body>
</html>
