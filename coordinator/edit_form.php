<?php
include 'session_coordinator.php'; // Include session handling
include 'config.php';

// Initialize error variables
$titleError = "";

// Check if 'id' is present in the URL
if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">Form ID parameter not found.</div>';
    print_r($_GET); // Output all GET parameters for debugging
    exit();
}

$form_id = intval($_GET['id']);

// Retrieve existing form data
$form_stmt = $conn->prepare("SELECT title, passing_marks, total_marks FROM customized_form WHERE id = ?");
$form_stmt->bind_param('i', $form_id);
$form_stmt->execute();
$form_result = $form_stmt->get_result();
$form_data = $form_result->fetch_assoc();

if (!$form_data) {
    echo '<div class="alert alert-danger">Form not found.</div>';
    exit();
}

$titleValue = htmlspecialchars($form_data['title']);
$passing_marks_value = htmlspecialchars($form_data['passing_marks']);
$total_marks_value = htmlspecialchars($form_data['total_marks']);

// Retrieve existing form details
$details_stmt = $conn->prepare("SELECT description, max_marks FROM form_detail WHERE form_id = ?");
$details_stmt->bind_param('i', $form_id);
$details_stmt->execute();
$details_result = $details_stmt->get_result();
$details = $details_result->fetch_all(MYSQLI_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $passing_marks = $_POST['passing_marks'];
    $total_marks = $_POST['total_marks'];

    // Validate title
    if (empty($title)) {
        $titleError = "Form title is required";
    } else {
        try {
            // Prepare and execute the statement to update customized_form
            $stmt = $conn->prepare("UPDATE customized_form SET title = ?, passing_marks = ?, total_marks = ? WHERE id = ?");
            $stmt->bind_param('ssii', $title, $passing_marks, $total_marks, $form_id);
            $stmt->execute();

            // Delete existing details
            $delete_stmt = $conn->prepare("DELETE FROM form_detail WHERE form_id = ?");
            $delete_stmt->bind_param('i', $form_id);
            $delete_stmt->execute();

            // Insert updated details
            $descriptions = $_POST['description'];
            $max_marks = $_POST['max_marks'];

            $stmt = $conn->prepare("INSERT INTO form_detail (form_id, description, max_marks) VALUES (?, ?, ?)");

            foreach ($descriptions as $key => $description) {
                $stmt->bind_param('isi', $form_id, $description, $max_marks[$key]);
                $stmt->execute();
            }

            echo '<script>alert("Form updated successfully!"); window.location.href = "forms.php";</script>';
        } catch(mysqli_sql_exception $e) {
            echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Edit Customized Form</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Edit Customized Form</li>
                    </ol>
                </nav>
                <div class="container mt-3 form-all" style="width: 650px;">
                    <h2 class="text-center form-heading">Edit Customized Form</h2>
                    <form method="post" action="edit_form.php?id=<?php echo $form_id; ?>" onsubmit="return validateMarks();">
                    <div class="row mb-3">
                            <div class="col">
                                <label for="title" class="form-label">Form Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="<?php echo $titleValue; ?>" required>
                                <div class="text-danger"><?php echo $titleError; ?></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label for="passing_marks" class="form-label">Passing Marks</label>
                                <input type="number" class="form-control" id="passing_marks" name="passing_marks" value="<?php echo $passing_marks_value; ?>" required>
                            </div>
                            <div class="col">
                                <label for="total_marks" class="form-label">Total Marks</label>
                                <input type="number" class="form-control" id="total_marks" name="total_marks" value="<?php echo $total_marks_value; ?>" required>
                            </div>
                        </div>

                        <div class="separator-line"></div>

                        <div id="form-details">
                            <h4>Form Details</h4>
                            <?php foreach ($details as $detail): ?>
                            <div class="row mb-3 detail-row">
                                <div class="col">
                                    <label for="description" class="form-label">Description</label>
                                    <input type="text" class="form-control" name="description[]" value="<?php echo htmlspecialchars($detail['description']); ?>" required>
                                </div>
                                <div class="col">
                                    <label for="max_marks" class="form-label">Max Marks</label>
                                    <input type="number" class="form-control" name="max_marks[]" value="<?php echo htmlspecialchars($detail['max_marks']); ?>" required>
                                </div>
                                <div class="col-auto d-flex align-items-center">
                                    <i class="fas fa-trash delete-icon" onclick="removeDetail(this)"></i>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="button" class="btn btn-success btn-add" onclick="addDetail()">Add Detail</button>

                        <div class="separator-line"></div>

                        <div class="d-grid gap-2 d-md-block">
                            <a href="forms.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addDetail() {
    var detailHTML = '<div class="row mb-3 detail-row">' +
        '<div class="col">' +
            '<label for="description" class="form-label">Description</label>' +
            '<input type="text" class="form-control" name="description[]" required>' +
        '</div>' +
        '<div class="col">' +
            '<label for="max_marks" class="form-label">Max Marks</label>' +
            '<input type="number" class="form-control" name="max_marks[]" required>' +
        '</div>' +
        '<div class="col-auto d-flex align-items-center">' +
            '<i class="fas fa-trash delete-icon" onclick="removeDetail(this)"></i>' +
        '</div>' +
    '</div>';
    document.getElementById('form-details').insertAdjacentHTML('beforeend', detailHTML);
}

function removeDetail(element) {
    element.closest('.detail-row').remove();
}

function validateMarks() {
    var passingMarks = parseInt(document.getElementById('passing_marks').value, 10);
    var totalMarks = parseInt(document.getElementById('total_marks').value, 10);

    if (isNaN(passingMarks) || isNaN(totalMarks) || passingMarks < 0 || totalMarks < 0) {
        alert('Please enter valid numbers for passing marks and total marks.');
        return false;
    }

    if (passingMarks > totalMarks) {
        alert('Passing marks cannot be greater than total marks.');
        return false;
    }

    return true;
}
</script>
</body>
</html>
