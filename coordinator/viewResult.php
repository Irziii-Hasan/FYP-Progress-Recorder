<?php
include 'session_coordinator.php'; 
include 'config.php';

// Fetch available batches from the database
$sql = "SELECT BatchID, BatchName FROM batches"; // Adjust table name and columns if necessary
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Results</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
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
                        <li class="breadcrumb-item active" aria-current="page">View Results</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="m-3 d-flex justify-content-between align-items-center button">
                        <h2 class="heading mb-4">Select Batch</h2>
                        <a href="view_student_results.php" class="btn btn-success">View Saved Results</a>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <!-- Table Container -->
                            <div class="table-container">
                            <table class="table table-bordered table-striped">  
                                    <thead>
                                        <tr>
                                            <th>Batches</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo '<tr>';
                                                echo '<td>' . htmlspecialchars($row['BatchName']) . '</td>';
                                                echo '<td>';
                                                echo '<a href="result_table.php?batch=' . urlencode($row['BatchID']) . '" class="btn btn-primary btn-sm">';
                                                echo 'View Results';
                                                echo '</a>';
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                        } else {
                                            echo '<tr>';
                                            echo '<td colspan="2" class="text-center">No batches available.</td>';
                                            echo '</tr>';
                                        }
                                        $conn->close();
                                        ?>
                                    </tbody>
                                </table>
                            </div> <!-- End of Table Container -->
                        </div>
                    </div>
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
