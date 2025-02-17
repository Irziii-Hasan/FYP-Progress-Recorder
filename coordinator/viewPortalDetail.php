<?php

include 'session_coordinator.php';
include 'config.php'; // Include your database connection settings

// Get the assignment ID from the URL
$assignment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($assignment_id == 0) {
    echo "Invalid Assignment ID.";
    exit;
}

// Fetch the assignment details
$sql_assignment = "SELECT * FROM assignments WHERE id = ?";
$stmt_assignment = $conn->prepare($sql_assignment);
$stmt_assignment->bind_param("i", $assignment_id);
$stmt_assignment->execute();
$result_assignment = $stmt_assignment->get_result();
$assignment = $result_assignment->fetch_assoc();

if (!$assignment) {
    echo "Assignment not found.";
    exit;
}

// Variable to store success message
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['marks'] as $submission_id => $marks) {
        $marks = intval($marks);
        // Ensure marks are within the allowed range (0 to 5)
        if ($marks < 0) $marks = 0;
        if ($marks > 5) $marks = 5;
        
        $sql_update_marks = "UPDATE submission SET marks = ? WHERE id = ?";
        $stmt_update_marks = $conn->prepare($sql_update_marks);
        $stmt_update_marks->bind_param("ii", $marks, $submission_id);
        $stmt_update_marks->execute();
    }
    $success_message = 'Marks updated successfully.';
}

// Fetch the project submissions for this assignment
$sql_submissions = "SELECT s.*, p.title as project_title, p.student1, p.student2, p.student3, p.student4 
                    FROM submission s
                    JOIN projects p ON s.project_id = p.id
                    WHERE s.assignment_id = ?";
$stmt_submissions = $conn->prepare($sql_submissions);
$stmt_submissions->bind_param("i", $assignment_id);
$stmt_submissions->execute();
$result_submissions = $stmt_submissions->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FYP | Submission Portal Details</title>
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
    .btn-back {
      float: right;
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
            <li class="breadcrumb-item"><a href="viewPortal.php">Assignments</a></li>
            <li class="breadcrumb-item active" aria-current="page">View Submitted Assignments</li>
          </ol>
        </nav>

        <!-- Page Heading -->
        <div class="container mt-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="heading">View Submitted Assignments</h2>
          </div>
          <div class="table-container mt-4">
            <h3><?php echo htmlspecialchars($assignment['assignment_name']); ?></h3>
            <p><strong>Deadline:</strong> <?php echo htmlspecialchars($assignment['deadline']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($assignment['description'])); ?></p>
            <form method="post" action="">
              <?php if ($result_submissions->num_rows > 0): ?>
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">S.No.</th>
                      <th scope="col">Project Name</th>
                      <th scope="col">Submitted File</th>
                      <th scope="col">Status</th>
                      <th scope="col">Marks (0-5)</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $count = 1;
                    while ($row = $result_submissions->fetch_assoc()):
                      $file_path = htmlspecialchars($row['submission_path']);
                      $marks = htmlspecialchars($row['marks']);
                    ?>
                      <tr>
                        <th scope="row"><?php echo $count++; ?></th>
                        <td><?php echo htmlspecialchars($row['project_title']); ?></td>
                        <td>
                          <?php if (file_exists($file_path)): ?>
                            <a href="<?php echo htmlspecialchars($file_path); ?>" target="_blank">View File</a>
                          <?php else: ?>
                            File not available
                          <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                          <input type="number" name="marks[<?php echo $row['id']; ?>]" min="0" max="5" value="<?php echo $marks; ?>" class="form-control">
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Update Marks</button>
              <?php else: ?>
                <div class="alert alert-info" role="alert">
                  No submissions found for this portal.
                </div>
              <?php endif; ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$conn->close();

// Echo JavaScript alert if there's a success message
if ($success_message) {
    echo "<script>alert('$success_message');</script>";
}
?>
</body>
</html>
