<?php
include 'session_student.php'; // Include session management
include 'config.php'; // Include database connection

// Retrieve student_id from session
$student_id = $_SESSION['student_id'];

$sql = "SELECT m.id, m.title, m.date, m.time, m.description, p.title AS project_title
        FROM meetings m
        JOIN projects p ON m.project_id = p.id
        WHERE p.student1 = ? OR p.student2 = ? OR p.student3 = ? OR p.student4 = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $student_id, $student_id, $student_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$meetings = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $meetings[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | View Meetings</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
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
                        <h1 class="heading">Scheduled Meetings</h1>
                    </div>
                    <div class="table-container">
                        <table class="table table-striped table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Time</th>
                                    <th>Date</th>
                                    <th>Project</th>
                                </tr>
                            </thead>
                            <tbody>
    <?php if (empty($meetings)): ?>
        <tr>
            <td colspan="4" class="text-center">No meetings found.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($meetings as $meeting): ?>
            <tr data-bs-toggle="modal" data-bs-target="#meetingModal<?php echo $meeting['id']; ?>">
                <td><?php echo htmlspecialchars($meeting['title']); ?></td>
                <td><?php echo date("h:i A", strtotime($meeting['time'])); ?></td>
                <td><?php echo date("d M Y", strtotime($meeting['date'])); ?></td>
                <td><?php echo htmlspecialchars($meeting['project_title']); ?></td>
            </tr>
            
            <!-- Meeting Modal -->
            <div class="modal fade" id="meetingModal<?php echo $meeting['id']; ?>" tabindex="-1" aria-labelledby="meetingModalLabel<?php echo $meeting['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="meetingModalLabel<?php echo $meeting['id']; ?>"><?php echo htmlspecialchars($meeting['title']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Time:</strong> <?php echo date("h:i A", strtotime($meeting['time'])); ?></p>
                            <p><strong>Date:</strong> <?php echo date("d M Y", strtotime($meeting['date'])); ?></p>
                            <p><strong>Project:</strong> <?php echo htmlspecialchars($meeting['project_title']); ?></p>
                            <p><strong>Description:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($meeting['description'])); ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
