<?php
include 'progressphp.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
      .chart-container {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: space-evenly;
    }
    .chart-card {
      flex: 1 1 calc(33.333% - 1rem); /* 3 charts in a row */
      min-width: 250px;
      max-width: 400px;
      text-align: center;
    }
    canvas {
      max-width: 80%;
      height: auto !important;
    }
        .heading {
            color: #0a4a91;
            font-weight: 700;
        }
        .container {
            margin-top: 20px;
        }
        .video-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            background: #000;
        }
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .description {
            height: 1.6rem; /* Adjust based on your design */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            transition: height 0.3s ease;
        }
        .description.expanded {
            height: auto;
            white-space: normal;
        }
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .toggle-description {
            cursor: pointer;
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
                        <li class="breadcrumb-item active" aria-current="page">Project Progress</li>
                    </ol>
                </nav>

                <div class="container mt-5">
                    <!-- Project Dropdown -->
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5>Select Project</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label for="project" class="form-label">Choose a Project</label>
                                    <select class="form-select" id="project" name="id" required>
                                        <option value="">Select a project</option>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            // Output each project as an option in the dropdown
                                            while($row = $result->fetch_assoc()) {
                                                // Retain selected project after form submission
                                                $selected = (isset($selected_project_id) && $selected_project_id == $row['id']) ? 'selected' : '';
                                                echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['title']) . "</option>";
                                            }
                                        } else {
                                            echo "<option>No projects available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>

                    <!-- Project Overview -->
                    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($project_details)) { ?>
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5>Project Overview</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Project ID:</strong> <?php echo htmlspecialchars($project_details['project_id']); ?></p>
                            <p><strong>Project Title:</strong> <?php echo htmlspecialchars($project_details['title']); ?></p>
                            <p><strong>Project Description:</strong> <?php echo htmlspecialchars($project_details['description']); ?></p>
                            <div class="row">
    <div class="col-md-4">
        <p><strong>Team Members:</strong></p>
        <ul>
            <li><strong>Student 1:</strong> <?php echo htmlspecialchars($project_details['student1']); ?></li>
            <li><strong>Student 2:</strong> <?php echo htmlspecialchars($project_details['student2']); ?></li>
            <li><strong>Student 3:</strong> <?php echo htmlspecialchars($project_details['student3']); ?></li>
            <li><strong>Student 4:</strong> <?php echo htmlspecialchars($project_details['student4']); ?></li>
        </ul>
    </div>
    <div class="col-md-4">
                                    <p><strong>Supervisors:</strong></p>
                                    <ul>
                                        <li><strong>Supervisor:</strong> <?php echo htmlspecialchars($project_details['supervisor']); ?></li>
                                        <li><strong>Co-Supervisor:</strong> 
                                            <?php echo (!empty($project_details['co_supervisor']) && $project_details['co_supervisor'] != '0') ? htmlspecialchars($project_details['co_supervisor']) : 'N/A'; ?>
                                        </li>
                                        <!--  -->
                                    </ul>
                                </div>
    <div class="col-md-4">
        <p><strong>Overall Progress:</strong></p>
        <canvas id="overallProgressChart" width="400" height="400"></canvas>
    </div>
</div>

                        </div>
                    </div>

                    <!-- Presentation Progress -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h5>Presentation Progress</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Evaluators</th>
                                        <th>Total Marks</th>
                                        <th>Obtained Marks</th>
                                        <th>Feedback</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $presentation_progress_rows; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                 


          <!-- Assignment Progress -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-white">
                            <h5>Assignment Progress</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Total Assignments:</strong> <?php echo htmlspecialchars($total_assignments); ?></p>
                            <p><strong>Completed:</strong> <?php echo htmlspecialchars($completed_assignments); ?></p>
                            <p><strong>Pending:</strong> <?php echo htmlspecialchars($pending_assignments); ?></p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($total_assignments > 0) ? (100 * $completed_assignments / $total_assignments) : 0; ?>%;" aria-valuenow="<?php echo ($total_assignments > 0) ? (100 * $completed_assignments / $total_assignments) : 0; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo ($total_assignments > 0) ? number_format((100 * $completed_assignments / $total_assignments), 2) : 0; ?>% Completed
                                </div>
                            </div>
                        </div>
                    </div>
                  
                    <!-- Meeting Progress -->
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white">
                            <h5>Meeting Progress</h5>
                        </div>
                        <div class="card-body">
                            <!-- Meetings Table -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Meeting Title</th>
                                        <th>Feedback</th>
                                        <th>Attendance Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php echo $meeting_rows; ?>
                                </tbody>
                            </table>

                            <p><strong>Total Meetings:</strong> <?php echo htmlspecialchars($total_meetings); ?></p>
                            <p><strong>Meetings Attended:</strong> <?php echo htmlspecialchars($attended_meetings); ?></p>
                            <p><strong>Meetings Not Attended:</strong> <?php echo htmlspecialchars($not_attended_meetings); ?></p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo ($total_meetings > 0) ? (100 * $attended_meetings / $total_meetings) : 0; ?>%;" aria-valuenow="<?php echo ($total_meetings > 0) ? (100 * $attended_meetings / $total_meetings) : 0; ?>" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo ($total_meetings > 0) ? number_format((100 * $attended_meetings / $total_meetings), 2) : 0; ?>% Attended
                                </div>
                            </div>
                        </div>
                    </div>   
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-description').forEach(function(button) {
            button.addEventListener('click', function() {
                var target = document.querySelector(this.getAttribute('data-target'));
                target.classList.toggle('expanded');
                this.innerHTML = target.classList.contains('expanded') ? '<i class="bi bi-dash"></i>' : '<i class="bi bi-three-dots"></i>';
            });
        });

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($project_details)) { ?>
        // Initialize the Chart.js doughnut chart
        const ctx = document.getElementById('overallProgressChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Meetings (<?php echo number_format($meetings_progress, 2); ?>%)', 'Assignments (<?php echo number_format($assignments_progress, 2); ?>%)', 'Presentations (<?php echo number_format($presentation_progress, 2); ?>%)'],
                datasets: [{
                    data: [
                        <?php echo number_format($meetings_progress, 2); ?>, 
                        <?php echo number_format($assignments_progress, 2); ?>, 
                        <?php echo number_format($presentation_progress, 2); ?>
                    ],
                    backgroundColor: ['#007bff', '#ffc107', '#28a745'],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + '%';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
        <?php } ?>
    });
</script>

</body>
</html>
