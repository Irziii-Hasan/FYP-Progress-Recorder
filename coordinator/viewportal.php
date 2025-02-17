<?php
include 'session_coordinator.php';
include 'config.php'; // Include your database connection settings

// Handle deletion if the 'delete' parameter is set
if (isset($_GET['delete'])) {
    $assignmentId = $_GET['delete'];

    // Prepare and execute the delete query
    $deleteSql = "DELETE FROM assignments WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $assignmentId);
    if ($stmt->execute()) {
        echo "<script>alert('Assignment deleted successfully'); window.location.href='viewportal.php';</script>";
    } else {
        echo "<script>alert('Error deleting assignment'); window.location.href='viewportal.php';</script>";
    }
    $stmt->close();
}

// Fetch the course durations for the filter
$courseDurationsSql = "SELECT id, title FROM course_durations";
$courseDurationsResult = $conn->query($courseDurationsSql);

// Fetch the assignments, with optional filter by course duration
$sql = "SELECT * FROM assignments"; // Base query

if (isset($_POST['course_title']) && $_POST['course_title'] != "") {
    $courseTitle = $_POST['course_title'];
    $sql = "SELECT * FROM assignments WHERE assignment_name = ?"; // Adjust this based on your schema
}

$stmt = $conn->prepare($sql);

if (isset($courseTitle) && $courseTitle != "") {
    $stmt->bind_param("s", $courseTitle); // Binding the course title parameter
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FYP | View Submission Portals</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
  <!-- Custom styles -->
  <link rel="stylesheet" href="style.css">
  <style>
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
    .btn-add {
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
            <li class="breadcrumb-item active" aria-current="page">Assignments</li>
          </ol>
        </nav>

        <!-- Page Heading -->
        <div class="container mt-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="heading">View Assignments</h2>
            <a href="createSubmission.php" class="btn btn-primary btn-add">Add Assignment</a>
          </div>
          <div class="col-md-3">
            <!-- Filter by Course Duration -->
            <form method="POST" action="viewportal.php">
              <div class="mb-3">
                <label for="course_title" class="form-label">Filter by Course Duration</label>
                <select name="course_title" class="form-select" onchange="this.form.submit()">
                  <option value="">Select Course Duration</option>
                  <?php while ($row = $courseDurationsResult->fetch_assoc()): ?>
                    <option value="<?php echo $row['title']; ?>" <?php echo (isset($courseTitle) && $courseTitle == $row['title']) ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($row['title']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
            </form>
          </div>

          <div class="table-container mt-4">
            <?php if ($result->num_rows > 0): ?>
              <table class="table table-bordered table-striped">                    <thead>
                  <tr>
                    <th scope="col">S.No.</th>
                    <th scope="col">Portal Name</th>
                    <th scope="col">Deadline</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $count = 1;
                  while ($row = $result->fetch_assoc()):
                    // Format the deadline date to a standard format
                    $formattedDate = date("d-m-y", strtotime($row['deadline']));
                  ?>
                    <tr>
                      <th scope="row"><?php echo $count++; ?></th>
                      <td><?php echo htmlspecialchars($row['assignment_name']); ?></td>
                      <td><?php echo $formattedDate; ?></td>
                      <td>
                        <a href="viewPortalDetail.php?id=<?php echo $row['id']; ?>" class="btn btn-info">View Details</a>
                        <a href="viewportal.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
                
            <?php else: ?>
              <div class="alert alert-info" role="alert">
                No submission portals found.
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$conn->close();
?>
</body>
</html>
