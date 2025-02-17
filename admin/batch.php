<?php include 'session_admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch List</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Batches</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="heading">Batch List</h1>
                        <a href="addBatch.php" class="btn btn-primary">Add Batch</a>
                    </div>
                    <div class="table-container">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>S No.</th>
                                <th>Batch Name</th>
                                <th>Degree Program</th>                                
                                <th>Course Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                           include 'config.php';

                            // Check if the delete button is clicked
                            if(isset($_GET['id']) && !empty($_GET['id'])) {
                                // Sanitize the input to prevent SQL injection
                                $batch_id = mysqli_real_escape_string($conn, $_GET['id']);

                                // SQL to delete the batch from the batches table
                                $sql_delete_batch = "DELETE FROM batches WHERE BatchID = '$batch_id'";
                                
                                if ($conn->query($sql_delete_batch) === TRUE) {
                                    echo "<script>alert('Batch deleted successfully.');</script>";
                                } else {
                                    echo "Error deleting batch: " . $conn->error;
                                }
                            }

                            // SQL to select batch records with course duration
                            $sql = "SELECT b.BatchID, b.BatchName,b.abbreviation, cd.title AS CourseDuration 
                                    FROM batches b
                                    LEFT JOIN course_durations cd ON b.course_duration_id = cd.id";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                $serial_number = 1; // Initialize a counter for S No.
                                // Output data of each row
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $serial_number . "</td>"; // Display S No.
                                    echo "<td>" . $row["BatchName"] . "</td>";
                                    echo "<td>" . $row["abbreviation"] . "</td>";
                                    echo "<td>" . ($row["CourseDuration"] ? $row["CourseDuration"] : '-') . "</td>"; // Display Course Duration or dash if NULL
                                    echo "<td>";
                                    // Edit icon
                                    echo "<a href='editBatch.php?id=" . $row["BatchID"] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a> ";
                                    // Delete icon with confirmation
                                    echo "</td>";
                                    echo "</tr>";
                                    $serial_number++; // Increment the counter
                                }
                            } else {
                                echo "<tr><td colspan='4'>No batch records found</td></tr>";
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
