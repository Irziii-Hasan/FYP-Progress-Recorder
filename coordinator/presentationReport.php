<?php
include 'session_coordinator.php';
include 'config.php';

// Get the selected batch and event type from the query parameters
$batch = isset($_GET['batch']) ? $_GET['batch'] : '';
$event = isset($_GET['event']) ? $_GET['event'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FYP | Select Report Columns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .heading {
            color: #0a4a91;
            font-weight: 700;
        }

        .border{
            padding: 20px;
            background-color: white;
            margin: 20px;
            border-radius: 20px;
            width: 60vw !important;

        }
    </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="wrapper">
    <?php include 'sidebar.php'; ?>

    <div class="container mt-5">
        <h2 class="heading text-center mb-4">Select Columns for Batch: <?php echo htmlspecialchars($batch); ?>, Event: <?php echo htmlspecialchars($event); ?></h2>
        <div class="container border">
            <!-- Column Selection Form -->
            <form action="view_report.php" method="get">
                <input type="hidden" name="batch" value="<?php echo htmlspecialchars($batch); ?>">
                <input type="hidden" name="event" value="<?php echo htmlspecialchars($event); ?>">
                <div class="mb-3">
                    <h3 for="columns" class="form-label" style="text-align: center;">Select Columns to Include in the Report:</h3>
                    <div class="row">
                        <div class="col text-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="event_name" id="event_name">
                            <label class="form-check-label" for="event_name">Event</label>
                        </div> 
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="batch" id="batch">
                            <label class="form-check-label" for="batch">Batch</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="room_number" id="room_number">
                            <label class="form-check-label" for="room_number">Room</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="date" id="date">
                            <label class="form-check-label" for="date">Date</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="time" id="time">
                            <label class="form-check-label" for="time">Time</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="project_title" id="project_title">
                            <label class="form-check-label" for="project_title">Project Title</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="internal_evaluator" id="internal_evaluator">
                            <label class="form-check-label" for="internal_evaluator">Internal Evaluator</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="external_evaluator" id="external_evaluator">
                            <label class="form-check-label" for="external_evaluator">External Evaluator</label>
                        </div>  
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="columns[]" value="student_name" id="student_name">
                            <label class="form-check-label" for="student_name">Student Name</label>
                        </div> 
                        </div>
                    </div>           
                </div>
                <div class="text-center">
                <button type="submit" class="btn btn-primary">Generate Report</button>

                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
