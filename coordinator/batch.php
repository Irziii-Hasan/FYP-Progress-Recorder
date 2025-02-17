<?php include 'session_coordinator.php'; ?>

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
                        <li class="breadcrumb-item"><a href="more.php">More</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Batches</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1>Batch List</h1>
                        <a href="addBatch.php" class="btn btn-primary">Add Batch</a>
                    </div>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>S No.</th>
                                <th>Batch Name</th>
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

                            // SQL to select batch records
                            $sql = "SELECT BatchID, BatchName FROM batches";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                $serial_number = 1; // Initialize a counter for S No.
                                // Output data of each row
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $serial_number . "</td>"; // Display S No.
                                    echo "<td>" . $row["BatchName"] . "</td>";
                                    echo "<td>";
                                    // Edit icon
                                    echo "<a href='editBatch.php?id=" . $row["BatchID"] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a> ";
                                    // Delete icon with confirmation
                                    echo "<a href='?id=" . $row["BatchID"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this batch?\")'><i class='bi bi-trash'></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                    $serial_number++; // Increment the counter
                                }
                            } else {
                                echo "<tr><td colspan='3'>No batch records found</td></tr>";
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

