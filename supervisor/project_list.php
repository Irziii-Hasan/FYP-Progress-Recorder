<?php
include 'session_supervisor.php'; // Include session management
include 'config.php'; // Include database configuration

// Get form_id from the URL
$form_id = $_GET['form_id'] ?? '';

// Initialize variables
$projects = [];
$supervisor_id = $_SESSION['faculty_id'] ?? '';

// Fetch projects assigned to the supervisor for the specific form
$sql = $conn->prepare("SELECT id, title FROM projects WHERE supervisor = ? OR co_supervisor=?");
$sql->bind_param("ii", $supervisor_id, $supervisor_id); // Pass the $supervisor_id twice
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

// Function to check if marks are already submitted by any supervisor or co_supervisor
function checkMarksSubmitted($form_id, $project_id, $conn) {
    $marks_sql = "SELECT COUNT(*) AS count FROM marks 
                  WHERE form_id = ? AND project_id = ? AND (supervisor IS NOT NULL OR internal IS NOT NULL)";
    $marks_stmt = $conn->prepare($marks_sql);
    $marks_stmt->bind_param("ii", $form_id, $project_id);
    $marks_stmt->execute();
    $marks_result = $marks_stmt->get_result();
    $row = $marks_result->fetch_assoc();
    $marks_stmt->close();
    return $row['count'] > 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Head content... -->
         <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
<style>
    .heading {
            color: #0a4a91;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .table-container {
            background-color: white; /* White background for table */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
</style>
</head>
<body>
<?php include 'nav.php'; ?> <!-- Include Navbar -->

<div class="wrapper">
    <?php include 'sidebar.php'; ?> <!-- Include Sidebar -->

    <div class="container-fluid" id="content">
        <div class="row">
            <div class="col-md-12">
                <!-- BREADCRUMBS -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                    <li class="breadcrumb-item"><a href="visibleforms.php">My Project Evaluation</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Project List</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <h1 class="heading">Projects list</h1>

                    <?php if (!empty($projects)): ?>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Project Title</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($projects as $project): ?>
                                            <?php
                                                // Check if marks have been submitted by any supervisor for this project and form
                                                $marks_submitted = checkMarksSubmitted($form_id, $project['id'], $conn);
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($project['title']); ?></td>
                                                <td>
                                                    <?php if ($marks_submitted): ?>
                                                        <a href="view_marks_supervisor.php?project_id=<?php echo $project['id']; ?>&form_id=<?php echo $form_id; ?>" class="btn btn-outline-success">
                                                            <i class="fas fa-eye"></i> View 
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="assignmarks.php?project_id=<?php echo $project['id']; ?>&form_id=<?php echo $form_id; ?>" class="btn btn-primary">
                                                            <i class="fas fa-pencil-alt"></i> Evaluate

                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-center no-form">No projects found for this form.</p>
                    <?php endif; ?>
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
