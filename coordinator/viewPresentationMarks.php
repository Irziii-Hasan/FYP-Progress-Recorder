<?php
include 'session_coordinator.php';
include 'config.php';

// Get the selected batch and event type from the form submission
$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$event = isset($_GET['event']) ? $_GET['event'] : '';

if ($batch && $event) {
    // Fetch records with separate columns for each evaluator, including supervisor
    $sql = "SELECT b.batchName AS batch, r.room_number, p.date, p.time, e.EventName AS event_name, pr.title AS project_title, 
                   f.username AS supervisor_name,
                   GROUP_CONCAT(DISTINCT i.username ORDER BY i.username ASC) AS internal_evaluator,
                   GROUP_CONCAT(DISTINCT ex.name ORDER BY ex.name ASC) AS external_evaluator
            FROM presentations p
            LEFT JOIN rooms r ON p.room_id = r.room_id
            LEFT JOIN projects pr ON p.project_id = pr.id
            LEFT JOIN faculty f ON pr.supervisor = f.faculty_id
            LEFT JOIN faculty i ON FIND_IN_SET(i.faculty_id, p.internal_evaluator_id)
            LEFT JOIN external ex ON FIND_IN_SET(ex.external_id, p.external_evaluator_id)
            LEFT JOIN batches b ON p.batch = b.batchID
            LEFT JOIN events e ON p.type = e.eventID
            WHERE b.batchName = ? AND e.EventName = ?
            GROUP BY pr.title, p.date, p.time, r.room_number, b.batchName, e.EventName";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $batch, $event);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = null;
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
    <link rel="stylesheet" href="style.css">
    <style>
        .container-with-buttons { width: 90%; margin-top: 20px; }
        .button-row { margin-bottom: 20px; }
        .bordered-container { 
            border: 1.5px solid #cbcbcb;
             border-radius: 10px; padding: 20px; background-color: white; width: 100%; overflow-x: auto; }
        .table th, .table td { text-align: center; vertical-align: middle; }
    </style>
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
                    <div class="row justify-content-center">
                        <div class="col-lg-12 bordered-container">
                            <h2 class="text-center mb-4">Records for Batch: <?php echo htmlspecialchars($batch); ?>, Event: <?php echo htmlspecialchars($event); ?></h2>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Batch</th>
                                        <th>Room</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Event</th>
                                        <th>Project</th>
                                        <th>Supervisor</th>
                                        <th>Supervisor Marks</th>
                                        <th>Internal Evaluator(s)</th>
                                        <th>Internal Marks</th>
                                        <th>External Evaluator(s)</th>
                                        <th>External Marks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result && $result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <?php
                                            $formatted_date = date('d-m-Y', strtotime($row['date']));
                                            $formatted_time = date('h:i A', strtotime($row['time']));
                                            $internal_evaluators = explode(',', $row['internal_evaluator']);
                                            $external_evaluators = explode(',', $row['external_evaluator']);
                                            ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['batch']); ?></td>
                                                <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                                                <td><?php echo htmlspecialchars($formatted_date); ?></td>
                                                <td><?php echo htmlspecialchars($formatted_time); ?></td>
                                                <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['project_title']); ?></td>
                                                <td><?php echo htmlspecialchars($row['supervisor_name']); ?></td>
                                                <td><!-- Placeholder for supervisor marks --></td>
                                                
                                                <!-- Internal Evaluators with marks -->
                                                <td>
                                                    <?php foreach ($internal_evaluators as $internal): ?>
                                                        <div><?php echo htmlspecialchars($internal); ?></div>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td><!-- Placeholder for internal marks --></td>
                                                
                                                <!-- External Evaluators with marks -->
                                                <td>
                                                    <?php foreach ($external_evaluators as $external): ?>
                                                        <div><?php echo htmlspecialchars($external); ?></div>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td><!-- Placeholder for external marks --></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="12" class="text-center">No records found for this batch and event.</td>
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

</body>
</html>
