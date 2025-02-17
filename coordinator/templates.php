<?php

include 'session_coordinator.php';
include 'config.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Templates</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
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
        .btn {
            border-radius: 5px;
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
                        <li class="breadcrumb-item active" aria-current="page">Templates</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="heading">Templates</h2>
                        <a href="uploadTemplates.php" class="btn btn-primary">Upload New Template</a>
                    </div>

                    <!-- Table Container -->
                    <div class="table-container">
                    <table class="table table-bordered table-striped">    
                            <thead>
                                <tr>
                                    <th>S No.</th>
                                    <th>Document Name</th>
                                    <th>Send To</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php

                                // Check if the delete button is clicked
                                if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
                                    // Sanitize the input to prevent SQL injection
                                    $template_id = mysqli_real_escape_string($conn, $_GET['delete_id']);

                                    // SQL to delete the template from the templates table
                                    $sql_delete_template = "DELETE FROM templates WHERE template_id = '$template_id'";
                                    
                                    if ($conn->query($sql_delete_template) === TRUE) {
                                        echo "<script>alert('Template deleted successfully.');</script>";
                                    } else {
                                        echo "Error deleting template: " . $conn->error;
                                    }
                                }

                                // SQL to select template records
                                $sql = "SELECT template_id, document_name, file_path, send_to FROM templates";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    $serial_number = 1; // Initialize a counter for S No.
                                    // Output data of each row
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $serial_number . "</td>"; // Display S No.
                                        echo "<td>" . $row["document_name"] . "</td>";
                                        echo "<td>" . $row["send_to"] . "</td>"; // Display Send To
                                        echo "<td>";
                                        // View icon
                                        echo "<a href='" . $row["file_path"] . "' target='_blank' class='btn btn-primary btn-sm'><i class='bi bi-eye'></i></a> ";
                                        // Delete icon with confirmation
                                        echo "<a href='?delete_id=" . $row["template_id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this template?\")'><i class='bi bi-trash'></i></a>";
                                        echo "</td>";
                                        echo "</tr>";
                                        $serial_number++; // Increment the counter
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No template records found</td></tr>"; // Adjust colspan
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
