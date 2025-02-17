<?php
include 'session_supervisor.php';
include 'config.php';

// Fetch presentations with project titles and only when 'send_to' is 'all'
$sql = "SELECT p.*, pr.title AS project_title, e.eventName 
        FROM presentations p 
        JOIN projects pr ON p.project_id = pr.id 
        JOIN events e ON p.type = e.EventID
        WHERE p.internal_evaluator_id = ? AND p.send_to = 'all'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['faculty_id']);
$stmt->execute();
$result = $stmt->get_result();

// Function to get form ID from customized_form table if form is assigned and visible
function getFormId($form_id, $conn) {
    $form_sql = "SELECT id FROM customized_form WHERE id = ? AND visible = 'yes'";
    $form_stmt = $conn->prepare($form_sql);
    $form_stmt->bind_param("i", $form_id);
    $form_stmt->execute();
    $form_result = $form_stmt->get_result();
    $form_id_result = null;
    if ($form_result->num_rows > 0) {
        $form_row = $form_result->fetch_assoc();
        $form_id_result = $form_row['id'];
    }
    $form_stmt->close();
    return $form_id_result;
}

// Function to check if marks are already submitted
function checkMarksSubmitted($form_id, $project_id, $faculty_id, $conn) {
    $marks_sql = "SELECT COUNT(*) AS count FROM marks WHERE form_id = ? AND project_id = ? AND internal = ?";
    $marks_stmt = $conn->prepare($marks_sql);
    $marks_stmt->bind_param("iii", $form_id, $project_id, $faculty_id);
    $marks_stmt->execute();
    $marks_result = $marks_stmt->get_result();
    $row = $marks_result->fetch_assoc();
    $marks_stmt->close();
    return $row['count'] > 0;
}

// Function to format time to AM/PM
function formatTime($time) {
    $dateTime = new DateTime($time);
    return $dateTime->format('g:i A');
}

// Function to format date to dd-mm-yyyy
function formatDate($date) {
    $dateTime = new DateTime($date);
    return $dateTime->format('d-m-Y');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Presentation Feedback</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
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
        .no-form {
            color: red;
            font-weight: bold;
        }
        
        .btn-primary:hover {
            background-color: #083c69; /* Darker shade on hover */
            border-color: #083c69;
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
        .action-btn {
            margin: 0 5px; /* Space between buttons */
            min-width: 120px; /* Set a minimum width for uniformity */
            text-align: center; /* Center text within buttons */
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
                        <li class="breadcrumb-item active" aria-current="page">Presentation Marks</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <h1 class="heading">Presentations</h1>
                    <div class="table-container">
                        <div class="table-responsive table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Project Title</th>
                                        <th>Event Name</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($presentation = $result->fetch_assoc()): 
                                            $form_id = getFormId($presentation['form_id'], $conn);  // Check if form_id exists in customized_form and is visible
                                            $marks_submitted = $form_id !== null ? checkMarksSubmitted($form_id, $presentation['project_id'], $_SESSION['faculty_id'], $conn) : false;
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($presentation['project_title']); ?></td>
                                            <td><?php echo htmlspecialchars($presentation['eventName']); ?></td>
                                            <td><?php echo htmlspecialchars(formatDate($presentation['date'])); ?></td>
                                            <td><?php echo htmlspecialchars(formatTime($presentation['time'])); ?></td>
                                            <td>
                                                <?php if ($form_id !== null): ?>
                                                    <?php if ($marks_submitted): ?>
                                                        <!-- Eye icon for 'View Marks' -->
                                                        <a href="view_marks.php?presentation_id=<?php echo htmlspecialchars($presentation['presentation_id']); ?>&form_id=<?php echo htmlspecialchars($form_id); ?>&project_id=<?php echo htmlspecialchars($presentation['project_id']); ?>" class="btn btn-outline-success action-btn">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                    <?php else: ?>
                                                        <!-- Button for 'Give Marks' -->
                                                        <a href="internal.php?presentation_id=<?php echo htmlspecialchars($presentation['presentation_id']); ?>&form_id=<?php echo htmlspecialchars($form_id); ?>" class="btn btn-primary action-btn">
                                                            <i class="fas fa-pencil-alt"></i> Evaluate
                                                        </a>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="no-form">No Form Available</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No presentations available</td>
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
<script>
    // Sidebar toggle functionality
    document.querySelector('.sidebar-toggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('collapsed');
        document.querySelector('.main-content').classList.toggle('collapsed');
    });
</script>
</body>
</html>

<?php
$conn->close();
?>