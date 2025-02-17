<?php
include 'session_external_evaluator.php'; 

include 'config.php';



// Fetch presentations
$external_evaluator_id = $_SESSION['external_id'];
$presentations_sql = "
    SELECT p.presentation_id, p.batch, DATE_FORMAT(p.date, '%Y') AS year, p.date, p.time, p.type, pr.title AS project_title, pr.description, r.room_number,
        s1.username AS student1_name, s1.seat_number AS student1_seat,
        s2.username AS student2_name, s2.seat_number AS student2_seat,
        s3.username AS student3_name, s3.seat_number AS student3_seat,
        s4.username AS student4_name, s4.seat_number AS student4_seat
    FROM presentations p
    JOIN projects pr ON p.project_id = pr.id
    JOIN rooms r ON p.room_id = r.room_id
    LEFT JOIN student s1 ON pr.student1 = s1.student_id
    LEFT JOIN student s2 ON pr.student2 = s2.student_id
    LEFT JOIN student s3 ON pr.student3 = s3.student_id
    LEFT JOIN student s4 ON pr.student4 = s4.student_id
    WHERE p.external_evaluator_id = ?
";
$presentations_stmt = $conn->prepare($presentations_sql);
$presentations_stmt->bind_param('s', $external_evaluator_id);
$presentations_stmt->execute();
$presentations_result = $presentations_stmt->get_result();
$presentations = [];

if ($presentations_result->num_rows > 0) {
    while ($row = $presentations_result->fetch_assoc()) {
        $presentation_id = $row['presentation_id'];

        // Define tables for marks distribution based on presentation type
        $types = [
            'FYP-I Mid' => 'fyp1_mid_external_marks',
            'FYP-I Summer' => 'fyp1_summer_external_marks',
            'FYP-I Terminal' => 'fyp1_terminal_external_marks',
            'FYP-II Mid' => 'fyp2_mid_external_marks',
            'FYP-II Terminal' => 'fyp2_terminal_external_marks'
        ];

        $marks_submitted = false;
        if (isset($types[$row['type']])) {
            $table = $types[$row['type']];
            $check_marks_sql = "SELECT COUNT(*) FROM $table WHERE presentation_id = ? AND external_evaluator_id = ?";
            $check_stmt = $conn->prepare($check_marks_sql);

            if ($check_stmt) {
                $check_stmt->bind_param('is', $presentation_id, $external_evaluator_id);
                $check_stmt->execute();
                $check_result = $check_stmt->get_result();
                $marks_submitted = $check_result->fetch_column() > 0;
                $check_stmt->close();
            } else {
                // Handle the error if the prepared statement fails
                $marks_submitted = false;
            }
        }

        $row['marks_submitted'] = $marks_submitted;
        $presentations[] = $row;
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Announcements and Presentations</title>
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
                        <li class="breadcrumb-item active" aria-current="page">Announcements & Presentations</li>
                    </ol>
    </nav>
                    <h1 class="heading mt-5">Presentations</h1>
                    <div class="table-container">
                        <table class="table table-striped table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Project Title</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Room Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($presentations)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No presentations found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($presentations as $presentation): ?>
                                        <tr data-bs-toggle="modal" data-bs-target="#presentationModal<?php echo $presentation['presentation_id']; ?>">
                                            <td><?php echo htmlspecialchars($presentation['project_title']); ?></td>
                                            <td><?php echo htmlspecialchars($presentation['date']); ?></td>
                                            <td><?php echo htmlspecialchars($presentation['time']); ?></td>
                                            <td><?php echo htmlspecialchars($presentation['room_number']); ?></td>
                                        </tr>

                                        <!-- Presentation Modal -->
                                        <div class="modal fade" id="presentationModal<?php echo $presentation['presentation_id']; ?>" tabindex="-1" aria-labelledby="presentationModalLabel<?php echo $presentation['presentation_id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="presentationModalLabel<?php echo $presentation['presentation_id']; ?>">Presentation Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Project Title:</strong> <?php echo htmlspecialchars($presentation['project_title']); ?></p>
                                                        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($presentation['description'])); ?></p>
                                                        <p><strong>Date:</strong> <?php echo htmlspecialchars($presentation['date']); ?></p>
                                                        <p><strong>Time:</strong> <?php echo htmlspecialchars($presentation['time']); ?></p>
                                                        <p><strong>Room Number:</strong> <?php echo htmlspecialchars($presentation['room_number']); ?></p>
                                                        <p><strong>Students:</strong></p>
                                                        <ul>
                                                            <?php if (!empty($presentation['student1_name'])): ?>
                                                                <li><?php echo htmlspecialchars($presentation['student1_name']); ?> (Seat Number: <?php echo htmlspecialchars($presentation['student1_seat']); ?>)</li>
                                                            <?php endif; ?>
                                                            <?php if (!empty($presentation['student2_name'])): ?>
                                                                <li><?php echo htmlspecialchars($presentation['student2_name']); ?> (Seat Number: <?php echo htmlspecialchars($presentation['student2_seat']); ?>)</li>
                                                            <?php endif; ?>
                                                            <?php if (!empty($presentation['student3_name'])): ?>
                                                                <li><?php echo htmlspecialchars($presentation['student3_name']); ?> (Seat Number: <?php echo htmlspecialchars($presentation['student3_seat']); ?>)</li>
                                                            <?php endif; ?>
                                                            <?php if (!empty($presentation['student4_name'])): ?>
                                                                <li><?php echo htmlspecialchars($presentation['student4_name']); ?> (Seat Number: <?php echo htmlspecialchars($presentation['student4_seat']); ?>)</li>
                                                            <?php endif; ?>
                                                        </ul>
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
