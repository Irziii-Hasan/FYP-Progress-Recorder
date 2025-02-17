<?php
include 'session_supervisor.php'; // Ensure this is after session_start()

// Check if faculty_id is set
if (!isset($_SESSION['faculty_id']) || !is_numeric($_SESSION['faculty_id'])) {
    die('Faculty ID is not set or is invalid.');
}

$faculty_id = $_SESSION['faculty_id'];
include "config.php";

try {
    // Prepare SQL statement to fetch projects for the supervisor or co-supervisor
    $sql = "SELECT * FROM projects WHERE supervisor = ? OR co_supervisor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $faculty_id, $faculty_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $projects = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $projects[] = $row;
        }
    }

    // Fetch names for students, co-supervisors, and supervisors
    $names = [];
    $student_ids = [];
    $faculty_ids = [];
    foreach ($projects as $project) {
        $student_ids = array_merge($student_ids, [
            $project['student1'], $project['student2'], $project['student3'], $project['student4']
        ]);
        $faculty_ids[] = $project['supervisor'];
        $faculty_ids[] = $project['co_supervisor'];
    }

    // Fetch student names
    if (!empty($student_ids)) {
        $student_ids = array_unique($student_ids);
        $student_ids_list = implode(',', array_filter($student_ids));
        if (!empty($student_ids_list)) {
            $sql = "SELECT student_id, username FROM student WHERE student_id IN ($student_ids_list)";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $names[$row['student_id']] = $row['username'];
                }
            }
        }
    }

    // Fetch faculty names (including supervisor and co-supervisor)
    if (!empty($faculty_ids)) {
        $faculty_ids = array_unique($faculty_ids);
        $faculty_ids_list = implode(',', array_filter($faculty_ids));
        if (!empty($faculty_ids_list)) {
            $sql = "SELECT faculty_id, username FROM faculty WHERE faculty_id IN ($faculty_ids_list)";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $names[$row['faculty_id']] = $row['username'];
                }
            }
        }
    }
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
} finally {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JUW - FYP Progress Recorder | View Projects</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link rel="stylesheet" href="style.css">
    <style>
        .nav-tabs {
            margin-bottom: 20px;
        }
        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: .25rem;
            border-top-right-radius: .25rem;
            padding: 10px;
            color: white; /* Font color */
            text-align: center; /* Center text in the tab */
            flex: 1; /* Make tabs flexible to take equal space */
            background-color: #000d30; /* Background color */
        }
        .nav-tabs .nav-link.active {
            border-color: #dee2e6 #dee2e6 #fff;
            background-color: #051747; /* Active tab background color */
            color: white; /* Active tab font color */
        }
        .nav-tabs .nav-link:not(.active) {
            background-color: #051747; /* Inactive tab background color */
        }
        .tab-content {
            border: 1px solid #dee2e6;
            border-top: 0;
            padding: 20px;
        }
        .tab-pane {
            padding: 20px;
            border: 3px solid #dee2e6;
            border-top: 0;
            background-color: #f8f9fa;
        }

        .nav-tabs .nav-item {
            margin: 1.5px;
        }
        .heading {
            color: #0a4a91;
            font-weight: 700;
            padding: 14px;
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
                        <li class="breadcrumb-item active" aria-current="page">View Projects</li>
                    </ol>
                </nav>
                <h1 class="heading" style="text-align: center;">Projects</h1>

                <ul class="nav nav-tabs" id="projectTabs" role="tablist">
                    <?php foreach ($projects as $index => $project): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" id="tab-<?php echo $project['id']; ?>" data-bs-toggle="tab" data-bs-target="#project-<?php echo $project['id']; ?>" type="button" role="tab" aria-controls="project-<?php echo $project['id']; ?>" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                <?php echo htmlspecialchars($project['title']); ?>
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="tab-content" id="projectTabsContent">
                    <?php foreach ($projects as $index => $project): ?>
                        <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" id="project-<?php echo $project['id']; ?>" role="tabpanel" aria-labelledby="tab-<?php echo $project['id']; ?>">
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <p><?php echo htmlspecialchars($project['description']); ?></p>
                            <p><strong>Students:</strong> 
                                <?php echo isset($names[$project['student1']]) ? htmlspecialchars($names[$project['student1']]) : 'N/A'; ?>,
                                <?php echo isset($names[$project['student2']]) ? htmlspecialchars($names[$project['student2']]) : 'N/A'; ?>,
                                <?php echo isset($names[$project['student3']]) ? htmlspecialchars($names[$project['student3']]) : 'N/A'; ?>,
                                <?php echo isset($names[$project['student4']]) ? htmlspecialchars($names[$project['student4']]) : 'N/A'; ?>
                            </p>
                            <p><strong>Supervisor:</strong> <?php echo isset($names[$project['supervisor']]) ? htmlspecialchars($names[$project['supervisor']]) : 'N/A'; ?></p>
                            <p><strong>Co-Supervisor:</strong> <?php echo isset($names[$project['co_supervisor']]) ? htmlspecialchars($names[$project['co_supervisor']]) : 'N/A'; ?></p>
                            <p><strong>External Supervisor:</strong> 
    <?php 
        echo (!empty($project['external_supervisor']) && $project['external_supervisor'] != '0') 
            ? htmlspecialchars($project['external_supervisor']) 
            : 'N/A'; 
    ?>
</p>
                            <p><strong>Created At:</strong> <?php echo date('d-m-Y', strtotime($project['created_at'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
</body>
</html>
