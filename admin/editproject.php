<?php
include 'session_admin.php'; // Include session handling

// Database connection 
include 'config.php';

if (isset($_GET['id'])) {
    $project_id = intval($_GET['id']);

    // Fetch the current values for the selected project
    $stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
    } else {
        echo '<script>alert("No project found.");</script>';
        echo '<script>window.location.href = "project.php";</script>';
        exit;
    }

    // Fetch list of students, faculty, externals, durations, and batches
    $students_result = $conn->query("SELECT student_id, username FROM student");
    $students = $students_result->fetch_all(MYSQLI_ASSOC);

    $faculty_result = $conn->query("SELECT faculty_id, username FROM faculty");
    $faculty = $faculty_result->fetch_all(MYSQLI_ASSOC);

    $external_result = $conn->query("SELECT external_id, name FROM external");
    $externals = $external_result->fetch_all(MYSQLI_ASSOC);

    $durations_result = $conn->query("SELECT id, title FROM course_durations");
    $durations = $durations_result->fetch_all(MYSQLI_ASSOC);

    $batches_result = $conn->query("SELECT batchid, batchname FROM batches");
    $batches = $batches_result->fetch_all(MYSQLI_ASSOC);
} else {
    echo '<script>alert("No project ID provided.");</script>';
    echo '<script>window.location.href = "project.php";</script>';
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $student1 = htmlspecialchars($_POST['student1']);
    $student2 = htmlspecialchars($_POST['student2']);
    $student3 = htmlspecialchars($_POST['student3']);
    $student4 = htmlspecialchars($_POST['student4']);
    $supervisor = htmlspecialchars($_POST['supervisor']);
    $co_supervisor = htmlspecialchars($_POST['co_supervisor']);
    $external_supervisor = htmlspecialchars($_POST['external_supervisor']);
    $duration_id = intval($_POST['duration_id']);
    $batch_id = intval($_POST['batch_id']);

    // Validate title: should only contain alphabets and spaces
    if (!preg_match("/^[a-zA-Z\s]+$/", $title)) {
        echo '<script>alert("Title must contain only alphabets and spaces.");</script>';
    } else {
        // Update the project record in the database
        $stmt = $conn->prepare("UPDATE projects SET title=?, description=?, student1=?, student2=?, student3=?, student4=?, supervisor=?, co_supervisor=?, external_supervisor=?, duration=?, batch=? WHERE id=?");
        $stmt->bind_param("sssssssssiis", $title, $description, $student1, $student2, $student3, $student4, $supervisor, $co_supervisor, $external_supervisor, $duration_id, $batch_id, $id);
        
        if ($stmt->execute()) {
            echo '<script>alert("Project updated successfully.");</script>';
            echo '<script>window.location.href = "project.php";</script>';
        } else {
            echo '<script>alert("Error updating project: ' . $conn->error . '");</script>';
        }
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .form-all {
            padding: 20px 30px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
        }
        .form-heading {
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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                        <li class="breadcrumb-item"><a href="project.php">Projects</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Edit Project</li>
                    </ol>
                </nav>
                <div class="container mt-3 form-all" style="width: 650px;">
                    <h2 class="text-center form-heading">Edit Project</h2>
                    <form action="editproject.php?id=<?php echo htmlspecialchars($project_id); ?>" method="post">
                        <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($project['description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="student1" class="form-label">Student 1</label>
                            <select class="form-control" id="student1" name="student1">
                                <option value="">Select Student 1</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['student_id']; ?>" <?php if ($student['student_id'] == $project['student1']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($student['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="student2" class="form-label">Student 2</label>
                            <select class="form-control" id="student2" name="student2">
                                <option value="">Select Student 2</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['student_id']; ?>" <?php if ($student['student_id'] == $project['student2']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($student['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="student3" class="form-label">Student 3</label>
                            <select class="form-control" id="student3" name="student3">
                                <option value="">Select Student 3</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['student_id']; ?>" <?php if ($student['student_id'] == $project['student3']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($student['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="student4" class="form-label">Student 4</label>
                            <select class="form-control" id="student4" name="student4">
                                <option value="">Select Student 4</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?php echo $student['student_id']; ?>" <?php if ($student['student_id'] == $project['student4']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($student['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="supervisor" class="form-label">Supervisor</label>
                            <select class="form-control" id="supervisor" name="supervisor" required>
                                <option value="">Select Supervisor</option>
                                <?php foreach ($faculty as $member): ?>
                                    <option value="<?php echo $member['faculty_id']; ?>" <?php if ($member['faculty_id'] == $project['supervisor']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($member['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="co_supervisor" class="form-label">Co-Supervisor</label>
                            <select class="form-control" id="co_supervisor" name="co_supervisor">
                                <option value="">Select Co-Supervisor</option>
                                <?php foreach ($faculty as $member): ?>
                                    <option value="<?php echo $member['faculty_id']; ?>" <?php if ($member['faculty_id'] == $project['co_supervisor']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($member['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
    <label for="external_supervisor" class="form-label">External Supervisor</label>
    <input type="text" class="form-control" id="external_supervisor" name="external_supervisor" 
           value="<?php echo htmlspecialchars($project['external_supervisor']); ?>" required>
</div>

                        <div class="mb-3">
                            <label for="duration_id" class="form-label">Duration</label>
                            <select class="form-control" id="duration_id" name="duration_id" required>
                                <option value="">Select Duration</option>
                                <?php foreach ($durations as $duration): ?>
                                    <option value="<?php echo $duration['id']; ?>" <?php if (isset($project['duration']) && $duration['id'] == $project['duration']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($duration['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        <div class="mb-3">
                            <label for="batch_id" class="form-label">Batch</label>
                            <select class="form-control" id="batch_id" name="batch_id" required>
                                <option value="">Select Batch</option>
                                <?php foreach ($batches as $batch): ?>
                                    <option value="<?php echo $batch['batchid']; ?>" <?php if (isset($project['batch']) && $batch['batchid'] == $project['batch']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($batch['batchname']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="d-grid gap-2 d-md-block">
                            <a href="project.php" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
