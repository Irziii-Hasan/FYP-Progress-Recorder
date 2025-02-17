<?php include 'session_coordinator.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Durations</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        .heading {
            color: #0a4a91;
            font-weight: 700;
        }
        .table-container {
            padding: 20px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
            margin-top: 20px;
        }
        .btn-view, .btn-add {
            margin-right: 10px;
        }
        table th, table td {
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
            color: #0a4a91;
        }
        .table-bordered {
            border: 1px solid #ddd;
            border-radius: 10px;
            overflow: hidden;
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
                        <li class="breadcrumb-item active" aria-current="page">View Durations</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="heading">Course Durations</h1>
                        <a href="course_Duration.php" class="btn btn-primary">Add Duration</a>
                    </div>
                    
                    <!-- Table Container with rounded corners -->
                    <div class="table-container">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>Title</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    include 'config.php';

                                    // Check if the delete button is clicked
                                    if (isset($_GET['id']) && !empty($_GET['id'])) {
                                        // Sanitize the input to prevent SQL injection
                                        $duration_id = mysqli_real_escape_string($conn, $_GET['id']);

                                        // SQL to delete the course duration from the course_durations table
                                        $sql_delete_duration = "DELETE FROM course_durations WHERE id = '$duration_id'";

                                        if ($conn->query($sql_delete_duration) === TRUE) {
                                            echo "<script>alert('Course duration deleted successfully.');</script>";
                                        } else {
                                            echo "Error deleting course duration: " . $conn->error;
                                        }
                                    }

                                    // SQL to select course duration records
                                    $sql = "SELECT id, title, start_date, end_date FROM course_durations";
                                    $result = $conn->query($sql);

                                    if ($result->num_rows > 0) {
                                        $serial_number = 1; // Initialize a counter for S No.
                                        // Output data of each row
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $serial_number . "</td>"; // Display S No.
                                            echo "<td>" . $row["title"] . "</td>";

                                            // Format the start_date and end_date
                                            $start_date = date("d-M-Y", strtotime($row["start_date"]));
                                            $end_date = date("d-M-Y", strtotime($row["end_date"]));

                                            echo "<td>" . $start_date . "</td>";
                                            echo "<td>" . $end_date . "</td>";

                                            echo "<td>";
                                            // Edit icon
                                            echo "<a href='editDuration.php?id=" . $row["id"] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a> ";
                                            // Delete icon with confirmation
                                            echo "<a href='?id=" . $row["id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this duration?\")'><i class='bi bi-trash'></i></a>";
                                            echo "</td>";
                                            echo "</tr>";
                                            $serial_number++; // Increment the counter
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>No course duration records found</td></tr>";
                                    }
                                    $conn->close();
                                ?>

                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

</body>
</html>
