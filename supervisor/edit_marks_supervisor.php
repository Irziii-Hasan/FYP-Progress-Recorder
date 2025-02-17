<?php
include 'session_supervisor.php';  // Include session management
include 'config.php';  // Include database configuration

// Get the parameters from the URL
$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;
$form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : null;
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;

// Check if parameters are valid
if ($student_id === null || $form_id === null || $project_id === null) {
    die("Invalid parameters");
}

// Get the supervisor ID from the session
$supervisor_id = $_SESSION['faculty_id']; 

// Fetch supervisor and co_supervisor from the projects table
$sql_project = "SELECT supervisor, co_supervisor FROM projects WHERE id = ?";
$stmt_project = $conn->prepare($sql_project);
$stmt_project->bind_param("i", $project_id);
$stmt_project->execute();
$result_project = $stmt_project->get_result();
$project_data = $result_project->fetch_assoc();

// Check if current user is either supervisor or co_supervisor
if ($project_data['supervisor'] != $supervisor_id && $project_data['co_supervisor'] != $supervisor_id) {
    die("You do not have permission to update marks for this project.");
}

// Fetch the username from the students table
$sql_user = "SELECT username FROM student WHERE student_id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $student_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$username = $result_user->fetch_assoc()['username'] ?? 'Unknown User';

// Fetch passing_marks and total_marks from customized_form table
$sql_customized = "SELECT passing_marks, total_marks FROM customized_form WHERE id = ?";
$stmt_customized = $conn->prepare($sql_customized);
$stmt_customized->bind_param("i", $form_id);
$stmt_customized->execute();
$result_customized = $stmt_customized->get_result();
$customized_data = $result_customized->fetch_assoc();
$passing_marks = $customized_data['passing_marks'] ?? 0;
$total_marks = $customized_data['total_marks'] ?? 0;

// If the form has been submitted, process the updates
$error_message = ""; // Initialize error message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marks = $_POST['marks'];
    $description_ids = $_POST['description_ids'];
    $total_marks = 0;  // Initialize total marks
    $valid = true; // Flag to check validity

    // Loop through marks to validate against max marks
    foreach ($marks as $index => $mark) {
        // Fetch the corresponding max marks
        $sql_max = "SELECT max_marks FROM form_detail WHERE id = ?";
        $stmt_max = $conn->prepare($sql_max);
        $stmt_max->bind_param("i", $description_ids[$index]);
        $stmt_max->execute();
        $result_max = $stmt_max->get_result();
        $max_marks = $result_max->fetch_assoc()['max_marks'] ?? 0;

        // Validate marks
        if ($mark > $max_marks) {
            $valid = false;
            $error_message = "Marks for {$marks[$index]} cannot exceed the maximum marks of {$max_marks}.";
            break; // Stop checking further marks
        }
    }

    if ($valid) {
        // If valid, proceed with updating marks
        foreach ($marks as $index => $mark) {
            $description_id = $description_ids[$index];

            // Update individual marks
            $sql_update = "UPDATE marks SET marks = ? 
                           WHERE student_id = ? AND form_id = ? AND project_id = ? AND (supervisor = ? OR supervisor = ?)
                           AND description_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("iiiiiii", $mark, $student_id, $form_id, $project_id, $project_data['supervisor'], $project_data['co_supervisor'], $description_id);
            $stmt_update->execute();

            // Add mark to total
            $total_marks += intval($mark);
        }

        
        // Update total marks in the total_student_marks table
        $sql_total_update = "UPDATE total_student_marks SET total_marks = ? 
                             WHERE student_id = ? AND form_id = ? AND project_id = ? AND faculty_id = ?";
        $stmt_total_update = $conn->prepare($sql_total_update);
        $stmt_total_update->bind_param("iiiii", $total_marks, $student_id, $form_id, $project_id, $supervisor_id);
        $stmt_total_update->execute();

        // Redirect back to the same page after updating to avoid form resubmission
        header("Location: view_marks_supervisor.php?student_id=$student_id&form_id=$form_id&project_id=$project_id&success=1");
        exit();
    }
}

// Query to fetch marks and related description for the specific student, form, and project
$sql = "SELECT m.description_id, m.marks, fd.description, fd.max_marks 
        FROM marks m
        JOIN projects p ON m.project_id = p.id
        JOIN form_detail fd ON m.description_id = fd.id
        WHERE m.student_id = ? AND m.form_id = ? AND m.project_id = ? 
        AND (p.supervisor = ? OR p.co_supervisor = ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $student_id, $form_id, $project_id, $project_data['supervisor'], $project_data['co_supervisor']);
$stmt->execute();
$result = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Marks</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .marks-summary {
            margin-top: 20px;
        }
        .alert {
            margin-top: 20px;
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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                    <li class="breadcrumb-item"><a href="view_marks_Supervisor.php?form_id=<?php echo urlencode($form_id); ?>&project_id=<?php echo urlencode($project_id); ?>">View Marks</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Marks</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                <h3 class="heading mb-4">Edit Marks for Student: <?php echo htmlspecialchars($username); ?></h3>
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php endif; ?>

                    <form action="" method="post">
                        <div class="table-container">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Marks</th>
                                        <th>Max Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <input type="text" name="descriptions[]" value="<?php echo htmlspecialchars($row['description']); ?>" class="form-control" readonly>
                                                    <input type="hidden" name="description_ids[]" value="<?php echo htmlspecialchars($row['description_id']); ?>">
                                                </td>
                                                <td>
                                                    <input type="number" name="marks[]" value="<?php echo htmlspecialchars($row['marks']); ?>" class="form-control" min="0" required>
                                                </td>
                                                <td>
                                                    <input type="text" value="<?php echo htmlspecialchars($row['max_marks']); ?>" class="form-control" readonly>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center">No data found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            
                            <button type="submit" class="btn btn-primary">Update Marks</button>

                            <div class="marks-summary">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Passing Marks:</strong> <?php echo htmlspecialchars($passing_marks); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Total Marks:</strong> <?php echo htmlspecialchars($total_marks); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
