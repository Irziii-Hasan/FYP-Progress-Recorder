<?php
include 'session_supervisor.php';
include 'config.php';

// Fetch marks details with related information
$query = "
    SELECT 
        m.marks,
        cf.title AS form_title,
        fd.description,
        fd.max_marks,
        p.title AS project_title,
        s.username AS student_username
    FROM marks m
    JOIN form_detail fd ON m.description_id = fd.id
    JOIN customized_form cf ON fd.form_id = cf.id
    JOIN presentations pr ON m.form_id = cf.id
    JOIN projects p ON pr.project_id = p.id
    JOIN student s ON m.student_id = s.student_id
    WHERE pr.internal_evaluator_id = ?
    ORDER BY cf.title, p.title, s.username
";
$stmt = $conn->prepare($query);
$faculty_id = $_SESSION['faculty_id'] ?? '';
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();

$marks_data = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Marks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        .card-container {
            padding: 20px;
            border: 1px solid #cbcbcb;
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .heading {
            color: #0a4a91;
            font-weight: 700;
            margin-bottom: 20px;
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
                        <div class="card-container">
                            <h1 class="heading">View Marks</h1>

                            <table class="table table-striped table-hover mt-3 align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">S.No</th>
                                        <th scope="col">Form Title</th>
                                        <th scope="col">Project Title</th>
                                        <th scope="col">Student Username</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Max Marks</th>
                                        <th scope="col">Marks Obtained</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($marks_data)): ?>
                                        <?php foreach ($marks_data as $index => $data): ?>
                                            <tr>
                                                <th scope="row"><?php echo $index + 1; ?></th>
                                                <td><?php echo htmlspecialchars($data['form_title']); ?></td>
                                                <td><?php echo htmlspecialchars($data['project_title']); ?></td>
                                                <td><?php echo htmlspecialchars($data['student_username']); ?></td>
                                                <td><?php echo htmlspecialchars($data['description']); ?></td>
                                                <td><?php echo htmlspecialchars($data['max_marks']); ?></td>
                                                <td><?php echo htmlspecialchars($data['marks']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7">No marks data found.</td>
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
</body>
</html>
