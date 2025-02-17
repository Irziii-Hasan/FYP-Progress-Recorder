<?php
include 'session_supervisor.php'; // Session connection
include 'config.php';

// Get supervisor ID from session
$supervisor_id = $_SESSION['faculty_id']; 

// Fetch projects where the current supervisor is assigned
$sql_projects = "SELECT id FROM projects WHERE supervisor = ?";
$stmt_projects = $conn->prepare($sql_projects);
$stmt_projects->bind_param("i", $supervisor_id);
$stmt_projects->execute();
$result_projects = $stmt_projects->get_result();

$project_ids = [];
if ($result_projects->num_rows > 0) {
    while ($row = $result_projects->fetch_assoc()) {
        $project_ids[] = $row['id'];
    }
}

$stmt_projects->close();

// If no projects are assigned, show a message
if (empty($project_ids)) {
    $meetings = [];
} else {
    // Fetch meetings for these projects
    $placeholders = implode(',', array_fill(0, count($project_ids), '?'));
    $sql_meetings = "SELECT meetings.id, meetings.title, meetings.date, meetings.time, meetings.description, meetings.feedback, meetings.attendance_status, projects.title as project_title 
                     FROM meetings 
                     JOIN projects ON meetings.project_id = projects.id 
                     WHERE meetings.project_id IN ($placeholders)";
    
    $stmt_meetings = $conn->prepare($sql_meetings);
    $stmt_meetings->bind_param(str_repeat('i', count($project_ids)), ...$project_ids);
    $stmt_meetings->execute();
    $result_meetings = $stmt_meetings->get_result();

    $meetings = [];
    if ($result_meetings->num_rows > 0) {
        while ($row = $result_meetings->fetch_assoc()) {
            $meetings[] = $row;
        }
    }

    $stmt_meetings->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | View Meetings</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
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
        .btn-schedule {
            float: right;
        }
        .sidebar {
            height: 100vh; /* Full viewport height */
            overflow-y: auto; /* Add vertical scroll */
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
                        <li class="breadcrumb-item active" aria-current="page">Meetings</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="heading">Meetings</h1>
                        <a href="scheduleMeeting.php" class="btn btn-primary">Set Meeting</a>
                    </div>
                    <div class="table-container">
                    <table class="table table-bordered table-striped">   
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Project</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Feedback</th>
                                    <th>Attendance</th>
                                    <th>Actions</th> <!-- Column for actions -->
                                    <th>Send Credentials</th> <!-- New column for actions -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($meetings)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">No meetings found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($meetings as $meeting): ?>
                                        <tr data-bs-toggle="modal" data-bs-target="#viewMeetingModal<?php echo $meeting['id']; ?>">
                                            <td><?php echo htmlspecialchars($meeting['title']); ?></td>
                                            <td><?php echo htmlspecialchars($meeting['project_title']); ?></td>
                                            <td><?php echo date("d-m-Y", strtotime(htmlspecialchars($meeting['date']))); ?></td>
                                            <td><?php echo date("h:i A", strtotime(htmlspecialchars($meeting['time']))); ?></td>

                                            <td>
                                                <?php
                                                $feedback = htmlspecialchars($meeting['feedback']);
                                                if (strlen($feedback) > 15) {
                                                    echo substr($feedback, 0, 15) . '...';
                                                } else {
                                                    echo $feedback;
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($meeting['attendance_status']); ?></td>
                                            <td>
                                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editMeetingModal<?php echo $meeting['id']; ?>">
                                                    Remarks
                                                </button>
                                                
                                               
    <a href="editMeetings.php?id=<?php echo $meeting['id']; ?>" class="btn btn-primary">
        <i class="bi bi-pencil-square"></i>
    </a>
    <button type="button" class="btn btn-danger" onclick="deleteMeeting(<?php echo $meeting['id']; ?>)">
        <i class="bi bi-trash"></i>
    </button>   
                                            </td>
                                            <td>
                                            <button type="button" class="btn btn-info" onclick="sendEmail(<?php echo $meeting['id']; ?>)">
                                                    <i class="bi bi-envelope"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <!-- Edit Meeting Modal -->
                                        <div class="modal fade" id="editMeetingModal<?php echo $meeting['id']; ?>" tabindex="-1" aria-labelledby="editMeetingModalLabel<?php echo $meeting['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="post" action="updateMeeting.php">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="editMeetingModalLabel<?php echo $meeting['id']; ?>">Remarks</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <input type="hidden" name="meeting_id" value="<?php echo $meeting['id']; ?>">
                                                            <div class="mb-3">
                                                                <label for="feedback<?php echo $meeting['id']; ?>" class="form-label">Feedback</label>
                                                                <div id="feedback<?php echo $meeting['id']; ?>" class="form-check">
                                                                    <input class="form-check-input" type="radio" name="feedback" value="Excellent" <?php echo ($meeting['feedback'] == 'Excellent') ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="feedbackExcellent">Excellent</label>
                                                                </div>
                                                                <div id="feedback<?php echo $meeting['id']; ?>" class="form-check">
                                                                    <input class="form-check-input" type="radio" name="feedback" value="Good" <?php echo ($meeting['feedback'] == 'Good') ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="feedbackGood">Good</label>
                                                                </div>
                                                                <div id="feedback<?php echo $meeting['id']; ?>" class="form-check">
                                                                    <input class="form-check-input" type="radio" name="feedback" value="Fair" <?php echo ($meeting['feedback'] == 'Fair') ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="feedbackFair">Fair</label>
                                                                </div>
                                                                <div id="feedback<?php echo $meeting['id']; ?>" class="form-check">
                                                                    <input class="form-check-input" type="radio" name="feedback" value="Poor" <?php echo ($meeting['feedback'] == 'Poor') ? 'checked' : ''; ?>>
                                                                    <label class="form-check-label" for="feedbackPoor">Poor</label>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="attendance_status<?php echo $meeting['id']; ?>" class="form-label">Attendance</label>
                                                                <select class="form-select" id="attendance_status<?php echo $meeting['id']; ?>" name="attendance_status" required>
                                                                    <option value="Present" <?php echo ($meeting['attendance_status'] == 'Present') ? 'selected' : ''; ?>>Present</option>
                                                                    <option value="Absent" <?php echo ($meeting['attendance_status'] == 'Absent') ? 'selected' : ''; ?>>Absent</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                    function sendEmail(meetingId) {
                        // Create a form element
                        var form = document.createElement('form');
                        form.method = 'post';
                        form.action = 'sendEmail.php';

                        // Create an input element to hold the meeting ID
                        var input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'meeting_id';
                        input.value = meetingId;

                        // Append the input element to the form
                        form.appendChild(input);

                        // Append the form to the body and submit it
                        document.body.appendChild(form);
                        form.submit();
                    }
                </script>
    <script>
function deleteMeeting(meetingId) {
    if (confirm("Are you sure you want to delete this meeting?")) {
        // Send a DELETE request to the server
        fetch(`deleteMeeting.php?id=${meetingId}`)
            .then(response => response.text())
            .then(data => {
                if (data === "success") {
                    alert("Meeting deleted successfully.");
                    location.reload(); // Refresh the page to update the table
                } else {
                    alert("Failed to delete the meeting.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while deleting the meeting.");
            });
    }
}
</script>

            </div>
        </div>
    </div>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
