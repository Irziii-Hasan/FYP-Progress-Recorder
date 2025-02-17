<?php
include 'session_coordinator.php'; // Include session handling
include 'config.php';

// Fetch all forms from the database
$query = "SELECT * FROM customized_form ORDER BY id DESC";
$result = $conn->query($query);


// Handle visibility form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['visibility'])) {
    foreach ($_POST['visibility'] as $form_id => $visibility) {
        $visible = ($visibility === 'on') ? 'yes' : 'no';
        $sql = "UPDATE customized_form SET visible = '$visible' WHERE id = '$form_id'";
        $conn->query($sql);
    }
    echo "<script>alert('Visibility settings updated successfully.'); window.location.href = 'forms.php';</script>";
}


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
        .btn-preview {
            font-size: 14px;
        }
        .btn-actions {
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
                        <li class="breadcrumb-item active" aria-current="page">Forms</li>
                    </ol>
                </nav>

                <div class="container mt-5">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="heading">Forms</h1>
            <a href="customized_form.php" class="btn btn-primary">Create Form</a>
            </div>

                    <div class="table-container">
                    <table class="table table-bordered table-striped">    
                            <thead>
                                <tr>
                                    <th scope="col">S.No</th>
                                    <th scope="col">Form Title</th>
                                    <th scope="col" class="text-center">Preview</th>
                                    <th scope="col" class="text-center">Actions</th> <!-- New column for actions -->
                                    <th scope="col" class="text-center">Form Visibility</th> <!-- New column for actions -->
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

                                        // Preview Button
                                        echo "<td class='text-center'><a href='form_preview.php?form_id=" . $row['id'] . "' class='btn btn-success btn-preview'><i class='fas fa-eye'></i> Preview</a></td>";

                                        // Edit and Delete Actions
                                        echo "<td class='text-center'>";
                                        echo "<a href='edit_form.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'><i class='bi bi-pencil'></i></a> ";
                                        echo "<button type='button' class='btn btn-danger btn-sm' onclick='confirmDelete(" . $row['id'] . ")'><i class='bi bi-trash'></i></button>";
                                        echo "</td>";

                                        // Visibility Toggle
                                        echo "<td class='text-center'>";
                                        echo "<div class='form-check form-switch d-inline-block'>";
                                        echo "<input type='checkbox' class='form-check-input visibility-toggle' data-form-id='" . $row['id'] . "' " . ($row['visible'] == 'yes' ? 'checked' : '') . ">";
                                        echo "</div>";
                                        echo "</td>";

                                        echo "</tr>";
                                        $serial_number++;
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center text-muted'>No forms found.</td></tr>";
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('.visibility-toggle').on('change', function () {
            var formId = $(this).data('form-id'); // Get the form ID
            var visibility = $(this).is(':checked') ? 'yes' : 'no'; // Get the checkbox state

            // AJAX request to update visibility
            $.ajax({
                url: 'update_visibility.php',
                type: 'POST',
                data: {
                    form_id: formId,
                    visible: visibility
                },
                success: function (response) {
                    console.log(response); // Log success response
                },
                error: function () {
                    alert('Error: Could not update visibility. Please try again.');
                }
            });
        });
    });
</script>


<script>
    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this form?")) {
            window.location.href = "delete_form.php?id=" + id;
        }
    }
</script>

</body>
</html>
