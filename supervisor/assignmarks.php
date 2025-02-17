<?php
include 'session_supervisor.php'; // Include session handling
include 'config.php';

// Initialize variables
$project_id = '';
$form_id = '';
$form_title = '';
$project_title = '';
$students_names = [];
$form_details = [];

// Get the supervisor ID from the session
$supervisor_id = $_SESSION['faculty_id'] ?? '';

// Fetch projects assigned to the supervisor or co-supervisor
$sql = $conn->prepare("SELECT id, title FROM projects WHERE supervisor = ? OR co_supervisor = ?");
$sql->bind_param("ii", $supervisor_id, $supervisor_id);
$sql->execute();
$result = $sql->get_result();

$projects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

// Handle form submission to populate fields based on selected project ID and form ID
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['projectId'])) {
        $project_id = $_POST['projectId'];
        
        // Fetch project title and student IDs for the selected project
        $sql = $conn->prepare("SELECT title, student1, student2, student3, student4 FROM projects WHERE id = ? AND (supervisor = ? OR co_supervisor = ?)");
        $sql->bind_param("iii", $project_id, $supervisor_id, $supervisor_id);
        $sql->execute();
        $result = $sql->get_result();
        $project = $result->fetch_assoc();

        if ($project) {
            $project_title = htmlspecialchars($project['title']);
            $student_ids = [$project['student1'], $project['student2'], $project['student3'], $project['student4']];
            
            foreach ($student_ids as $student_id) {
                if ($student_id) {
                    $sql = $conn->prepare("SELECT username FROM student WHERE student_id = ?");
                    $sql->bind_param("i", $student_id);
                    $sql->execute();
                    $result = $sql->get_result();
                    if ($result->num_rows > 0) {
                        $students_names[$student_id] = $result->fetch_assoc()['username'];
                    }
                }
            }
        }

        // Fetch form details
        if (isset($_POST['formId'])) {
            $form_id = $_POST['formId'];
            $form_query = "SELECT * FROM customized_form WHERE id = ?";
            $stmt = $conn->prepare($form_query);
            $stmt->bind_param("i", $form_id);
            $stmt->execute();
            $form_result = $stmt->get_result();
            if ($form_result->num_rows > 0) {
                $form_row = $form_result->fetch_assoc();
                $form_title = htmlspecialchars($form_row['title']);
            }

            // Fetch form details
            $details_query = "SELECT * FROM form_detail WHERE form_id = ?";
            $stmt = $conn->prepare($details_query);
            $stmt->bind_param("i", $form_id);
            $stmt->execute();
            $form_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
    }

    // Handle marks submission
    if (isset($_POST['marks'])) {
        $project_id = $_POST['projectId']; // Get project_id from POST data
        $form_id = $_POST['formId']; // Get form_id from POST data

        // Check if records already exist for the selected project_id, form_id, and supervisor or co-supervisor
        $check_query = "SELECT 1 FROM marks WHERE project_id = ? AND form_id = ? AND supervisor = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("iii", $project_id, $form_id, $supervisor_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            echo "<script>alert('Records already exist for this project, form, and supervisor. Please check and update existing records if needed.');</script>";
        } else {
            $total_marks_per_student = [];
            foreach ($_POST['marks'] as $description_id => $marks) {
                foreach ($marks as $student_id => $mark) {
                    $mark = (float) $mark;

                    $stmt = $conn->prepare("INSERT INTO marks (form_id, description_id, student_id, project_id, marks, supervisor) 
                                            VALUES (?, ?, ?, ?, ?, ?) 
                                            ON DUPLICATE KEY UPDATE marks = VALUES(marks), supervisor = VALUES(supervisor)");
                    $stmt->bind_param("iiidii", $form_id, $description_id, $student_id, $project_id, $mark, $supervisor_id);
                    $stmt->execute();

                    if (!isset($total_marks_per_student[$student_id])) {
                        $total_marks_per_student[$student_id] = 0;
                    }
                    $total_marks_per_student[$student_id] += $mark;
                }
            }

            // Insert total marks into 'total_student_marks' table
            $form_comment = $_POST['form_comment'] ?? ''; // Get the single comment for the form

foreach ($total_marks_per_student as $student_id => $total_marks) {
    $role = 'supervisor';
    $created_at = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO total_student_marks (student_id, faculty_id, form_id, project_id, total_marks, role, comment, created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE total_marks = VALUES(total_marks), role = VALUES(role), comment = VALUES(comment), created_at = VALUES(created_at)");
    $stmt->bind_param("iiiiisss", $student_id, $supervisor_id, $form_id, $project_id, $total_marks, $role, $form_comment, $created_at);
    $stmt->execute();
}

            header("Location: project_list.php?form_id=" . urlencode($form_id));

            echo "<script>alert('Marks submitted successfully!');</script>";

            echo "<script>document.getElementById('projectId').selectedIndex = 0;</script>";
        }

        $check_stmt->close();
    }

} else {
    if (isset($_GET['project_id'])) {
        $project_id = $_GET['project_id'];
        
        $sql = $conn->prepare("SELECT title, student1, student2, student3, student4 FROM projects WHERE id = ? AND (supervisor = ? OR co_supervisor = ?)");
        $sql->bind_param("iii", $project_id, $supervisor_id, $supervisor_id);
        $sql->execute();
        $result = $sql->get_result();
        $project = $result->fetch_assoc();

        if ($project) {
            $project_title = htmlspecialchars($project['title']);
            $student_ids = [$project['student1'], $project['student2'], $project['student3'], $project['student4']];
            
            foreach ($student_ids as $student_id) {
                if ($student_id) {
                    $sql = $conn->prepare("SELECT username FROM student WHERE student_id = ?");
                    $sql->bind_param("i", $student_id);
                    $sql->execute();
                    $result = $sql->get_result();
                    if ($result->num_rows > 0) {
                        $students_names[$student_id] = $result->fetch_assoc()['username'];
                    }
                }
            }
        }
    }

    if (isset($_GET['form_id'])) {
        $form_id = intval($_GET['form_id']);
        
        $form_query = "SELECT * FROM customized_form WHERE id = ?";
        $stmt = $conn->prepare($form_query);
        $stmt->bind_param("i", $form_id);
        $stmt->execute();
        $form_result = $stmt->get_result();
        if ($form_result->num_rows > 0) {
            $form_row = $form_result->fetch_assoc();
            $form_title = htmlspecialchars($form_row['title']);
            $passing_marks = htmlspecialchars($form_row['passing_marks']);
            $total_marks = htmlspecialchars($form_row['total_marks']);
        } else {
            echo "No form details found for form_id: " . htmlspecialchars($form_id);
        }

        // Fetch form details
        $details_query = "SELECT * FROM form_detail WHERE form_id = ?";
        $stmt = $conn->prepare($details_query);
        $stmt->bind_param("i", $form_id);
        $stmt->execute();
        $form_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Assign Marks</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .card-container {
            padding: 20px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
        }
        .heading {
            color: #0a4a91;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .btn-submit {
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
                        <li class="breadcrumb-item"><a href="project_list.php?form_id=<?php echo urlencode($form_id); ?>">Projects</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Assign Marks</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <h1 class="heading">Assign Marks</h1>
                    <?php if (!empty($form_title)): ?>
                        <div class="card-container mt-4">
                            <h2><?php echo htmlspecialchars($form_title); ?></h2>
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" onsubmit="return validateForm()">
                                <input type="hidden" name="projectId" value="<?php echo htmlspecialchars($project_id); ?>">
                                <input type="hidden" name="formId" value="<?php echo htmlspecialchars($form_id); ?>">

                                <table class="table table-striped table-hover mt-3 align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">S.No</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Max Marks</th>
                                            <?php foreach ($students_names as $student_id => $student_name): ?>
                                                <th scope="col"><?php echo htmlspecialchars($student_name); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody id="marksTableBody">
                                        <?php foreach ($form_details as $index => $detail): ?>
                                            <tr>
                                                <th scope="row"><?php echo $index + 1; ?></th>
                                                <td><?php echo htmlspecialchars($detail['description']); ?></td>
                                                <td><?php echo htmlspecialchars($detail['max_marks']); ?></td>
                                                <?php foreach ($students_names as $student_id => $student_name): ?>
                                                    <td>
                                                        <input type="number" name="marks[<?php echo intval($detail['id']); ?>][<?php echo intval($student_id); ?>]" 
                                                            class="form-control" min="0" max="<?php echo htmlspecialchars($detail['max_marks']); ?>" 
                                                            oninput="calculateTotals()">
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr id="totalRow">
                                            <th scope="row">Total</th>
                                            <td colspan="2"></td>
                                            <?php foreach ($students_names as $student_id => $student_name): ?>
                                                <td id="totalMarks_<?php echo intval($student_id); ?>">0</td>
                                            <?php endforeach; ?>
                                        </tr>
                                    </tbody>

                                    
                                </table>
                                <!-- Comment Section for Entire Form -->
<div class="mb-3 mt-4">
    <label for="form_comment" class="form-label"><strong>Comment for the Form</strong></label>
    <textarea name="form_comment" id="form_comment" class="form-control" rows="3" placeholder="Write your comment for the entire form..."></textarea>
</div>

                                <p><strong>Total Marks:</strong> <span id="totalMarks"><?php echo htmlspecialchars($total_marks); ?></span></p>
                                <p><strong>Passing Marks:</strong> <?php echo htmlspecialchars($passing_marks); ?></p>
                                <button type="submit" class="btn btn-primary btn-submit">Save All Marks</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
function calculateTotals() {
    // Initialize total marks array
    const totalMarks = {};

    // Get all input fields
    const inputs = document.querySelectorAll('input[type="number"]');

    // Loop through all inputs to calculate totals
    inputs.forEach(input => {
        const studentId = input.name.match(/marks\[\d+\]\[(\d+)\]/)[1]; // Extract student ID from input name
        const value = parseFloat(input.value) || 0; // Get input value, default to 0 if NaN

        if (!totalMarks[studentId]) {
            totalMarks[studentId] = 0; // Initialize if not already set
        }
        totalMarks[studentId] += value; // Add to total
    });

    // Update total marks in the total row
    for (const [studentId, total] of Object.entries(totalMarks)) {
        document.getElementById(`totalMarks_${studentId}`).textContent = total;
    }
}
function validateForm() {
    let valid = true;
    const marksTableBody = document.getElementById('marksTableBody');
    const inputs = marksTableBody.getElementsByTagName('input');

    // Check if all inputs are filled and within range
    for (let input of inputs) {
        const inputValue = parseFloat(input.value); // Parse the input value as a float
        const maxValue = parseFloat(input.max); // Parse the max attribute as a float

        if (isNaN(inputValue) || inputValue < 0 || inputValue > maxValue) {
            valid = false;
            input.classList.add('is-invalid'); // Add Bootstrap invalid class
        } else {
            input.classList.remove('is-invalid'); // Remove invalid class if valid
        }
    }

    // Show alert if form is invalid
    if (!valid) {
        alert("Please fill all fields correctly. Marks must be between 0 and the maximum marks.");
    }

    return valid; // Return the validation result
}

</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>