<?php
include 'session_coordinator.php';
include 'config.php';

// Get the selected batch and event type from the form submission
$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$event = isset($_GET['event']) ? $_GET['event'] : '';

if ($batch && $event) {
    // Fetch records based on the selected batch and event type
    $sql = "
    SELECT 
        pr.id,
        p.presentation_id, 
        b.batchName AS batch, 
        b.BatchID, -- Ensuring BatchID is fetched
        r.room_number, 
        r.room_id,
        p.date, 
        p.time, 
        e.EventName AS event_name, 
        pr.id AS project_id,
        pr.project_id AS project_ID,
        pr.title AS project_title, 
        GROUP_CONCAT(DISTINCT i.username ORDER BY i.username ASC SEPARATOR ', ') AS internal_evaluator,
        GROUP_CONCAT(DISTINCT ex.name ORDER BY ex.name ASC SEPARATOR ', ') AS external_evaluator,
        GROUP_CONCAT(DISTINCT s.username ORDER BY s.username ASC SEPARATOR ', ') AS student_names,
        p.send_to AS send_to,
        p.type AS type,
        p.form_id AS form_id
    FROM presentations p
    LEFT JOIN rooms r ON p.room_id = r.room_id
    LEFT JOIN projects pr ON p.project_id = pr.id
    LEFT JOIN student s ON s.student_id IN (pr.student1, pr.student2, pr.student3, pr.student4)
    LEFT JOIN faculty i ON FIND_IN_SET(i.faculty_id, p.internal_evaluator_id)
    LEFT JOIN external ex ON p.external_evaluator_id = ex.external_id
    LEFT JOIN batches b ON p.batch = b.BatchID
    LEFT JOIN events e ON p.type = e.eventID
    WHERE b.batchName = ? AND e.EventName = ?
    GROUP BY 
        pr.project_id, 
        pr.title, 
        p.date, 
        p.time, 
        r.room_number, 
        b.batchName, 
        e.EventName, 
        p.send_to,
        p.type,
        p.form_id
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $batch, $event);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null; // If no batch or event is selected
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP | View Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
        }    </style>
</head>
<body>
<?php include 'nav.php'; ?>
<div class="wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="container-fluid row" id="content">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                        <li class="breadcrumb-item"><a href="view_Schedule.php">View Schedule</a></li>
                        <li class="breadcrumb-item active" aria-current="page">View Records</li>
                    </ol>
                </nav>
                <div class="container mt-3 container-with-buttons">
                    <div class="row button-row">
                        <div class="col text-end">
                            <a href="presentationreport.php?batch=<?php echo urlencode($batch); ?>&event=<?php echo urlencode($event); ?>" class="btn btn-success">Generate Report</a>
                            <a href="assignPresentation.php" class="btn btn-primary">Assign Presentation</a>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="col-lg-12 bordered-container">
                            <h2 class="text-center mb-4">Records for Batch: <?php echo htmlspecialchars($batch); ?>, Event: <?php echo htmlspecialchars($event); ?></h2>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S. No.</th>
                                        <th>Project ID</th>
                                        <th>Project Title</th>
                                        <th>Batch</th>
                                        <th>Student Names</th>
                                        <th>Internal Evaluator(s)</th>
                                        <th>External Evaluator(s)</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Room</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php $serial = 1; ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $serial++; ?></td>
                                                <td><?php echo htmlspecialchars($row['project_ID']); ?></td>
                                                <td><?php echo htmlspecialchars($row['project_title']); ?></td>
                                                <td><?php echo htmlspecialchars($row['batch']); ?></td>
                                                <td><?php echo htmlspecialchars($row['student_names'] ?: 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($row['internal_evaluator']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['external_evaluator'] ?: 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['date']))); ?></td>
                                                <td><?php echo htmlspecialchars(date('h:i A', strtotime($row['time']))); ?></td>
                                                <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                                                <td>
                                                    <button 
                                                        class="btn btn-danger delete-btn"
                                                        data-room-id="<?php echo htmlspecialchars($row['room_id']); ?>"
                                                        data-batch-id="<?php echo htmlspecialchars($row['BatchID']); ?>"
                                                        data-date="<?php echo htmlspecialchars($row['date']); ?>"
                                                        data-time="<?php echo htmlspecialchars($row['time']); ?>"
                                                        data-project-id="<?php echo htmlspecialchars($row['project_id']); ?>"
                                                        data-form-id="<?php echo htmlspecialchars($row['form_id']); ?>"
                                                    >
                                                    <i class='bi bi-trash'></i>                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="text-center">No records found for this batch and event.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <div class="text-end">
                                <a href="view_Schedule.php" class="btn btn-secondary">Back to Schedule</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function () {
            if (confirm('Are you sure you want to delete this presentation?')) {
                const params = {
                    room_id: this.dataset.roomId,
                    batch_id: this.dataset.batchId,
                    date: this.dataset.date,
                    time: this.dataset.time,
                    project_id: this.dataset.projectId,
                    form_id: this.dataset.formId,
                };

                fetch('delete_presentation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(params),
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                })
                .catch(error => {
                    console.error(error);
                    alert('An error occurred.');
                });
            }
        });
    });
});
</script>
</body>
</html>
