<?php
include 'session_coordinator.php';
include 'config.php';

$serialNumber = 1; // Initialize the serial number

// Fetch durations for filtering
$durationOptions = "";
$sql = "SELECT id, title FROM course_durations";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $selected = (isset($_GET['duration_id']) && $_GET['duration_id'] == $row['id']) ? "selected" : "";
        $durationOptions .= "<option value='{$row['id']}' $selected>{$row['title']}</option>";
    }
}

// Fetch batches for filtering
$batchOptions = "";
$sql = "SELECT batchID, BatchName FROM batches";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $selected = (isset($_GET['batch_id']) && $_GET['batch_id'] == $row['batchID']) ? "selected" : "";
        $batchOptions .= "<option value='{$row['batchID']}' $selected>{$row['BatchName']}</option>";
    }
}

// Fetch project details based on selected duration and batch
$projectDetails = "";
$conditions = [];
$params = [];
$types = "";

if (isset($_GET['duration_id']) && !empty($_GET['duration_id'])) {
    $conditions[] = "p.duration = ?";
    $params[] = intval($_GET['duration_id']);
    $types .= "i";
}

if (isset($_GET['batch_id']) && !empty($_GET['batch_id'])) {
    $conditions[] = "p.batch = ?";
    $params[] = intval($_GET['batch_id']);
    $types .= "i";
}

if (!empty($conditions)) {
    $sql = "
        SELECT p.project_id, p.title, p.description, 
               s1.username AS student1, s2.username AS student2, 
               s3.username AS student3, s4.username AS student4, 
               f.username AS supervisor, c.username AS co_supervisor, 
               p.external_supervisor AS external_supervisor, b.BatchName AS batch 
        FROM projects p
        LEFT JOIN student s1 ON p.student1 = s1.student_id
        LEFT JOIN student s2 ON p.student2 = s2.student_id
        LEFT JOIN student s3 ON p.student3 = s3.student_id
        LEFT JOIN student s4 ON p.student4 = s4.student_id
        LEFT JOIN faculty f ON p.supervisor = f.faculty_id
        LEFT JOIN faculty c ON p.co_supervisor = c.faculty_id
        LEFT JOIN batches b ON p.batch = b.batchID
        WHERE " . implode(" AND ", $conditions);
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $externalSupervisor = (!empty($row['external_supervisor']) && $row['external_supervisor'] !== "0") ? $row['external_supervisor'] : "N/A";

            $projectDetails .= "
                <tr>
                    <td>{$serialNumber}</td>
                    <td>{$row['project_id']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['student1']}</td>
                    <td>{$row['student2']}</td>
                    <td>{$row['student3']}</td>
                    <td>{$row['student4']}</td>
                    <td>{$row['supervisor']}</td>
                    <td>{$row['co_supervisor']}</td>
                    <td>{$externalSupervisor}</td>
                    <td>{$row['batch']}</td>
                </tr>
            ";

            $serialNumber += 1;
        }
    } else {
        $projectDetails = "<tr><td colspan='12' class='text-center'>No projects found for the selected filters</td></tr>";
    }
    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>FYP | Project Details</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
        .filter-form {
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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">JUW - FYP Progress Recorder</a></li>
                    <li class="breadcrumb-item"><a href="project.php">Projects</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Projects Details</li>
                    </ol>
                </nav>
                <div class="container-fluid mt-4 p-4">
                    <h2 class="heading">Project Details</h2>
                    <div class="container">
                        <form method="GET" class="filter-form">
                            <div class="row d-flex justify-content-end">
                                <div class="col-md-2">
                                    <label for="duration_id">Filter by FYP Duration:</label>
                                    <select name="duration_id" id="duration_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Select Duration</option>
                                        <?php echo $durationOptions; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="batch_id">Filter by Batch:</label>
                                    <select name="batch_id" id="batch_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">Select Batch</option>
                                        <?php echo $batchOptions; ?>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                


                    <div class="table-container table-responsive">
                        <table class="table  table-striped table-bordered mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th>S. No.</th>
                                    <th>Project ID</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Student 1</th>
                                    <th>Student 2</th>
                                    <th>Student 3</th>
                                    <th>Student 4</th>
                                    <th>Supervisor</th>
                                    <th>Co-Supervisor</th>
                                    <th>External Supervisor</th>
                                    <th>Batch</th>
                                </tr>
                            </thead>
                            <tbody class="table-sm">
                                <?php echo $projectDetails; ?>
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
