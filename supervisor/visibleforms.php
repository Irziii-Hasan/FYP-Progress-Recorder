<?php
include 'session_supervisor.php'; // Include session handling
include 'config.php';

// Fetch all forms from the database
$query = "SELECT * FROM customized_form where visible ='yes' ORDER BY id DESC";
$result = $conn->query($query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Forms List</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
        .btn-assign {
            font-size: 14px;
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
                        <li class="breadcrumb-item active" aria-current="page">My Project Evaluation</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="heading">My Project Evaluation</h1>
                    </div>

                    <div class="table-container">
                        <table class="table table-striped table-hover mt-3 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">S.No</th>
                                    <th scope="col">Form Title</th>
                                    <th scope="col" class="text-center">Assign Marks</th> <!-- Updated column title -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    $serial_number = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $serial_number . "</td>";
                                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                                        echo "<td class='text-center'><a href='project_list.php?form_id=" . $row['id'] . "' class='btn btn-success btn-assign'><i class='fas fa-pencil-alt'></i> Assign Marks</a></td>"; // Updated button
                                        echo "</tr>";
                                        $serial_number++;
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center text-muted'>No forms found.</td></tr>"; // Updated colspan to 3
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>


</body>
</html>
