<?php
include 'session_coordinator.php'; // Include session handling
include 'config.php';

$titleError = ""; // Initialize title error variable
$titleValue = ""; // Initialize title value variable
$remainingMarks = 100; // Initialize remaining marks variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $passing_marks = $_POST['passing_marks'];
    $total_marks = $_POST['total_marks'];
    $created_at = date("Y-m-d H:i:s"); // Capture the current timestamp

    // Check if the form title already exists
    $checkTitleStmt = $conn->prepare("SELECT * FROM customized_form WHERE title = ?");
    $checkTitleStmt->bind_param('s', $title);
    $checkTitleStmt->execute();
    $result = $checkTitleStmt->get_result();

    if ($result->num_rows > 0) {
        // Title already exists
        $titleError = "Form title already exists. Please choose a different title.";
    } else {
        // Validate title
        if (empty($title)) {
            $titleError = "Form title is required";
        } else {
            try {
                // Determine the duration_id based on created_at timestamp
                $durationStmt = $conn->prepare("SELECT id FROM course_durations WHERE ? BETWEEN start_date AND end_date");
                $durationStmt->bind_param('s', $created_at);
                $durationStmt->execute();
                $durationResult = $durationStmt->get_result();
                
                $duration_id = null;
                if ($durationResult->num_rows > 0) {
                    $durationRow = $durationResult->fetch_assoc();
                    $duration_id = $durationRow['id']; // Get the matching duration ID

                    // Calculate the total of existing marks for this duration
                    $totalMarksStmt = $conn->prepare("SELECT SUM(total_marks) AS sum_total_marks FROM customized_form WHERE duration_id = ?");
                    $totalMarksStmt->bind_param('i', $duration_id);
                    $totalMarksStmt->execute();
                    $totalMarksResult = $totalMarksStmt->get_result();
                    $totalMarksRow = $totalMarksResult->fetch_assoc();

                    $currentTotalMarks = $totalMarksRow['sum_total_marks'] ?? 0; // Get current sum of marks, or 0 if none exist
                    $remainingMarks = 100 - $currentTotalMarks; // Calculate remaining marks

                    // Check if adding this form's marks would exceed the limit
                    if (($currentTotalMarks + $total_marks) > 100) {
                        $titleError = "The total marks for forms in this duration exceed 100. You have {$remainingMarks} marks left. Please adjust your total marks.";
                    } else {
                        // Prepare and execute the statement to insert into customized_form
                        $stmt = $conn->prepare("INSERT INTO customized_form (title, passing_marks, total_marks, created_at, duration_id) VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param('ssssi', $title, $passing_marks, $total_marks, $created_at, $duration_id);
                        $stmt->execute();

                        // Get the last inserted form ID
                        $form_id = $conn->insert_id;

                        // Prepare and execute the statement to insert into form_detail
                        $descriptions = $_POST['description'];
                        $max_marks = $_POST['max_marks'];

                        $stmt = $conn->prepare("INSERT INTO form_detail (form_id, description, max_marks) VALUES (?, ?, ?)");

                        foreach ($descriptions as $key => $description) {
                            $stmt->bind_param('isi', $form_id, $description, $max_marks[$key]);
                            $stmt->execute();
                        }

                        header("Location: forms.php");

                        echo '<script>alert("Form saved successfully!");</script>';
                    }
                } else {
                    $titleError = "No valid course duration found for the current date.";
                }

                $totalMarksStmt->close();
                $durationStmt->close();
            } catch (mysqli_sql_exception $e) {
                echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
            }
        }
    }

    $checkTitleStmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Create Customized Form</title>
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
        .form-all, .preview-all {
            padding: 20px 30px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
            margin-bottom: 20px;
        }

        .form-heading, .preview-heading {
            color: #0a4a91;
            font-weight: 700;
        }

        label {
            font-weight: 500;
        }

        .btn-add {
            width: 100%;
        }

        .separator-line {
            border-top: 2px solid #cbcbcb;
            margin: 20px 0;
        }

        .delete-icon {
            color: red;
            cursor: pointer;
            margin-top: 10px;
        }

        .preview-section {
            border: 1px solid #cbcbcb;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .preview-title {
            font-size: 1.9em;
            font-weight: bold;
            text-align: center;
        }

        .preview-details {
            margin-top: 10px;
        }

        .preview-table {
            width: 100%;
            border-collapse: collapse;
        }

        .preview-table th, .preview-table td {
            border: 1px solid #cbcbcb;
            padding: 8px;
            text-align: left;
        }

        .preview-table th {
            background-color: #f2f2f2;
        }

        .preview-marks {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
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
                        <li class="breadcrumb-item"><a href="forms.php">Forms</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create Customized Form</li>
                    </ol>
                </nav>
                <div class="container mt-3 form-all" style="width: 650px;">
                    <h2 class="text-center form-heading">Create Customized Form</h2>
                    <form method="post" action="customized_form.php" onsubmit="return validateMarks();">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="title" class="form-label">Form Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($titleValue); ?>" required>
                                <div class="text-danger"><?php echo $titleError; ?></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="passing_marks" class="form-label">Passing Marks</label>
                                <input type="number" class="form-control" id="passing_marks" name="passing_marks" required>
                            </div>
                            <div class="col">
                            <label for="total_marks" class="form-label">Total Marks</label>
                                <input type="number" class="form-control" id="total_marks" name="total_marks" max="<?php echo $remainingMarks; ?>" required>
                                <div class="error-message" id="totalMarksError"></div>

                            </div>
                        </div>

                        <div class="separator-line"></div>

                        <div id="form-details">
                            <h4>Form Details</h4>
                            <div class="row mb-3 detail-row">
                                <div class="col">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" id="description" name="description[]" required>
                                </div>
                                <div class="col">
                                    <label for="max_marks" class="form-label">Max Marks</label>
                                    <input type="number" class="form-control" id="max_marks" name="max_marks[]" required>
                                </div>
                                <div class="col-1 d-flex align-items-center">
                                    <i class="fas fa-trash-alt delete-icon"></i>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-primary mb-3 btn-add" id="add-detail">Add More Details</button>

                        <div class="d-grid gap-2 d-md-block">
                            <a href="forms.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Form</button>
                        </div>
                    </form>
                </div>

                <div class="container mt-3 preview-all" style="width: 650px;">
                    <h2 class="text-center preview-heading">Form Preview</h2>
                    <div class="preview-section">
                        <div class="preview-title" id="preview-title"></div>
                        <div class="preview-marks">
                            <span id="preview-passing-marks"></span>
                            <span id="preview-total-marks"></span>
                        </div>
                        <table class="preview-table" id="preview-details">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Max Marks</th>
                                </tr>
                            </thead>
                            <tbody>
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

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function(){
        function updatePreview() {
            let title = $('#title').val();
            let passingMarks = $('#passing_marks').val();
            let totalMarks = $('#total_marks').val();
            let detailsHtml = '';

            $('#preview-title').html(title);
            $('#preview-passing-marks').html(`<strong>Passing Marks:</strong> ${passingMarks}`);
            $('#preview-total-marks').html(`<strong>Total Marks:</strong> ${totalMarks}`);
            
            $('#preview-details tbody').empty(); // Clear previous preview details

            $('#form-details .detail-row').each(function(){
                let description = $(this).find('input[name="description[]"]').val();
                let maxMarks = $(this).find('input[name="max_marks[]"]').val();
                detailsHtml += `<tr><td>${description}</td><td>${maxMarks}</td></tr>`;
            });

            $('#preview-details tbody').append(detailsHtml);
        }

        $('#add-detail').on('click', function() {
            let newRow = `<div class="row mb-3 detail-row">
                            <div class="col">
                                <input type="text" class="form-control" name="description[]" required>
                            </div>
                            <div class="col">
                                <input type="number" class="form-control" name="max_marks[]" required>
                            </div>
                            <div class="col-1 d-flex align-items-center">
                                <i class="fas fa-trash-alt delete-icon"></i>
                            </div>
                        </div>`;
            $('#form-details').append(newRow);
        });

        $(document).on('click', '.delete-icon', function() {
            $(this).closest('.detail-row').remove();
            updatePreview();
        });

        // Update preview when any of the input fields change
        $('#title, #passing_marks, #total_marks').on('input', updatePreview);
        $('#form-details').on('input', '.detail-row input', updatePreview);

        // Initial preview update
        updatePreview();
    });
    
    function validateMarks() {
        let totalMarks = parseInt(document.getElementById('total_marks').value);
        let maxMarks = document.getElementsByName('max_marks[]');
        let sum = 0;

        for (let i = 0; i < maxMarks.length; i++) {
            sum += parseInt(maxMarks[i].value);
        }

        if (sum < totalMarks) {
            alert("The sum of max marks fields is less than the total marks. Please ensure the total marks are distributed correctly.");
            return false; // Prevent form submission
        } else if (sum > totalMarks) {
            alert("The sum of max marks fields is greater than the total marks. Please ensure the total marks are distributed correctly.");
            return false; // Prevent form submission
        }
        return true; // Allow form submission
    }
</script>
</body>
</html>


