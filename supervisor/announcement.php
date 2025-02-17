<?php
include 'session_supervisor.php';

include 'config.php';

// Fetch announcements
$announcements_sql = "SELECT id, message, created_at FROM announcements WHERE audience_type IN ('supervisor', 'all') ORDER BY created_at DESC";
$announcements_result = $conn->query($announcements_sql);
$announcements = [];

if ($announcements_result->num_rows > 0) {
    while ($row = $announcements_result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

// Fetch presentations
$faculty_id = $_SESSION['faculty_id'];
$presentations_sql = "
    SELECT p.presentation_id, p.batch, DATE_FORMAT(p.date, '%Y') AS year, p.date, p.time, pr.title AS project_title, pr.description, r.room_number,
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
    WHERE p.internal_evaluator_id = '$faculty_id'
";
$presentations_result = $conn->query($presentations_sql);
$presentations = [];

if ($presentations_result->num_rows > 0) {
    while ($row = $presentations_result->fetch_assoc()) {
        $presentations[] = $row;
    }
}

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
        .table tbody tr {
    cursor: pointer; /* Cursor pointer for click indication */
    transition: background-color 0.3s ease-in-out; /* Smooth hover effect */
}

.table tbody tr:hover {
    background-color:rgb(7, 140, 249) !important; /* Light blue background on hover */
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

                <div class="container mt-5">
                    <h1 class="heading">Announcements</h1>
                    <div class="table-container">
                    <table class="table table-bordered table-striped">   
                            <thead>
                                <tr>
                                    <th>Message</th>
                                    <th style="width: 20%;">Date</th> <!-- Date column width increased -->
                                    </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($announcements)): ?>
                                    <tr>
                                        <td colspan="2" class="text-center">No announcements found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($announcements as $announcement): ?>
                                        <tr data-bs-toggle="modal" data-bs-target="#announcementModal<?php echo $announcement['id']; ?>">
                                            <td><?php echo htmlspecialchars($announcement['message']); ?></td>
                                            <td><?php echo date("d-m-Y", strtotime(htmlspecialchars($announcement['created_at']))); ?></td>
                                            </tr>

                                        <!-- Announcement Modal -->
                                        <div class="modal fade" id="announcementModal<?php echo $announcement['id']; ?>" tabindex="-1" aria-labelledby="announcementModalLabel<?php echo $announcement['id']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="announcementModalLabel<?php echo $announcement['id']; ?>">Announcement</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Message:</strong></p>
                                                        <p><?php echo nl2br(htmlspecialchars($announcement['message'])); ?></p>
                                                        <p><strong>Date:</strong> <?php echo date("d-m-Y", strtotime(htmlspecialchars($announcement['created_at']))); ?></p>
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
                                            <td><?php echo date("d-m-Y", strtotime(htmlspecialchars($presentation['date']))); ?></td>
                                            <td><?php echo date("h:i A", strtotime(htmlspecialchars($presentation['time']))); ?></td>
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
                                                        <p><strong>Date:</strong> <?php echo date("d-m-Y", strtotime(htmlspecialchars($presentation['date']))); ?></p>
                                                        <p><strong>Time:</strong> <?php echo date("h:i A", strtotime(htmlspecialchars($presentation['time']))); ?></p>
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
