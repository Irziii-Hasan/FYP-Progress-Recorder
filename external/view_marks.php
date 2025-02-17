<?php
include 'session_external_evaluator.php';
include 'config.php'; // Include database configuration

// Get the parameters from the URL
$presentation_id = isset($_GET['presentation_id']) ? intval($_GET['presentation_id']) : null;
$form_id = isset($_GET['form_id']) ? intval($_GET['form_id']) : null;
$project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : null;

// Check if parameters are valid
if ($presentation_id === null || $form_id === null || $project_id === null) {
    die("Invalid parameters");
}

// Get the external evaluator ID from the session
$external_evaluator_id = $_SESSION['external_id']; // Adjust the session variable name for external evaluator

// Query to fetch and sum marks for each student based on project_id, presentation_id, form_id, and external evaluator ID
$sql = "SELECT s.student_id, s.username, SUM(m.marks) AS total_marks 
        FROM marks m
        JOIN student s ON m.student_id = s.student_id
        WHERE m.project_id = ? AND m.form_id = ? AND m.external = ? 
        GROUP BY m.student_id";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $project_id, $form_id, $external_evaluator_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> <!-- Font Awesome CSS -->
    <style>
        body {
            background-color: #f8f9fa; /* Light background */
        }
        .heading {
            color: #0a4a91;
            font-weight: 700;
            margin-bottom: 30px; /* Added bottom margin for spacing */
        }
        .table th, .table td {
            vertical-align: middle; /* Centering table content */
        }
        .table-container {
            background-color: white; /* White background for table */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        .breadcrumb-item a {
            color: #0a4a91; /* Custom breadcrumb link color */
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
                        <li class="breadcrumb-item"><a href="remarks.php">Presentations</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Marks</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <h1 class="heading">Marks Summary</h1>
                    <div class="table-container">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>S.No</th> <!-- Serial Number Column -->
                                        <th>Student Name</th>
                                        <th>Total Marks</th>
                                        <th>Action</th>  <!-- New Action Column -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php 
                                        $sno = 1; // Initialize serial number
                                        while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $sno++; ?></td> <!-- Serial Number Increment -->
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo htmlspecialchars($row['total_marks']); ?></td>
                                                <td>
                                                    <a href="edit_marks.php?student_id=<?php echo htmlspecialchars($row['student_id']); ?>&presentation_id=<?php echo htmlspecialchars($presentation_id); ?>&form_id=<?php echo htmlspecialchars($form_id); ?>&project_id=<?php echo htmlspecialchars($project_id); ?>" class="btn btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                </td>  <!-- Edit Icon with student_id -->
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">No marks found for this presentation.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
