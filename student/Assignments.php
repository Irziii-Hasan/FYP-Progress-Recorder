<?php
include 'session_student.php';

$student_id = $_SESSION['student_id']; // Get the logged-in student's ID

include 'config.php';
// Get the project ID the student is enrolled in
$sql_project = "SELECT id, project_id, title FROM projects 
                WHERE student1 = '$student_id' 
                OR student2 = '$student_id' 
                OR student3 = '$student_id' 
                OR student4 = '$student_id'";
$result_project = mysqli_query($conn, $sql_project);
$project = mysqli_fetch_assoc($result_project);
$project_id = $project['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assignments</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
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
        } {
            height: 100vh; /* Full viewport height */
            overflow-y: auto; /* Add vertical scroll */
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
        .text-danger {
            color: #dc3545 !important;
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
            <li class="breadcrumb-item active" aria-current="page">View Assignments</li>
          </ol>
        </nav>

        <div class="container mt-5">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="heading">Assignments</h1>
          </div>
          <div class="table-container">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Assignment Name</th>
                  <th>Deadline</th>
                  <th>Time Left</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="myTable">
                <?php
                  // Fetch all assignments
                  $sql_assignments = "SELECT a.*, s.id AS submission_id, s.submission_path FROM assignments a 
                                      LEFT JOIN submission s ON a.id = s.assignment_id AND s.project_id = '$project_id'";
                  $result_assignments = mysqli_query($conn, $sql_assignments);

                  if (mysqli_num_rows($result_assignments) > 0) {
                      while ($row = mysqli_fetch_assoc($result_assignments)) {
                          $assignment_id = $row['id'];
                          $submitted = $row['submission_id'] ? true : false;
                          $deadline = $row['deadline'];
                          $deadline_time = strtotime($deadline);
                          $current_time = time();
                          $time_difference = $deadline_time - $current_time;

                          if ($time_difference > 0) {
                              $days_left = floor($time_difference / (60 * 60 * 24));
                              $hours_left = floor(($time_difference % (60 * 60 * 24)) / (60 * 60));
                              $time_left = "$days_left days, $hours_left hours";
                              $deadline_class = "";
                          } elseif ($time_difference == 0) {
                              $time_left = "Deadline is now";
                              $deadline_class = "";
                          } else {
                              $time_left = "Past deadline";
                              $deadline_class = "text-danger";
                          }

                          echo "<tr>";
                          echo "<td><a href='../coordinator/{$row['document_path']}' target='_blank'>{$row['assignment_name']}</a></td>";
                          echo "<td class='$deadline_class'>" . date("F j, Y, g:i a", strtotime($deadline)) . "</td>";
                          echo "<td class='$deadline_class'>$time_left</td>";
                          echo "<td>" . ($submitted ? "<i class='fas fa-check-circle text-success'></i> Submitted" : "Not submitted") . "</td>";
                          if ($submitted) {
                              $submission_path = $row['submission_path'];
                              echo "<td><a href='$submission_path' class='btn btn-success btn-sm' target='_blank'>Preview</a></td>";
                          } else {
                              echo "<td><a href='submit.php?assignment_id=$assignment_id' class='btn btn-primary btn-sm'>Submit</a></td>";
                          }
                          echo "</tr>";
                      }
                  } else {
                      echo "<tr><td colspan='5' class='text-center'>No assignments available.</td></tr>";
                  }
                  mysqli_close($conn);
                ?>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
