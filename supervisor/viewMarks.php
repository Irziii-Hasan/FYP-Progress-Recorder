<?php
include 'session_supervisor.php';
include 'config.php';

// Fetch marks for projects supervised by the current supervisor
$faculty_id = $_SESSION['faculty_id'];

// Adjust the query to get project details based on student enrollments
$marks_sql = "
    SELECT pr.project_id, pr.title AS project_title, sm.marks_title, sm.student_id, sm.student_marks, s.username AS student_name
    FROM student_marks sm
    JOIN student s ON sm.student_id = s.student_id
    JOIN projects pr ON (
        pr.student1 = sm.student_id
        OR pr.student2 = sm.student_id
        OR pr.student3 = sm.student_id
        OR pr.student4 = sm.student_id
    )
    WHERE pr.supervisor = ?
";

$stmt = $conn->prepare($marks_sql);
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

$marks_data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $marks_data[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | View Marks</title>
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
                        <li class="breadcrumb-item active" aria-current="page">View Marks</li>
                    </ol>
                </nav>
                <div class="container mt-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="heading">Student Marks</h1>
                        <a href="marks.php" class="btn btn-primary">Give Marks</a>
                    </div>
                <div class="container mt-5">
                    <div class="table-container">
                        <table class="table table-striped table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Project ID</th>
                                    <th>Project Title</th>
                                    <th>Marks Title</th>
                                    <th>Student Name</th>
                                    <th>Marks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($marks_data)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No marks found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($marks_data as $mark): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($mark['project_id']); ?></td>
                                            <td><?php echo htmlspecialchars($mark['project_title']); ?></td>
                                            <td><?php echo htmlspecialchars($mark['marks_title']); ?></td>
                                            <td><?php echo htmlspecialchars($mark['student_name']); ?></td>
                                            <td><?php echo htmlspecialchars($mark['student_marks']); ?></td>
                                        </tr>
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
