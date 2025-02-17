<?php
include 'config.php'; // Database connection configuration
// session_student.php
session_start();
if (!isset($_SESSION['student_id'])) {
    // Redirect to login or show an error
    header("Location: login.php");
    exit();
}
$student_id = $_SESSION['student_id'];

// Fetch student projects and their progress
$sql = "
    SELECT p.title, 
           pd.predicted_progress, 
           td.current_progress
    FROM projects p
    JOIN project_predictions pd ON p.id = pd.project_id
    JOIN training_data td ON p.id = td.project_id
    WHERE p.student1 = ? OR p.student2 = ? OR p.student3 = ? OR p.student4 = ?
";

// Prepare and execute the SQL statement
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $student_id, $student_id, $student_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Student Progress</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .table-container {
            padding: 20px;
            border: 1px solid #cbcbcb;
            border-radius: 20px;
            background-color: white;
        }
        .heading {
            color: #0a4a91;
            font-weight: 700;
        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">

            <!-- BREADCRUMBS -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Student Progress</li>
                </ol>
            </nav>

            <div class="container mt-5">
                <h1 class="heading">Student Project Progress</h1>
                <div class="table-container">
                    <table class="table table-striped table-hover mt-3">
                        <thead>
                            <tr>
                                <th>Project Title</th>
                                <th>Predicted Progress %</th>
                                <th>Current Progress %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projects)): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No projects found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($projects as $project): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($project['title']); ?></td>
                                        <td><?php echo htmlspecialchars($project['predicted_progress']); ?></td>
                                        <td><?php echo htmlspecialchars($project['current_progress']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
