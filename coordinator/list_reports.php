<?php
include 'session_coordinator.php';  // Include session management
include 'config.php';  // Include your database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Reports</title>
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
                    <li class="breadcrumb-item"><a href="view_schedule.php">View Schedule</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Saved Reports</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="heading">Saved Reports</h2>
                    </div>
                    <?php
                    // Fetch all reports from the database
                    $sql = "SELECT id, report_path, recipient, heading, batch, event_id FROM reports ORDER BY id DESC";
                    $result = $conn->query($sql);
                    ?>

                    <?php if ($result->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Heading</th>
                                    <th>Batch</th>
                                    <th>Recipient</th>
                                    <th>Event ID</th>
                                    <th>Report</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Initialize a counter for the serial number
                                $serial_number = 1;
                                
                                // Loop through each row and display it
                                while ($row = $result->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td><?php echo $serial_number++; ?></td> <!-- Increment serial number -->
                                        <td><?php echo htmlspecialchars($row['heading']); ?></td>
                                        <td><?php echo htmlspecialchars($row['batch']); ?></td>
                                        <td><?php echo htmlspecialchars($row['recipient']); ?></td>
                                        <td><?php echo htmlspecialchars($row['event_id']); ?></td>
                                        <td>
                                            <a href="<?php echo htmlspecialchars($row['report_path']); ?>" target="_blank" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i> View PDF
                                            </a>
                                        </td>
                                        <td>
                                            <a href="delete_report.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this report?');">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">No reports found.</div>
                    <?php endif; ?>

                    <?php $conn->close(); ?>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
