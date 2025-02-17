<?php
include 'session_supervisor.php';

// Initialize variables for project details
$project_id = '';
$title = '';
$markstitle = '';
$supervisor = '';
$co_supervisor = '';
$external_supervisor = '';
$student1 = '';
$student2 = '';
$student3 = '';
$student4 = '';
$marksStudent1 = '';
$marksStudent2 = '';
$marksStudent3 = '';
$marksStudent4 = '';

// Database connection
include 'config.php';

// Get the supervisor ID from the session
$supervisor_id = $_SESSION['faculty_id'] ?? ''; // Adjust this if the session variable name is different

// Fetch project IDs assigned to the supervisor
$sql = $conn->prepare("SELECT project_id FROM projects WHERE supervisor = ?");
$sql->bind_param("i", $supervisor_id);
$sql->execute();
$result = $sql->get_result();

$project_ids = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $project_ids[] = $row['project_id'];
    }
}

// Handle form submission to populate fields based on selected project ID
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['projectId'])) {
    $project_id = $_POST['projectId'];
    $sql = $conn->prepare("SELECT * FROM projects WHERE project_id = ? AND supervisor = ?");
    $sql->bind_param("si", $project_id, $supervisor_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];

        // Fetch Supervisor, Co-Supervisor, and External Supervisor names
        $supervisor_id = $row['supervisor'];
        $co_supervisor_id = $row['co_supervisor'];
        $external_supervisor_id = $row['external_supervisor'];
        $student1_id = $row['student1'];
        $student2_id = $row['student2'];
        $student3_id = $row['student3'];
        $student4_id = $row['student4'];

        // Fetch supervisor name
        $sql = $conn->prepare("SELECT username FROM faculty WHERE faculty_id = ?");
        $sql->bind_param("i", $supervisor_id);
        $sql->execute();
        $result = $sql->get_result();
        $supervisor = $result->num_rows > 0 ? $result->fetch_assoc()['username'] : '';

        // Fetch co-supervisor name
        $sql = $conn->prepare("SELECT username FROM faculty WHERE faculty_id = ?");
        $sql->bind_param("i", $co_supervisor_id);
        $sql->execute();
        $result = $sql->get_result();
        $co_supervisor = $result->num_rows > 0 ? $result->fetch_assoc()['username'] : '';

        // Fetch external supervisor name
        $sql = $conn->prepare("SELECT name FROM external WHERE external_id = ?");
        $sql->bind_param("i", $external_supervisor_id);
        $sql->execute();
        $result = $sql->get_result();
        $external_supervisor = $result->num_rows > 0 ? $result->fetch_assoc()['name'] : '';

        // Fetch student names
        $sql = $conn->prepare("SELECT username FROM student WHERE student_id = ?");
        
        // Student 1
        $sql->bind_param("i", $student1_id);
        $sql->execute();
        $result = $sql->get_result();
        $student1 = $result->num_rows > 0 ? $result->fetch_assoc()['username'] : '';

        // Student 2
        $sql->bind_param("i", $student2_id);
        $sql->execute();
        $result = $sql->get_result();
        $student2 = $result->num_rows > 0 ? $result->fetch_assoc()['username'] : '';

        // Student 3
        $sql->bind_param("i", $student3_id);
        $sql->execute();
        $result = $sql->get_result();
        $student3 = $result->num_rows > 0 ? $result->fetch_assoc()['username'] : '';

        // Student 4
        $sql->bind_param("i", $student4_id);
        $sql->execute();
        $result = $sql->get_result();
        $student4 = $result->num_rows > 0 ? $result->fetch_assoc()['username'] : '';
    }
}

// Handle form submission to save marks
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['marksStudent1'])) {
    // Fetch form values
    $marksStudent1 = $_POST['marksStudent1'];
    $marksStudent2 = $_POST['marksStudent2'] ?? null;
    $marksStudent3 = $_POST['marksStudent3'] ?? null;
    $marksStudent4 = $_POST['marksStudent4'] ?? null;
    $markstitle = $_POST['markstitle'] ?? '';

    $students = [
        $student1_id => $marksStudent1,
        $student2_id => $marksStudent2,
        $student3_id => $marksStudent3,
        $student4_id => $marksStudent4
    ];

    foreach ($students as $student_id => $marks) {
        if ($student_id && $marks !== null) {
            // Check if record exists
            $stmt = $conn->prepare("SELECT * FROM student_marks WHERE student_id = ? AND marks_title = ?");
            $stmt->bind_param("is", $student_id, $markstitle);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Update existing record
                $stmt = $conn->prepare("UPDATE student_marks SET student_marks = ? WHERE student_id = ? AND marks_title = ?");
                $stmt->bind_param("iis", $marks, $student_id, $markstitle);
            } else {
                // Insert new record
                $stmt = $conn->prepare("INSERT INTO student_marks (student_id, student_marks, marks_title) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $student_id, $marks, $markstitle);
            }

            $stmt->execute();
        }
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Marks Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            background-color: #ffffff; /* Set background color to white */
            padding: 15px; /* Reduced padding */
            border-radius: 8px; /* Optional: adds rounded corners */
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1); /* Optional: adds shadow for better visibility */
            max-width: 600px; /* Set a maximum width */
            width: 100%; /* Make it responsive */
        }
        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Makes sure the form is vertically centered */
            padding: 0 15px; /* Adds horizontal padding */
        }
        .form-control {
            font-size: 0.875rem; /* Slightly smaller text */
        }
        .form-label {
            font-size: 0.875rem; /* Slightly smaller label text */
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="container-fluid" id="content">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
            <li class="breadcrumb-item"><a href="viewMarks.php">View Marks</a></li>
            <li class="breadcrumb-item active" aria-current="page">Enter Marks</li>
                </ol>
            </nav>

            <div class="form-wrapper">
                <div class="form-container">
                    <h2 class="text-center">Assign Marks to Students</h2>
                    <form method="POST" action="marks.php">
                        <!-- Project ID Dropdown -->
                        <div class="form-group mb-3">
                            <label for="markstitle" class="form-label">Marks Title:</label>
                            <input type="text" class="form-control" id="markstitle" name="markstitle" value="<?php echo htmlspecialchars($markstitle); ?>" placeholder="Enter Project Title" required>
                        </div> 
                        <div class="form-group mb-3">
                            <label for="projectId" class="form-label">Project ID:</label>
                            <select class="form-control" id="projectId" name="projectId" onchange="this.form.submit()" required>
                                <option value="">Select Project ID</option>
                                <?php foreach ($project_ids as $id) : ?>
                                    <option value="<?php echo $id; ?>" <?php echo ($id == $project_id) ? 'selected' : ''; ?>><?php echo $id; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Project Details -->
                        <div class="form-group mb-3">
                            <label for="title" class="form-label">Title:</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Enter Project Title" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="supervisor" class="form-label">Supervisor:</label>
                            <input type="text" class="form-control" id="supervisor" name="supervisor" value="<?php echo htmlspecialchars($supervisor); ?>" placeholder="Enter Supervisor's Name">
                        </div>
                        <div class="form-group mb-3">
                            <label for="co_supervisor" class="form-label">Co-Supervisor:</label>
                            <input type="text" class="form-control" id="co_supervisor" name="co_supervisor" value="<?php echo htmlspecialchars($co_supervisor); ?>" placeholder="Enter Co-Supervisor's Name">
                        </div>
                        <div class="form-group mb-3">
                            <label for="external_supervisor" class="form-label">External Supervisor:</label>
                            <input type="text" class="form-control" id="external_supervisor" name="external_supervisor" value="<?php echo htmlspecialchars($external_supervisor); ?>" placeholder="Enter External Supervisor's Name">
                        </div>

                        <!-- Students and Marks -->
                        <div class="row">
                            <!-- First Column -->
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="student1" class="form-label">Student 1:</label>
                                    <input type="text" class="form-control" id="student1" name="student1" value="<?php echo htmlspecialchars($student1); ?>" placeholder="Student 1 ID" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="student2" class="form-label">Student 2:</label>
                                    <input type="text" class="form-control" id="student2" name="student2" value="<?php echo htmlspecialchars($student2); ?>" placeholder="Student 2 ID" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="student3" class="form-label">Student 3:</label>
                                    <input type="text" class="form-control" id="student3" name="student3" value="<?php echo htmlspecialchars($student3); ?>" placeholder="Student 3 ID">
                                </div>
                                <div class="form-group mt-3">
                                    <label for="student4" class="form-label">Student 4:</label>
                                    <input type="text" class="form-control" id="student4" name="student4" value="<?php echo htmlspecialchars($student4); ?>" placeholder="Student 4 ID">
                                </div>
                            </div>

                            <!-- Second Column -->
                            <div class="col-6 mb-3">
                                <div class="form-group">
                                    <label for="marksStudent1" class="form-label">Marks for Student 1:</label>
                                    <input type="number" class="form-control" id="marksStudent1" name="marksStudent1" placeholder="Enter Marks" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="marksStudent2" class="form-label">Marks for Student 2:</label>
                                    <input type="number" class="form-control" id="marksStudent2" name="marksStudent2" placeholder="Enter Marks" required>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="marksStudent3" class="form-label">Marks for Student 3:</label>
                                    <input type="number" class="form-control" id="marksStudent3" name="marksStudent3" placeholder="Enter Marks">
                                </div>
                                <div class="form-group mt-3">
                                    <label for="marksStudent4" class="form-label">Marks for Student 4:</label>
                                    <input type="number" class="form-control" id="marksStudent4" name="marksStudent4" placeholder="Enter Marks">
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-block">
                            <a href="viewMarks.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php

