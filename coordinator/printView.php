<?php
include 'session_coordinator.php';
include 'config.php';

// Fetch data with JOIN to include room number, evaluator names, and project title
$sql = "SELECT p.batch, r.room_number, p.date, p.time, pr.title AS project_title, 
               i.username AS internal_evaluator, e.name AS external_evaluator, p.send_to
        FROM presentations p
        LEFT JOIN rooms r ON p.room_id = r.room_id
        LEFT JOIN projects pr ON p.project_id = pr.id
        LEFT JOIN faculty i ON p.internal_evaluator_id = i.faculty_id
        LEFT JOIN external e ON p.external_evaluator_id = e.external_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .container {
            width: 100%;
            margin: auto;
            margin-top: 20px;
        }
        .bordered-container {
            border: 1.5px solid #cbcbcb;
            border-radius: 10px;
            padding: 20px;
            background-color: white;
            width: 100%;
            overflow-x: auto;
        }
        th, td {
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #e9ecef;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
            .bordered-container {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            table, th, td {
                border: 1px solid #000;
                font-size: 10px;
            }
            th {
                background-color: #e9ecef !important;
                -webkit-print-color-adjust: exact;
            }
            @page {
                size: landscape;
            }
        }
    </style>
</head>
<body>

<div class="container mt-3">
    <div class="bordered-container">
        <h2 class="text-center mb-4">Scheduled Presentations</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">Batch</th>
                    <th class="text-center">Room Number</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Time</th>
                    <th class="text-center">Project</th>
                    <th class="text-center">Internal Evaluator</th>
                    <th class="text-center">External Evaluator</th>
                    <th class="text-center">Send To</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['batch']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['time']); ?></td>
                            <td><?php echo htmlspecialchars($row['project_title']); ?></td>
                            <td><?php echo htmlspecialchars($row['internal_evaluator']); ?></td>
                            <td><?php echo htmlspecialchars($row['external_evaluator']); ?></td>
                            <td><?php echo htmlspecialchars($row['send_to']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No presentations scheduled yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
window.print();
</script>
</body>
</html>
